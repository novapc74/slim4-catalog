<?php

namespace App\Service\Command\SyncData;

use App\Models\City;
use App\Models\Price;
use App\Models\PriceType;
use App\Models\Product;

class PriceSync implements SyncDatabaseInterface
{
    public static function getEntityFqcn(): string
    {
        return Price::class;
    }

    public static function getFileName(): ?string
    {
        return file_exists($filename = __DIR__ . '/../../../../var/data/prices.json')
            ? $filename
            : null;
    }

    public static function getEntityItem(array $data): ?array
    {
        foreach ($data['data'] as $item) {
            $productId = $data['УникальныйИдентификатор'] ?? null;

            if ($productId && $productId = Product::query()->where('id', $productId)->value('id')) {
                $item['product_id'] = $productId;
                if ($priceData = self::getPriceData($item)) {
                    $collection[] = $priceData;
                }
            }
        }

        return $collection ?? null;
    }

    private static function getCitySlug(string $cityName): ?string
    {
        return match ($cityName) {
            'СПБ', 'СПб' => 'spb',
            'МСК' => 'msk',
            'РнД' => 'rnd',
            default => null,
        };
    }

    private static function getPriceTypeSlug(string $priceTypeName): ?string
    {
        return match ($priceTypeName) {
            'Розница' => 'retail',
            'Акция' => 'action',
            'Карта' => 'promotion',
            'Оптовая' => 'opt',
            'Стоп' => 'stop',
            default => null,
        };
    }

    private static function denormalizePriceItem(array $item): ?array
    {
        $data = explode(' ', $item['ВидЦены']);

        $cityName = $data[0];

        if(!$priceTypeName = $data[1] ?? null) {
            return null;
        }

        return [self::getCitySlug($cityName), self::getPriceTypeSlug($priceTypeName)];
    }

    private static function getPriceData(array $item): ?array
    {
        [$citySlug, $priceTypeSlug] = self::denormalizePriceItem($item);

        if (in_array($citySlug, ['spb', 'msk', 'rnd']) && in_array($priceTypeSlug, ['opt', 'stop', 'promotion', 'action', 'retail'])) {
            return [
                'city_id' => City::query()->where('slug', $citySlug)->value('id'),
                'price_type_id' => PriceType::query()->where('slug', $priceTypeSlug)->value('id'),
                'value' => $item['Цена'] * 100,
                'product_id' => $item['product_id'],
            ];
        }

        return null;
    }
}
