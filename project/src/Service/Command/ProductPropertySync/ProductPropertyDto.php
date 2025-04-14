<?php

namespace App\Service\Command\ProductPropertySync;

use App\Models\Property;

class ProductPropertyDto
{
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
        $propertyTitle = explode(',', $this->productPropertyItem['Имя'])[0] ?? trim($this->productPropertyItem['Имя']);

        if (!$property_id = Property::query()
            ->where('title', trim($propertyTitle))
            ->where('is_invisible', false)->value('id')) {
            return null;
        }

        return [
            'product_id' => $this->productId,
            'property_id' => $property_id,
            'value' => self::getValue(),
        ];
    }

    private function getValue(): ?string
    {
        if (empty($this->productPropertyItem['Значение'])) {
            return null;
        }

        if (strpos($this->productPropertyItem['Значение'], ',')) {
            $value = str_replace(',', '.', $this->productPropertyItem['Значение']);

            return trim($value);
        }

        if (strpos($this->productPropertyItem['Значение'], ':')) {
            $data = explode(':', $this->productPropertyItem['Значение']);

            $a = (float)str_replace(',', '.', $data[0]);
            $b = (float)str_replace(',', '.', $data[1]);

            if ($b != 0) {
                return $a / $b;
            }
        }

        return $this->productPropertyItem['Значение'];
    }
}
