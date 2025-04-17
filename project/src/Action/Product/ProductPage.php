<?php

namespace App\Action\Product;

use App\Action\AbstractAction;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use App\Service\Action\Product\ProductService;

class ProductPage extends AbstractAction
{
    /**
     * @throws InvalidArgumentException
     */
    protected function action(): ResponseInterface
    {
        $arguments = [$this->cache, $this->args['slug']];
        $product = ProductService::product(...$arguments);

        return $this->jsonResponse($product, 200, true);
    }
}
