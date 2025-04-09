<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class Brand extends Model
{
    public $timestamps = false;
    public $fillable = [
        'title',
    ];

    public static function upsertBrand(array $brands): int
    {
        return self::upsert($brands, ['id', 'title']);
    }
}
