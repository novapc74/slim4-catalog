<?php

namespace App\Action\Product;

use App\Action\AbstractAction;
use App\Exception\JsonException;
use App\Traits\HumanSizeCounterTrait;
use Psr\Http\Message\ResponseInterface;
use App\Service\Action\Product\ProductService;
use App\Service\Action\Product\ProductBreadcrumbs;

class ProductPage extends AbstractAction
{
    use HumanSizeCounterTrait;
    /**
     * @throws JsonException
     */
    protected function action(): ResponseInterface
    {
        $slug = $this->args['slug'];

        $data = [
            'breadcrumbs' => ProductBreadcrumbs::breadcrumbs($slug),
            'product' => ProductService::product(slug: $slug),
            'meta' => [
                'peak_memory' => self::humanizeUsageMemory(true),
                'city_slug' => self::citySlug(),
            ],
        ];

        return $this->jsonResponse($data);
    }
}
