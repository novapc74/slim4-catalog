<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Builder
 */
class Property extends Model
{
    public $timestamps = false;
    public $fillable = [
        'title'
    ];

    public function measure(): BelongsTo
    {
        return $this->belongsTo(Measure::class);
    }
}
