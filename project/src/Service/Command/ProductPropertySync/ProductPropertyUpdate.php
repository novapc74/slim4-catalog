<?php

namespace App\Service\Command\ProductPropertySync;

use App\Models\Product;
use App\Models\ProductProperty;
use Generator;

class ProductPropertyUpdate
{
    private const FILE_PATH = __DIR__ . '/../../../../var/data/products.json';
    public static function execute(): int
    {
        $i = 0;
        foreach (self::getCollection() as $productData) {

            if (!$productId = Product::where('id', $productData['УникальныйИдентификатор'])?->value('id')) {
                continue;
            }

            $collection = [];
            foreach ($productData['ОсновныеЕдиницыИзмерения'] as $productItem) {

                if(!$productProperty = ProductPropertyDto::new($productId, $productItem)->execute()) {
                    continue;
                }

                $collection[] = $productProperty;
               $i++;
            }

            foreach ($productData['ДополнительныеРеквизиты'] as $productItem) {
                if(!$productProperty = ProductPropertyDto::new($productId, $productItem)->execute()) {
                    continue;
                }

                $collection[] = $productProperty;
                $i++;
            }

            ProductProperty::upsertProductProperty($collection);
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
