<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\ProductProperty;
use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\Command\ProductPropertySync\ProductPropertyUpdate;

#[AsCommand(name: 'app:sync-product-property', description: 'Обновление свойств товаров.')]
class ProductPropertySyncCommand extends Command
{
    use HumanSizeCounterTrait;
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', -1);
        $io = new SymfonyStyle($input, $output);
        $io->title('Обновляем свойства товаров.');

        ProductProperty::truncate();

        $i = ProductPropertyUpdate::execute();

        $io->success(sprintf('Обновили свойств: %s. Память: %s', $i , self::humanizeUsageMemory()));

        return Command::SUCCESS;
    }
}
