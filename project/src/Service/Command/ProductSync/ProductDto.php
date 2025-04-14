<?php

namespace App\Service\Command\ProductSync;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductIdentifier;
use Illuminate\Support\Str;

class ProductDto
{
    private array $slugCollection = [];
    private array $productData;

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

    public function execute(): array
    {
        return [
            'id' => self::getProductUuid(),
            'title' => self::getProductTitle(),
            'slug' => self::getSlug(self::getProductTitle()),
            'category_id' => self::getCategoryId() ?? null,
            'brand_id' => self::getBrandId() ?? null,
            'product_identifier_id' => self::getProductIdentifierId() ?? null
        ];
    }

    private function getProductIdentifierId(): ?int
    {
        return ProductIdentifier::query()->where('shop_code', $this->productData['КодТовара'])->value('id') ?? null;

    }

    public function getProductUuid(): string
    {
        return $this->productData['УникальныйИдентификатор'];
    }

    public function getSlug(string $title): string
    {
        $slug = Str::slug($title);
        if (!in_array($slug, $this->slugCollection)) {
            $this->slugCollection[] = $slug;
            return $slug;
        }

        $i = 1;
        while (in_array($slug, $this->slugCollection)) {
            $slug = $slug . '-' . $i;
            $i++;
        }

        $this->slugCollection[] = $slug;

        return $slug;
    }

    public function getBrandId(): ?int
    {
        return Brand::query()->where('title', self::getBrandTitle())->value('id') ?? null;
    }

    public  function getDescription(): ?string
    {
        return $this->productData['ФайлОписанияДляСайта'] ?? null;
    }

    public function getProductTitle(): string
    {
        return $this->productData['Наименование'];
    }

    public function getCategoryId(): ?string
    {
        if(Category::query()->where('id', $this->productData['РодительУникальныйИдентификатор'])->count() == 0) {
            return null;
        }
        return $this->productData['РодительУникальныйИдентификатор'] ?? null;
    }

    public function getBrandTitle(): ?string
    {
        if (!$brand = $this->productData['Бренд'] ?? null) {
            return null;
        }

        return mb_ucfirst(mb_strtolower($brand));
    }

    public function getProperties(): array
    {
        $properties = [];

        $mainPropertyCollection = self::getPropertyCollection('ОсновныеЕдиницыИзмерения');
        $additionalPropertyCollection = self::getPropertyCollection('ДополнительныеРеквизиты');

        return array_merge($properties, $mainPropertyCollection, $additionalPropertyCollection);
    }

    private function getPropertyCollection(string $title): array
    {
        $properties = [];
        foreach ($this->productData[$title] as $propertyData) {
            $data = explode(',', $propertyData['Имя']);
            if (!$property = $data[0] ?? null) {
                continue;
            }

            if(count($data) === 1) {
                $properties[]= [
                    'title' => $property,
                    'measure_id' => null,
                ];

                continue;
            }

            if ($measure = trim($data[1] ?? null)) {
                $measure = self::sanitizeMeasure($measure);
            }

            $properties[]= [
                'title' => $property,
                'measure' => $measure,
            ];
        }

        return $properties;
    }

    private static function sanitizeMeasure(string $measure): string
    {
        return match ($measure) {
            '°C', '°С' => '°С',
            'A', 'А' => 'А',
            'В / Гц', 'В/Гц' => 'В/Гц',
            'шт', 'шт.' => 'шт',
            'ч', 'час' => 'час',
            'с', 'сек' => 'сек',
            'град', 'Градусы' => 'град',
            'Н*м', 'Н×м' => 'Н×м',
            default => $measure
        };
    }

    public function getProductIdentifier(): array
    {
        return [
            'shop_code' => $this->productData['КодТовара'] ?? null,
            'sku' => $this->productData['Артикул'] ?? null,
            'description' => mb_substr(self::getDescription(), 0, 999),
        ];
    }
}
