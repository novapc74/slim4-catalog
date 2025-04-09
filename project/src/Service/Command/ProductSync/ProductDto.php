<?php

namespace App\Service\Command\ProductSync;

class ProductDto
{
    private array $productData = [];

    public function __construct(mixed $productData)
    {
        $this->productData = is_string($productData)
            ? json_decode($productData, true)
            : $productData;
    }

    public static function new(mixed $productData): self
    {
        return new self($productData);
    }

    public function getBrand(): ?string
    {
        if (!$brand = $this->productData['Бренд'] ?? null) {
            return null;
        }

        return mb_ucfirst(mb_strtolower($brand));
    }

    public function getProductProperties(): array
    {
        return $this->productData;
    }

    public function getMeasures(): array
    {
        $measures = self::getMeasureCollection($this->productData['ОсновныеЕдиницыИзмерения']);
        $additionalMeasures = self::getMeasureCollection($this->productData['ДополнительныеРеквизиты']);

        $measures = array_merge($measures, $additionalMeasures);

        $measures = array_unique(array_filter($measures, function($measure) {
            return !empty($measure);
        }));

        return array_values($measures);
    }

    private static function getMeasureCollection(array $propertyCollection): array
    {
        $measures = [];
        foreach ($propertyCollection as $property) {
            $nameData = $property['Имя'];
            if(!$measure = explode(',', $nameData)[1] ?? null) {
                continue;
            }

            $measures[] = trim($measure);
        }

        return $measures;
    }
}
