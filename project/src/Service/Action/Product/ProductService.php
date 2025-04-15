<?php

namespace App\Service\Action\Product;

use App\Traits\CitySlugTrait;
use App\Exception\JsonException;
use Illuminate\Database\Capsule\Manager as Capsule;

final class ProductService
{
    use CitySlugTrait;

    private Capsule $db;

    public function __construct()
    {
        $this->db = new Capsule();
    }

    public static function new(): self
    {
        return new self();
    }

    /**
     * @throws JsonException
     */
    public function getProduct(string $slug): array
    {
        #TODO организовать репозиторий, и там творить дичь :)

        if (!$product = $this->db::select('SELECT * FROM products WHERE slug = ?', [$slug])[0] ?? null) {
            throw new JsonException(['error' => 'Product not found'], 404);
        }

        return [
            'product' => $product,
            'citySlug' => self::citySlug(),
        ];
    }
}
