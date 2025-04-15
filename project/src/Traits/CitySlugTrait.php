<?php

namespace App\Traits;

trait CitySlugTrait
{
    private const X_CITY_SLUG = '';
    protected static function citySlug(): string
    {
        return $_SERVER['HTTP_X_CITY_SLUG'] ?? 'spb';
    }
}
