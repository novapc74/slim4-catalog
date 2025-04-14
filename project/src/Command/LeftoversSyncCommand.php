<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\Leftover;
use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\Command\LeftoversSync\LeftoversUpdate;

#[AsCommand(name: 'app:sync-leftovers', description: 'Обновление остатков товаров по городам.')]
class LeftoversSyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', -1);

        $io = new SymfonyStyle($input, $output);
        $io->title('Обновление цен товаров.');

        Leftover::truncateLeftovers();

        $i = LeftoversUpdate::execute();

        $io->success(sprintf(
                'Обновили остатки в количестве: %s. Память: %s',
                $i,
                self::humanizeUsageMemory())
        );

        return Command::SUCCESS;
    }
}
