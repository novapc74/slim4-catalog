<?php

namespace App\Middleware;

use Slim\Psr7\Response;
use App\Exception\JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
class JsonExceptionHandler
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (JsonException $e) {
            $response = new Response();
            $response->getBody()->write(json_encode($e->getExceptionMessage()));
            return $response->withStatus($e->getCode())
                ->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            // Если это не JsonException, передаем управление дальше
            throw $e; // Это позволит Slim обработать другие исключения
        }
    }
}
