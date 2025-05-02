<?php

namespace App\Command;

use Generator;
use Throwable;
use App\Traits\RunCommandTrait;
use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:sync', description: 'Обновляем всю базу данных.')]
class UpdateDatabaseCommand extends Command
{
    use HumanSizeCounterTrait;
    use RunCommandTrait;

    private const MAPPING = [
        'Бренды' => 'app:sync-brands',
        'Категории' => 'app:sync-categories',
        'Свойства' => 'app:sync-property',
        'Идентификаторы товаров' => 'app:sync-product-identifier',
        'Товары' => 'app:sync-product',
        'Цены' => 'app:sync-price',
        'Свойства товаров' => 'app:sync-product-property',
        'Остатки' => 'app:sync-leftovers',
    ];

    /**
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        ini_set('memory_limit', '-1');

        $start = self::getScriptStartTime();
        $io = new SymfonyStyle($input, $output);
        $io->note('Обновляем базу данных');

        foreach (self::getGen() as $item) {
            self::runCommand($item['entityName'], $item['commandName'], $io);
        }

        $io->success(sprintf(
                'Память:%s. Время: %s.',
                self::humanizeUsageMemory(true),
                self::getExecutionTime($start)
            )
        );

        return Command::SUCCESS;
    }

    private static function getGen(): Generator
    {
        foreach (self::MAPPING as $entityName => $commandName) {
            yield compact('entityName', 'commandName');
        }
    }
}
