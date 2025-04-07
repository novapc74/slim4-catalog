<?php

namespace App\Command;

use App\Traits\HumanSizeCounterTrait;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\Command\CategoriesSyncCommandService;

class CategoriesSyncCommand extends Command
{
    use HumanSizeCounterTrait;
    protected function configure(): void
    {
        parent::configure();

        $this->setName('app:sync-categories');
        $this->setDescription('Синхронизация категорий с удаленным сервером.');
    }

    /**
     * @throws GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Обновляем категории');

        $result = CategoriesSyncCommandService::init()
            ->execute();

        if ($result['errors'] ?? null) {
            $io->error(implode(PHP_EOL, $result['errors']));

            return Command::FAILURE;
        }

        $io->success(sprintf('Создали/обновили категорий: %d, Память: %s', array_shift($result), self::humanizeUsageMemory()));

        return Command::SUCCESS;
    }
}
