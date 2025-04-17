<?php

namespace App\Renderer;

use Psr\Http\Message\ResponseInterface;

final class JsonRenderer
{
    public static function json(ResponseInterface $response, mixed $data = null, int|string $status = '200', bool $isJson = false): ResponseInterface
    {
        if ((int)$status < 100 || (int)$status > 505) {
            $status = 503;
        }

        if ($isJson) {
            $response->getBody()->write($data);
            return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
        }

        $data = is_array($data) || is_object($data)
            ? json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR)
            : $data;

        $response->getBody()->write($data);

        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
