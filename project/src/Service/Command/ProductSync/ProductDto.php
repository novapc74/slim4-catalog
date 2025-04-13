<?php

namespace App\Service\Command\ProductSync;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Measure;
use Illuminate\Support\Str;

class ProductDto
{
    private array $slugCollection = [];
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

    public function execute(): array
    {
        return [
            'id' => self::getProductUuid(),
            'title' => self::getProductTitle(),
            'slug' => self::getSlug(self::getProductTitle()),
            'category_id' => self::getCategoryId() ?? null,
            'brand_id' => self::getBrandId() ?? null,
            'description' => mb_substr(self::getDescription(), 0, 999),
        ];
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

    public function getProductProperties(): array
    {
        return $this->productData;
    }

    public function getProperties(): array
    {
        $properties = [];

        self::setProperty($properties, 'Артикул', 'Артикул');
        self::setProperty($properties, 'КодТовара', 'Код товара');

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
                $measureId = Measure::query()->where('title', $measure)->value('id') ?? null;
            }

            $properties[]= [
                'title' => $property,
                'measure_id' => $measureId ?? null,
            ];
        }

        return $properties;
    }

    private  function setProperty(array &$properties, string $title, string $newTitle = null,  $measureId = null): void
    {
        if ($this->productData[$title] ?? null) {
            $properties[] = [
                'title' => $newTitle ?? $title,
                'measure_id' => $measureId,
            ];
        }
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
