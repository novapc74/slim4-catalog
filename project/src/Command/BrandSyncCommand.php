<?php

namespace App\Command;

use App\Models\Brand;
use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Command\Command;
use App\Service\Command\ProductSync\ProductDto;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BrandSyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function configure(): void
    {
        parent::configure();

        $this->setName('app:sync-brands');
        $this->setDescription('Создание / обновление брендов из файла товаров.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Создаем, обновляем бренды товаров.');

        $data = file_get_contents(__DIR__ . '/../../var/data/products.json');
        $collection = json_decode($data, true);

        $brandCollection = [];
        $resolvedBrands = [];
        foreach ($collection as $item) {

            if (!$brand = ProductDto::new($item)->getBrand()) {
                continue;
            }

            if (in_array($brand, $resolvedBrands)) {
                continue;
            }

            $resolvedBrands[] = $brand;
            $brandCollection[] = ['title' => $brand];
        }

        unset($resolvedBrands);

        [] === $brandCollection ?: Brand::upsertBrand($brandCollection);

        $io->success(sprintf(
                'Обновили/добавили брендов в количестве - %s. Память - %s',
                count($brandCollection),
                self::humanizeUsageMemory())
        );

        return Command::SUCCESS;
    }
}
