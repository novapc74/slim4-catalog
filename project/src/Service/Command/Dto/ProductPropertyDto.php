<?php

namespace App\Service\Command\Dto;

use App\Models\Property;

class ProductPropertyDto
{
    private string $propertyTitle;

    public function __construct(private readonly string $productId,
                                private readonly array  $productPropertyItem)
    {
    }

    public static function new(string $productId, array $productPropertyItem): self
    {
        return new self($productId, $productPropertyItem);
    }

    public function execute(): ?array
    {
        $this->propertyTitle = explode(',', $this->productPropertyItem['Имя'])[0] ?? trim($this->productPropertyItem['Имя']);

        if (!$propertyId = Property::query()
            ->where('title', trim($this->propertyTitle))
            ->where('is_invisible', false)->value('id')) {
            return null;
        }

        if (!$value = self::getValue()) {
            return null;
        }

        return [
            'product_id' => $this->productId,
            'property_id' => $propertyId,
            'value' => $value,
        ];
    }

    private function getValue(): ?string
    {
        if (empty($this->productPropertyItem['Значение'])) {
            return null;
        }

        $isSquareRatioProperty = $this->propertyTitle == 'Коэффициент для пересчета в м2';
        if (str_contains($this->productPropertyItem['Значение'], ':') && $isSquareRatioProperty) {
            $data = explode(':', $this->productPropertyItem['Значение']);

            $a = (float)str_replace(',', '.', $data[0]);
            $b = (float)str_replace(',', '.', $data[1]);

            if ($b > 0) {
                return (string)($a / $b);
            }
        }

        if (strpos($this->productPropertyItem['Значение'], ',')) {
            $value = str_replace(',', '.', $this->productPropertyItem['Значение']);

            return trim($value);
        }

        return $this->productPropertyItem['Значение'];
    }
}
