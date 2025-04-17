<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GenerateUniqueSlugTrait
{
    public static function bootGenerateUniqueSlugTrait(): void
    {
        static::saving(function ($model) {
            $model->slug = $model->generateUniqueSlug($model->title);
        });
    }

    public function generateUniqueSlug($title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "$originalSlug-$count";
            $count++;
        }

        return $slug;
    }
}
