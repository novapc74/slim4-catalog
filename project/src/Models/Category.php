<?php

namespace App\Models;

use App\Traits\GenerateUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use App\Traits\GenerateUniqueSlugTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Builder
 */
class Category extends Model
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
        'id', 'title', 'slug', 'parent_category_id'
    ];

    /**
     * Получить родительскую категорию.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public static function upsertCategory($sortedCategories): int
    {
        return self::upsert($sortedCategories, ['id', 'slug']);
    }
}
