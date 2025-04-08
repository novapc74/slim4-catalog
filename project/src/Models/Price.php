<?php

namespace App\Models;

use App\Traits\GenerateUuidTrait;
use Illuminate\Database\Query\Builder;
use App\Traits\GenerateUniqueSlugTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    #TODO тут, нужно подумать, :) как и везде.
    public static function upsertPrice($sortedCategories): int
    {
        return self::upsert($sortedCategories, ['id']);
    }
}
