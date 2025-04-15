<?php

namespace App\Command;

use App\Models\ProductProperty;
use App\Traits\HumanSizeCounterTrait;
use App\Service\Command\SyncEntityService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\Command\SyncData\ProductPropertySync;

#[AsCommand(name: 'app:sync-product-property', description: 'Обновление свойств товаров.')]
class ProductPropertySyncCommand extends Command
{
    use HumanSizeCounterTrait;
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', -1);
        $start = self::getScriptStartTime();
        $io = new SymfonyStyle($input, $output);
        $io->title('Обновляем свойства товаров.');

        ProductProperty::truncateProductProperty();

        $count = SyncEntityService::update(new ProductPropertySync());

        $io->success(sprintf(
                'Товары: %s. Память: %s. Время: %s',
                $count,
                self::humanizeUsageMemory(true),
                self::getExecutionTime($start)
            )
        );

        return Command::SUCCESS;
    }
}
