<?php

namespace App\Command;

use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use App\Service\Command\CategorySync\CategoryUpdate;
use Symfony\Component\Console\Output\OutputInterface;

class CategorySyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function configure(): void
    {
        parent::configure();

        $this->setName('app:sync-categories');
        $this->setDescription('Создание / обновление категорий из файла.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Обновляем категории');

        $result = CategoryUpdate::execute();

        if ($result['errors'] ?? null) {
            $io->error(implode(PHP_EOL, $result['errors']));

            return Command::FAILURE;
        }

        $io->success(sprintf(
            'Создали/обновили категорий: %d, Всего категорий: %d, Память: %s',
            array_shift($result),
            array_shift($result),
            self::humanizeUsageMemory()
        ));

        return Command::SUCCESS;
    }
}
