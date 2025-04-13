<?php

namespace App\Command;

use App\Models\Property;
use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Command\Command;
use App\Service\Command\ProductSync\ProductDto;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PropertySyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function configure(): void
    {
        parent::configure();

        $this->setName('app:sync-property');
        $this->setDescription('Создание / обновление свойств из файла товаров.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', -1);
        $io = new SymfonyStyle($input, $output);

        $io->title('Создаем, обновляем свойства.');

        $data = file_get_contents(__DIR__ . '/../../var/data/products.json');
        $collection = json_decode($data, true);

        $propertyCollection = [];
        $resolvedProperties = [];
        foreach ($collection as $item) {
            foreach (ProductDto::new($item)->getProperties() ?? [] as $property) {

                if (in_array($property['title'], $resolvedProperties)) {
                    continue;
                }

                $resolvedProperties[] = $property['title'];
                $propertyCollection[] = $property;
            }
        }

        unset($resolvedProperties);

        [] === $propertyCollection ?: Property::upsertProperty($propertyCollection);

        $io->success(sprintf(
                'Обновили/добавили свойств в количестве - %s. Память - %s',
                count($propertyCollection),
                self::humanizeUsageMemory())
        );

        return Command::SUCCESS;
    }
}
