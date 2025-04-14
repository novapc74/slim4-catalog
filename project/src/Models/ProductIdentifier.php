<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class ProductIdentifier extends Model
{
    public $timestamps = false;
    public $fillable = [
        'shop_code',
        'sku',
        'description'
    ];

    public static function upsertProductIdentifier(array $data): int
    {
        return self::upsert($data, ['id', 'shop_code', 'sku']);
    }
}
