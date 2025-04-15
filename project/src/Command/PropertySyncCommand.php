<?php

namespace App\Command;

use App\Traits\HumanSizeCounterTrait;
use App\Service\Command\SyncData\PropertySync;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use App\Service\Command\SyncData\SyncEntityService;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:sync-property', description: 'Обновление свойств.')]
class PropertySyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '256M');

        $start = self::getScriptStartTime();
        $io = new SymfonyStyle($input, $output);
        $io->title('Обновляем свойства.');

        $count = SyncEntityService::update(new PropertySync());

        $io->success(sprintf(
                'Свойства: %s. Память:%s. Время: %s.',
                $count,
                self::humanizeUsageMemory(true),
                self::getExecutionTime($start)
            )
        );

        return Command::SUCCESS;
    }
}
