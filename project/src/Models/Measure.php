<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class Measure extends Model
{
    public $timestamps = false;
    public $fillable = [
        'title'
    ];
}
