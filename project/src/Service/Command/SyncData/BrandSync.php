<?php

namespace App\Service\Command\SyncData;

use App\Models\Brand;
use App\Traits\GenerateSlagTrait;
use App\Service\Command\Interface\GenerateSlugInterface;
use App\Service\Command\Interface\SyncDatabaseInterface;

class BrandSync implements SyncDatabaseInterface, GenerateSlugInterface
{
    use GenerateSlagTrait;

    private static array $resolvedBrandTitle = [];

    public static function getEntityFqcn(): string
    {
        return Brand::class;
    }

    public static function getFileName(): ?string
    {
        return file_exists($filename = __DIR__ . '/../../../../var/data/products.json')
            ? $filename
            : null;
    }

    public static function getEntityItem(array $data): ?array
    {
            if (!$title = $data['Бренд'] ?? null) {
                return null;
            }

            $title = mb_ucfirst(mb_strtolower($title));

            if (in_array($title, self::getResolvedTitles())) {
                return null;
            }

            self::addResolvedTitle($title);

            $collection[] = [
                'title' => $title,
                'slug' => self::generateSlug($title),
            ];

        return $collection ?? null;
    }
}
