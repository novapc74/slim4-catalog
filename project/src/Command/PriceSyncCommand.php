<?php

namespace App\Command;

use App\Models\Price;
use App\Traits\HumanSizeCounterTrait;
use App\Service\Command\SyncData\PriceSync;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use App\Service\Command\SyncData\SyncEntityService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:sync-price', description: 'Обновление цен товаров по городам и типам цен.')]
class PriceSyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '64M');

        $start = self::getScriptStartTime();
        $io = new SymfonyStyle($input, $output);
        $io->title('Обновление цен товаров.');

        Price::truncatePrice();

        $count = SyncEntityService::update(new PriceSync());

        $io->success(sprintf(
                'Цены: %s. Память: %s. Время: %s',
                $count,
                self::humanizeUsageMemory(true),
                self::getExecutionTime($start)
            )
        );

        return Command::SUCCESS;
    }
}
