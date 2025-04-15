<?php

namespace App\Exception;

use Exception;
use Throwable;

class JsonException extends Exception
{
    private array $errorMessageData = [];

    public function __construct(mixed $violations = [], int $code = 404, ?Throwable $previous = null)
    {
        if (is_string($violations)) {
            $this->errorMessageData['violations']['default'] = $violations;
        }

        if (is_array($violations)) {
            foreach ($violations as $key => $violation) {
                $this->errorMessageData['violations'][$key] = $violation;
            }

            $violations = json_encode($violations, JSON_UNESCAPED_UNICODE, 6);
        }

        parent::__construct($violations, $code, $previous);

        $this->errorMessageData['code'] = $code;
    }

    public function getExceptionMessage(): array
    {
        return $this->errorMessageData ?? [];
    }
}
