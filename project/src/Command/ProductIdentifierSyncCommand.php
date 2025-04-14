<?php

namespace App\Command;

use App\Models\ProductIdentifier;
use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Command\Command;
use App\Service\Command\ProductSync\ProductDto;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductIdentifierSyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function configure(): void
    {
        parent::configure();

        $this->setName('app:sync-product-identifier');
        $this->setDescription('Создание / обновление идентификаторов товаров.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', -1);
        $io = new SymfonyStyle($input, $output);

        $io->title('Создаем, обновляем идентификаторы товаров.');

        $data = file_get_contents(__DIR__ . '/../../var/data/products.json');
        $data = json_decode($data, true);

        $collection = [];
        $resolvedCollection = [];
        foreach ($data as $item) {
            $productIdentifier = ProductDto::new($item)->getProductIdentifier();

            if (in_array($productIdentifier['shop_code'], $resolvedCollection)) {
                continue;
            }

            $resolvedCollection[] = $productIdentifier['shop_code'];
            $collection[] = $productIdentifier;
        }

        unset($resolvedCollection);

        [] === $collection ?: ProductIdentifier::upsertProductIdentifier($collection);

        $io->success(sprintf(
                'Обновили/добавили идентификаторов товара в количестве - %s. Память - %s',
                count($collection),
                self::humanizeUsageMemory())
        );

        return Command::SUCCESS;
    }
}
