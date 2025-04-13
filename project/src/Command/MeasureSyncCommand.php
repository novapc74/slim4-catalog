<?php

namespace App\Command;

use App\Models\Measure;
use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Console\Command\Command;
use App\Service\Command\ProductSync\ProductDto;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MeasureSyncCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function configure(): void
    {
        parent::configure();

        $this->setName('app:sync-measure');
        $this->setDescription('Создание / обновление мер свойств из файла товаров.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', -1);
        $io = new SymfonyStyle($input, $output);

        $io->title('Создаем, обновляем меры свойств.');

        $data = file_get_contents(__DIR__ . '/../../var/data/products.json');
        $collection = json_decode($data, true);

        $measureCollection = [];
        $resolvedMeasures = [];
        foreach ($collection as $item) {
            foreach (ProductDto::new($item)->getMeasures() ?? [] as $measure) {

                $measure = self::sanitizeMeasure($measure);

                if (in_array($measure, $resolvedMeasures)) {
                    continue;
                }

                $resolvedMeasures[] = $measure;
                $measureCollection[] = ['title' => $measure];
            }
        }

        unset($resolvedMeasures);

        [] === $measureCollection ?: Measure::upsertMeasure($measureCollection);

        $io->success(sprintf(
                'Обновили/добавили мер в количестве - %s. Память - %s',
                count($measureCollection),
                self::humanizeUsageMemory())
        );

        return Command::SUCCESS;
    }

    private static function sanitizeMeasure(string $measure): string
    {
        return match ($measure) {
            '°C', '°С' => '°С',
            'A', 'А' => 'А',
            'В / Гц', 'В/Гц' => 'В/Гц',
            'шт', 'шт.' => 'шт',
            'ч', 'час' => 'час',
            'с', 'сек' => 'сек',
            'град', 'Градусы' => 'град',
            'Н*м', 'Н×м' => 'Н×м',
            default => $measure
        };
    }
}
