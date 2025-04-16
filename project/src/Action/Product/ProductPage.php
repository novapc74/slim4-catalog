<?php

namespace App\Action\Product;

use App\Action\AbstractAction;
use App\Exception\JsonException;
use App\Traits\HumanSizeCounterTrait;
use Psr\Http\Message\ResponseInterface;
use App\Service\Action\Product\ProductService;

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
            'category' => '',
            'product' => ProductService::new()->getProduct(slug: $slug),
            'meta' => [
                'peak_memory' => self::humanizeUsageMemory(true),
                'city_slug' => self::citySlug(),
            ],
        ];

        return $this->jsonResponse($data);
    }
}
