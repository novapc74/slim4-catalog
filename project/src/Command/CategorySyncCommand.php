<?php

namespace App\Command;

use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use App\Service\Command\CategorySync\CategoryUpdate;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:sync-categories', description: 'Обновление категорий.')]
class CategorySyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = self::getScriptStartTime();
        $io = new SymfonyStyle($input, $output);
        $io->title('Обновляем категории');

        $result = CategoryUpdate::execute();

        if ($result['errors'] ?? null) {
            $io->error(implode(PHP_EOL, $result['errors']));

            return Command::FAILURE;
        }

        $io->success(sprintf(
            'Новых: %d, Всего: %d, Память: %s. Время: %s',
            array_shift($result),
            array_shift($result),
            self::humanizeUsageMemory(true),
            self::getExecutionTime($start)
        ));

        return Command::SUCCESS;
    }
}
