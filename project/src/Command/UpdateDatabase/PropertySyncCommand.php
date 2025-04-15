<?php

namespace App\Command\UpdateDatabase;

use App\Service\Command\SyncData\PropertySync;
use App\Service\Command\SyncEntityService;
use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:sync-property', description: 'Обновление свойств.')]
class PropertySyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', -1);

        $start = self::getScriptStartTime();
        $io = new SymfonyStyle($input, $output);
        $io->title('Свойства.');

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
