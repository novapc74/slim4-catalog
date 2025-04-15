<?php

namespace App\Command;

use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use App\Service\Command\SyncData\SyncEntityService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\Command\SyncData\ProductIdentifierSync;

#[AsCommand(name: 'app:sync-product-identifier', description: 'Обновление идентификаторов товаров.')]
class ProductIdentifierSyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '256M');

        $start = self::getScriptStartTime();
        $io = new SymfonyStyle($input, $output);
        $io->title('Создаем, обновляем идентификаторы товаров.');

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
