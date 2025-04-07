<?php

namespace App\Config\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $host = 'https://' . $_ENV['CORS'];
        $response = $handler->handle($request);

        $response
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Origin', $host)
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Features, Token, withCredentials');

        if ($request->getMethod() === 'OPTIONS') {
             $response
                ->withHeader('Content-Type', 'text/plain; charset=UTF-8')
                ->withHeader('Content-Length', 0)
                ->withStatus(204);

             return $response;
        }

        return $response;
    }
}
