<?php

namespace App\Command;

use App\Models\Leftover;
use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Command\Command;
use App\Service\Command\SyncData\LeftoversSync;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use App\Service\Command\SyncData\SyncEntityService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:sync-leftovers', description: 'Обновление остатков товаров по городам.')]
class LeftoversSyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = self::getScriptStartTime();
        $io = new SymfonyStyle($input, $output);
        $io->title('Обновление цен товаров.');

        Leftover::truncateLeftovers();

        $count = SyncEntityService::update(new LeftoversSync());

        $io->success(sprintf(
                'Остатки: %s. Память: %s. Время: %s',
                $count,
                self::humanizeUsageMemory(true),
                self::getExecutionTime($start)
            )
        );

        return Command::SUCCESS;
    }
}
