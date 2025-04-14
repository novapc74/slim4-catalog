<?php

namespace App\Service\Command\PriceSync;

use Generator;
use App\Models\Price;
use App\Models\Product;

class PriceSync
{
    private const FILE_PATH = __DIR__ . '/../../../../var/data/prices.json';

    public static function execute(): int
    {
        $i = 0;
        foreach (self::getCollection() as $item) {

            if (!$productId = $item['УникальныйИдентификатор'] ?? null) {
                continue;
            }

            if (!Product::where('id', $productId)->exists()) {
                continue;
            }

            $collection = [];
            foreach ($item['data'] as $priceItem) {
                if (!$priceData = PriceDto::new($productId, $priceItem)->execute()) {
                    continue;
                }

                $collection[] = $priceData;
                $i++;
            }

            $collection = array_filter($collection);

            Price::upsertPrice($collection);
        }

        return $i;
    }

    private static function getCollection(): Generator
    {
        foreach (self::getDataFromFile() ?? [] as $item) {
            yield $item;
        }
    }

    private static function getDataFromFile(): ?array
    {
        if (!file_exists(self::FILE_PATH)) {
            return null;
        }

        $oldData = file_get_contents(self::FILE_PATH);

        return json_decode($oldData, true);
    }
}
