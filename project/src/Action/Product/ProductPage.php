<?php

namespace App\Action\Product;

use App\Action\AbstractAction;
use App\Exception\JsonException;
use Psr\Http\Message\ResponseInterface;
use App\Service\Action\Product\ProductService;

class ProductPage extends AbstractAction
{
    /**
     * @throws JsonException
     */
    protected function action(): ResponseInterface
    {
        $slug = $this->args['slug'];

        $data = ProductService::new()->getProduct(slug: $slug);

        return $this->jsonResponse($data);
    }
}
