<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid;

trait GenerateUuidTrait
{
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string)Uuid::uuid4(); // Генерация UUID
            }
        });
    }
}

