<?php

namespace App\Service\Command\LeftoversSync;

use App\Models\City;

class LeftoversDto
{
    public function __construct(private readonly string $productId,
                                private readonly array  $leftovers)
    {
    }

    public static function new(string $productId, array $leftovers): self
    {
        return new self($productId, $leftovers);
    }

    public function execute(): ?array
    {
        if (!$cityId = self::getCityId($this->leftovers['base'])) {
            return null;
        }

        return [
            'product_id' => $this->productId,
            'amount' => $this->leftovers['quantity'],
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
