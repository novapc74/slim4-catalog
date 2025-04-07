<?php

namespace App\Action;

use App\Renderer\JsonRenderer;
use App\Traits\HumanSizeCounterTrait;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class AbstractAction
{
    use HumanSizeCounterTrait;

    protected Request $request;

    protected ResponseInterface $response;

    protected array $args;


    public function __invoke(Request $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        try {
            return $this->action();
        } catch (Exception $e) {
         #TODO throw custom exception ...
            return JsonRenderer::json($response, $e->getMessage(), $e->getCode());
        }
    }

    abstract protected function action(): ResponseInterface;

    protected function jsonResponse(ResponseInterface $response, $data, int $status = 200): ResponseInterface
    {
        return JsonRenderer::json($response, $data, $status);
    }
}
