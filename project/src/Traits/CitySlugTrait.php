<?php

namespace App\Traits;

trait CitySlugTrait
{
    protected static function citySlug(): string
    {
        return $_SERVER['HTTP_X_CITY_SLUG'] ?? 'spb';
    }
}
