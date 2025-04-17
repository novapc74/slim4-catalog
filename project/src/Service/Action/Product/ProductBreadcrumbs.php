<?php

namespace App\Service\Action\Product;

use App\Models\Product;
use App\Traits\CitySlugTrait;
use App\Enum\SQL\Product\ProductSql;
use App\Service\Singleton\CapsuleSingleton;

class ProductBreadcrumbs
{
    use CitySlugTrait;

    public static function breadcrumbs(string $slug): array
    {
        $product = Product::findBySlug($slug);
        if (!$product) {
            return [];
        }

        if (!$categoryId = $product->category_id) {
            return [];
        }

        return self::getBreadcrumbs($categoryId);
    }

    private static function getBreadcrumbs(string $categoryId): array
    {
        $capsule = CapsuleSingleton::capsule();

        $sql = ProductSql::PRODUCT_BREADCRUMBS->value;

        $sqlParams = [
            'categoryId' => $categoryId,
            'citySlug' => self::citySlug(),
        ];

        $breadcrumbs = $capsule::select($sql, $sqlParams);

        foreach ($breadcrumbs as &$category) {
            $category = (array)$category;
            if (0 === $category['product_count']) {
                unset($category['product_count']);
            }
        }

        return $breadcrumbs;
    }
}
