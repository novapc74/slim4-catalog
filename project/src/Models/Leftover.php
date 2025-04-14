<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Builder
 */
class Leftover extends Model
{
    public $timestamps = false;
    /**
     * Атрибуты, для которых разрешено массовое присвоение значений.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'amount',
        'product_id',
        'city_id',
    ];

    public static function truncateLeftovers(): void
    {
        self::truncate();
    }

    public static function upsertLeftover($sortedCategories): int
    {
        return self::upsert($sortedCategories, ['id', 'product_id', 'city_id']);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
