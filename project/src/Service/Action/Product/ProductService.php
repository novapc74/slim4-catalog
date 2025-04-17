<?php

namespace App\Service\Action\Product;

use App\Traits\CitySlugTrait;
use App\Exception\JsonException;
use App\Enum\SQL\Product\ProductSql;
use Illuminate\Database\Capsule\Manager as Capsule;

final class ProductService
{
    use CitySlugTrait;

    private static Capsule $db;

    public function __construct()
    {
        self::$db = new Capsule();
    }

    public static function new(): self
    {
        return new self();
    }

    /**
     * @throws JsonException
     */
    public function getProduct(string $slug): object
    {
        $sql = ProductSql::PRODUCT_BY_SLUG->value;

        $sqlParams = [
            'productSlug' => $slug,
            'citySlug' => self::citySlug(),
        ];

        if (!$product = self::$db::select($sql, $sqlParams)[0] ?? null) {
            throw new JsonException(['error' => 'Product not found'], 404);
        }

        $product->properties = json_decode($product->properties, true);
        $product->prices = json_decode($product->prices, true);

        return $product;
    }
}
