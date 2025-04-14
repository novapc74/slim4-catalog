<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\City;
use App\Models\Price;
use App\Models\Product;
use App\Models\PriceType;
use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:sync_price', description: 'Обновление цен товаров по городам и типам цен.')]
class PriceSyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', -1);
        $io = new SymfonyStyle($input, $output);
        $io->title('Обновление цен товаров.');

        $data = file_get_contents(__DIR__ . '/../../var/data/prices.json');
        $data = json_decode($data, true);

        $i = 0;
        foreach ($data as $item) {

            if (!$productId = $item['УникальныйИдентификатор'] ?? null) {
                continue;
            }

            if (!Product::where('id', $productId)->exists()) {
                continue;
            }

            $collection = [];

            foreach ($item['data'] as $priceItem) {

                if ($priceData = self::sanitizePrice($priceItem['ВидЦены'])) {
                    $priceData['product_id'] = $productId;
                    $priceData['value'] = $priceItem['Цена'] * 100;
                    $i++;
                    $collection[] = $priceData;
                }
            }

            Price::upsertPrice($collection);
        }

        $io->success(sprintf(
                'Обновили/добавили идентификаторов товара в количестве - %s. Память - %s',
                $i,
                self::humanizeUsageMemory())
        );

        return Command::SUCCESS;
    }

    private function sanitizePrice(string $price): ?array
    {
        return match ($price) {
            "СПБ Розница" => self::getPriceData('spb', 'retail'),
            "СПб Акция" => self::getPriceData('spb', 'action'),
            "СПБ Карта" => self::getPriceData('spb', 'promotion'),
            "СПБ Оптовая" => self::getPriceData('spb', 'opt'),
            "СПБ Стоп" => self::getPriceData('spb', 'stop'),
            "МСК Стоп" => self::getPriceData('msk', 'stop'),
            "МСК Оптовая" => self::getPriceData('msk', 'opt'),
            "МСК Акция" => self::getPriceData('msk', 'action'),
            "МСК Розница" => self::getPriceData('msk', 'retail'),
            "МСК Карта" => self::getPriceData('msk', 'promotion'),
            "РнД Стоп" => self::getPriceData('rnd', 'stop'),
            "РнД Оптовая" => self::getPriceData('rnd', 'opt'),
            "РнД Розница" => self::getPriceData('rnd', 'retail'),
            "РнД Карта" => self::getPriceData('rnd', 'promotion'),
            "РнД Акция" => self::getPriceData('rnd', 'action'),
            default => null,
        };
    }

    private function getPriceData(string $citySlug, string $priceTypeSlug): ?array
    {
        if (in_array($citySlug, ['spb', 'msk', 'rnd']) && in_array($priceTypeSlug, ['opt', 'stop', 'promotion', 'action', 'retail'])) {
            return [
                'city_id' => City::query()->where('slug', $citySlug)->value('id'),
                'price_type_id' => PriceType::query()->where('slug', $priceTypeSlug)->value('id'),
            ];
        }

        return null;
    }
}
