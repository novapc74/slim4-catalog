<?php

namespace App\Command;

use App\Models\Brand;
use App\Service\Command\ProductSync\ProductUpdate;
use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Command\Command;
use App\Service\Command\ProductSync\ProductDto;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductSyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function configure(): void
    {
        parent::configure();

        $this->setName('app:sync-product');
        $this->setDescription('Синхронизируем товары.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', -1);

        $start = self::getScriptStartTime();
        $io = new SymfonyStyle($input, $output);
        $io->title('Создаем, обновляем товары.');

        ProductUpdate::execute();

        $io->success(sprintf(
                'Сохранили категории, хеш-товары, остатки, цены. Память: %s, Время: %s',
                self::humanizeUsageMemory(),
                self::getExecutionTime($start))
        );

        return Command::SUCCESS;
    }
}
