<?php

namespace App\Command\UpdateDatabase;

use App\Service\Command\SyncData\ProductIdentifierSync;
use App\Service\Command\SyncEntityService;
use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:sync-product-identifier', description: 'Обновление идентификаторов товаров.')]
class ProductIdentifierSyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', -1);

        $start = self::getScriptStartTime();
        $io = new SymfonyStyle($input, $output);
        $io->title('Идентификаторы товаров.');

        $count = SyncEntityService::update(new ProductIdentifierSync());

        $io->success(sprintf(
                'Идентификаторы товара: %s. Память: %s. Время: %s',
                $count,
                self::humanizeUsageMemory(true),
                self::getExecutionTime($start)
            )
        );

        return Command::SUCCESS;
    }
}
