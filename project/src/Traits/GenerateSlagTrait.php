<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GenerateSlagTrait
{
    private static array $resolvedTitles = [];
    private static array $slugCollection = [];

    public static function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        if (!in_array($slug, self::$slugCollection)) {
            self::$slugCollection[] = $slug;
            return $slug;
        }

        $i = 1;
        while (in_array($slug, self::$slugCollection)) {
            $slug = $slug . '-' . $i;
            $i++;
        }

        self::$slugCollection[] = $slug;

        return $slug;
    }

    public static function addResolvedTitle(string $slug): void
    {
        self::$resolvedTitles[] = $slug;
    }

    public static function getResolvedTitles(): array
    {
        return self::$resolvedTitles;
    }
}
