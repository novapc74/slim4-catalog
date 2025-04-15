<?php

namespace App\Service\Command\SyncData;

use App\Models\Product;
use App\Models\ProductProperty;
use App\Service\Command\Dto\ProductPropertyDto;
use App\Service\Command\Interface\SyncDatabaseInterface;

class ProductPropertySync implements SyncDatabaseInterface
{
    public static function getEntityFqcn(): string
    {
        return ProductProperty::class;
    }

    public static function getFileName(): ?string
    {
        return file_exists($filename = __DIR__ . '/../../../../var/data/products.json')
            ? $filename
            : null;
    }

    public static function getEntityItem(array $data): ?array
    {
        if (!$productId = Product::where('id', $data['УникальныйИдентификатор'])?->value('id')) {

            return null;
        }

        $collection = [];

        foreach ($data['ОсновныеЕдиницыИзмерения'] as $productItem) {
            if (!$productProperty = ProductPropertyDto::new($productId, $productItem)->execute()) {

                return null;
            }

            $collection[] = $productProperty;
        }

        foreach ($data['ДополнительныеРеквизиты'] as $productItem) {
            if (!$productProperty = ProductPropertyDto::new($productId, $productItem)->execute()) {

                return null;
            }

            $collection[] = $productProperty;
        }

        return $collection ?? null;
    }
}
