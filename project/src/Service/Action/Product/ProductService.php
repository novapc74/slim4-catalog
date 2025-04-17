<?php

namespace App\Service\Action\Product;

use App\Traits\CitySlugTrait;
use App\Traits\HumanSizeCounterTrait;
use Symfony\Component\Cache\CacheItem;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class ProductService
{
    use CitySlugTrait;
    use HumanSizeCounterTrait;

    /**
     * @throws InvalidArgumentException
     */
    public static function product(TagAwareCacheInterface $cache, string $productSlug): string
    {
        $citySlug = self::citySlug();

        return $cache->get("product-$productSlug-$citySlug", function (CacheItem $item) use ($productSlug, $citySlug) {
            $item->expiresAfter(86400);
            $item->tag('product-city');

            $data =  [
                'breadcrumbs' => ProductBreadcrumbs::breadcrumbs(slug: $productSlug),
                'product' => ProductPage::product(slug: $productSlug),
                'meta' => [
                    'peak_memory' => self::humanizeUsageMemory(true),
                    'city_slug' => self::citySlug(),
                ],
            ];

            return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
        });
    }
}
