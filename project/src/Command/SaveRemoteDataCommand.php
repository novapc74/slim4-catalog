<?php

namespace App\Command;

use GuzzleHttp\Client;
use App\Traits\HumanSizeCounterTrait;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SaveRemoteDataCommand extends Command
{
    use HumanSizeCounterTrait;

    protected function configure(): void
    {
        parent::configure();

        $this->setName('app:remote-update-data');
        $this->setDescription('Категории, товары, цены, остатки.');
    }

    /**
     * @throws GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '512M');
        $start = self::getScriptStartTime();
        $io = new SymfonyStyle($input, $output);
        $io->title('Сохраняем на сервере данные для обновления категорий, товаров, цен и остатков.');

        $categoryData = $this->getRemoteData('/peckingorder');
        $this->saveCategories($categoryData);
        unset($categoryData);

        $productData = $this->getRemoteData('/categories_with_products_hex');
        $this->saveProducts($productData);
        unset($productData);

        $leftoversData = $this->getRemoteData('/remains');
        $this->saveLeftovers($leftoversData);
        unset($leftoversData);

        $priceData = $this->getRemoteData('/price');
        $this->savePrices($priceData);
        unset($priceData);

        $io->success(sprintf(
                'Сохранили категории, хеш-товары, остатки, цены. Память: %s, Время: %s',
                self::humanizeUsageMemory(),
                self::getExecutionTime($start))
        );

        return Command::SUCCESS;
    }

    public function savePrices(array $priceData): void
    {
        file_put_contents(__DIR__ . '/../../var/data/prices.json', json_encode($priceData['price'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function saveLeftovers(array $leftoversData): void
    {
        file_put_contents(__DIR__ . '/../../var/data/leftovers.json', json_encode($leftoversData['remains'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function saveCategories(array $categoryData): void
    {
        file_put_contents(__DIR__ . '/../../var/data/categories.json', json_encode($categoryData['peckingorder'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function saveProducts(array $productData): void
    {
        file_put_contents(__DIR__ . '/../../var/data/products.json', json_encode($productData['catalog'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /**
     * @throws GuzzleException
     */
    public function getRemoteData($rootUri): ?array
    {
        $client = new Client(['base_uri' => env('ROOT_SERVER') . $rootUri]);
        $response = $client->request('GET');
        $responseCode = $response->getStatusCode();
        if ($responseCode !== 200) {
            return null;
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
