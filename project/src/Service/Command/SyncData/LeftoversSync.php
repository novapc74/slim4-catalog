<?php

namespace App\Service\Command\SyncData;

use App\Models\City;
use App\Models\Leftover;
use App\Models\Product;

class LeftoversSync implements SyncDatabaseInterface
{

    public static function getEntityFqcn(): string
    {
        return Leftover::class;
    }

    public static function getFileName(): ?string
    {
        return file_exists($filename = __DIR__ . '/../../../../var/data/leftovers.json')
            ? $filename
            : null;
    }

    public static function getEntityItem(array $data): ?array
    {
        foreach ($data['bases'] as $item) {
            $productId = $data['УникальныйИдентификатор'] ?? null;

            if ($productId && $productId = Product::query()->where('id', $productId)->value('id')) {
                $item['product_id'] = $productId;
                if ($priceData = self::getLeftoverData($item)) {
                    $collection[] = $priceData;
                }
            }
        }

        return $collection ?? null;
    }

    private static function getLeftoverData(array $data): ?array
    {
        if (!$cityId = self::getCityId($data['base'])) {
            return null;
        }

        return [
            'product_id' => $data['product_id'],
            'amount' => $data['quantity'],
            'city_id' => $cityId,
        ];
    }

    private static function getCityId(string $warehouse): ?int
    {
        return match ($warehouse) {
            "СПб, Репищева 14" => City::query()->where('slug', 'spb')?->value('id'),
            "Москва" => City::query()->where('slug', 'msk')?->value('id'),
            "Ростов" => City::query()->where('slug', 'rnd')?->value('id'),
            default => null,
        };
    }
}
