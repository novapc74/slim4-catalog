<?php

namespace App\Service\Command\PriceSync;

use App\Models\City;
use App\Models\PriceType;

class PriceDto
{
    public function __construct(private readonly string $productId, private readonly array $priceItem)
    {
    }

    public static function new(string $productId, array $priceItem): self
    {
        return new self($productId, $priceItem);
    }

    public function execute(): ?array
    {
        if ($priceData = self::sanitizePrice($this->priceItem['ВидЦены'])) {
            $priceData['product_id'] = $this->productId;
            $priceData['value'] = $this->priceItem['Цена'] * 100;
            return $priceData;
        }

        return null;
    }

    private function sanitizePrice(string $price): ?array
    {
        return match ($price) {
            "СПБ Розница" => self::getPriceData('spb', 'retail'),
            "СПб Акция" => self::getPriceData('spb', 'action'),
            "СПБ Карта" => self::getPriceData('spb', 'promotion'),
            "СПБ Оптовая" => self::getPriceData('spb', 'opt'),
            "СПБ Стоп" => self::getPriceData('spb', 'stop'),
            "МСК Стоп" => self::getPriceData('msk', 'stop'),
            "МСК Оптовая" => self::getPriceData('msk', 'opt'),
            "МСК Акция" => self::getPriceData('msk', 'action'),
            "МСК Розница" => self::getPriceData('msk', 'retail'),
            "МСК Карта" => self::getPriceData('msk', 'promotion'),
            "РнД Стоп" => self::getPriceData('rnd', 'stop'),
            "РнД Оптовая" => self::getPriceData('rnd', 'opt'),
            "РнД Розница" => self::getPriceData('rnd', 'retail'),
            "РнД Карта" => self::getPriceData('rnd', 'promotion'),
            "РнД Акция" => self::getPriceData('rnd', 'action'),
            default => null,
        };
    }

    private function getPriceData(string $citySlug, string $priceTypeSlug): ?array
    {
        if (in_array($citySlug, ['spb', 'msk', 'rnd']) && in_array($priceTypeSlug, ['opt', 'stop', 'promotion', 'action', 'retail'])) {
            return [
                'city_id' => City::query()->where('slug', $citySlug)->value('id'),
                'price_type_id' => PriceType::query()->where('slug', $priceTypeSlug)->value('id'),
            ];
        }

        return null;
    }
}
