<?php

namespace App\Renderer;

use Psr\Http\Message\ResponseInterface;

final class JsonRenderer
{
    public static function json(ResponseInterface $response, mixed $data = null, int|string $status = '200'): ResponseInterface
    {
        if ((int)$status < 100 || (int)$status > 505) {
            $status = 503;
        }

        $data = is_array($data)
            ? json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR)
            : $data;


        $response->getBody()->write($data);

        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
