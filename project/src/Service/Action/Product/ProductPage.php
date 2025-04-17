<?php

namespace App\Service\Action\Product;

use App\Traits\CitySlugTrait;
use App\Exception\JsonException;
use App\Enum\SQL\Product\ProductSql;
use App\Service\Singleton\CapsuleSingleton;

class ProductPage
{
    use CitySlugTrait;
    /**
     * @throws JsonException
     */
    public static function product(string $slug): object
    {
        $sql = ProductSql::PRODUCT_BY_SLUG->value;

        $capsule = CapsuleSingleton::capsule();

        $sqlParams = [
            'productSlug' => $slug,
            'citySlug' => self::citySlug(),
        ];

        if (!$product = $capsule::select($sql, $sqlParams)[0] ?? null) {
            throw new JsonException(['error' => 'Product not found'], 404);
        }

        $product->properties = json_decode($product->properties, true);
        $product->prices = json_decode($product->prices, true);

        return $product;
    }
}
