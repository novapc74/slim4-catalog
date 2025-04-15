<?php

namespace App\Command;

use App\Service\Command\SyncData\BrandSync;
use App\Service\Command\SyncEntityService;
use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:sync-brands', description: 'Обновление брендов.')]
class BrandSyncCommand extends Command
{
    use HumanSizeCounterTrait;
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '256M');
        $start = self::getScriptStartTime();
        $io = new SymfonyStyle($input, $output);
        $io->title('Создаем, обновляем бренды товаров.');

        $count = SyncEntityService::update(new BrandSync());

        $io->success(sprintf(
                'Брендов: %s. Память:%s. Время: %s.',
                $count,
                self::humanizeUsageMemory(true),
                self::getExecutionTime($start)
            )
        );

        return Command::SUCCESS;
    }
}
