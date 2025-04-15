<?php

namespace App\Service\Command\SyncData;

use App\Models\Property;
use App\Service\Command\Dto\ProductDto;
use App\Service\Command\Interface\SyncDatabaseInterface;

class PropertySync implements SyncDatabaseInterface
{

    public static function getEntityFqcn(): string
    {
        return Property::class;
    }

    public static function getFileName(): ?string
    {
        return file_exists($filename = __DIR__ . '/../../../../var/data/products.json')
            ? $filename
            : null;
    }

    public static function getEntityItem(array $data): ?array
    {
        $resolvedProperty = [];
            foreach (ProductDto::new($data)->getProperties() as $property) {
                if (in_array($property['title'], $resolvedProperty)) {
                    continue;
                }

                $collection[] = $property;
            }

        return $collection ?? null;
    }
}
