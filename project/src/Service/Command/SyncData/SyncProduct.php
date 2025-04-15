<?php

namespace App\Service\Command\SyncData;

use App\Models\Product;
use App\Service\Command\Dto\ProductDto;
use App\Service\Command\Interface\SyncDatabaseInterface;

class SyncProduct implements SyncDatabaseInterface
{

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public static function getFileName(): ?string
    {
        return file_exists($filename = __DIR__ . '/../../../../var/data/products.json')
            ? $filename
            : null;
    }

    public static function getEntityItem(array $data): ?array
    {
        return [ProductDto::new($data)->execute()];
    }
}
