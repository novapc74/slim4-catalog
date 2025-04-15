<?php

namespace App\Service\Action\Product;

use App\Traits\CitySlugTrait;
use App\Exception\JsonException;
use Illuminate\Database\Capsule\Manager as Capsule;

readonly class ProductService
{
    use CitySlugTrait;
    public function __construct(private Capsule $db)
    {
    }

    public static function new(): self
    {
        return new self(new Capsule());
    }

    /**
     * @throws JsonException
     */
    public function getProduct(string $slug): array
    {
        if (!$product = $this->db::select('SELECT * FROM products WHERE slug = ?', [$slug])[0] ?? null) {
            throw new JsonException(['error' => 'Product not found'], 404);
        }

        return [
            'product' => $product,
            'citySlug' => self::citySlug(),
        ];
    }
}
