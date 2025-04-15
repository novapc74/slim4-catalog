<?php

namespace App\Traits;

trait SecretTokenTrait
{
    public static function isSecureRequest(): bool
    {
        $secretToken = env('SECRET_TOKEN');
        $requestToken = $_SERVER['HTTP_X_SECRET_TOKEN'] ?? null;

        return $requestToken === $secretToken;
    }
}
