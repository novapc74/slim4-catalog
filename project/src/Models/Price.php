<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Builder
 */
class Price extends Model
{
    public $timestamps = false;

    /**
     * Атрибуты, для которых разрешено массовое присвоение значений.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'value',
        'price_type_id',
        'product_id',
        'city_id',
    ];

    public static function upsertPrice($sortedCategories): int
    {
        return self::upsert($sortedCategories, ['id']);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
    public function priceType(): BelongsTo
    {
        return $this->belongsTo(PriceType::class);
    }
}
