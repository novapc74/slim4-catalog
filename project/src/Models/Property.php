<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Builder
 */
class Property extends Model
{
    public $timestamps = false;
    public $fillable = [
        'id',
        'title',
        'measure',
        'is_invisible'
    ];

    public static function upsertProperty(array $data): int
    {
        $i = 0;
        foreach ($data as $property) {
            self::updateOrCreate(
                ['title' => $property['title']],
                $property
            );
            $i++;
        }

        return $i;
    }
}
