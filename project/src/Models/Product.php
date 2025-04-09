<?php

namespace App\Models;

use App\Traits\GenerateUuidTrait;
use App\Traits\GenerateUniqueSlugTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Builder
 */
class Product extends Model
{
    use HasUuids;
    use GenerateUuidTrait;
    use GenerateUniqueSlugTrait;

    /**
     * Тип данных автоинкрементного идентификатора.
     *
     * @var string
     */
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * Атрибуты, для которых разрешено массовое присвоение значений.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'title',
        'slug',
        'category_id',
        'brand_id',
    ];

    /**
     * Получить родительскую категорию.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    public function productProperties(): HasMany
    {
        return $this->hasMany(ProductProperty::class);
    }

    public static function upsertProduct($sortedCategories): int
    {
        return self::upsert($sortedCategories, ['id', 'slug']);
    }
}
