<?php

namespace App\Service\Command\ProductSync;


use App\Models\Product;

class ProductUpdate
{
    private const FILE_PATH = __DIR__ . '/../../../../var/data/products.json';

    public static function execute(): void
    {
        $productCollection = [];
        foreach (self::getCollection() as $item) {
            $productCollection[] = ProductDto::new($item)->execute();
        }

        Product::upsertProduct($productCollection);
    }

    private static function getCollection(): \Generator
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
