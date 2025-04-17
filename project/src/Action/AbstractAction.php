<?php

namespace App\Action;

use Exception;
use App\Renderer\JsonRenderer;
use App\Traits\SecretTokenTrait;
use Psr\Http\Message\ResponseInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class AbstractAction
{
    use SecretTokenTrait;

    protected Request $request;

    protected ResponseInterface $response;

    protected array $args;

    public function __construct(protected TagAwareCacheInterface $cache) // Добавляем кэш в конструктор
    {
    }

    public function __invoke(Request $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        try {
            return $this->action();
        } catch (Exception $e) {
            return JsonRenderer::json($response, $e->getMessage(), $e->getCode());
        }
    }

    abstract protected function action(): ResponseInterface;

    protected function jsonResponse($data, int $status = 200, bool $isJson = false): ResponseInterface
    {
        return JsonRenderer::json($this->response, $data, $status);
    }
}
