<?php

namespace App\Models;

use App\Traits\GenerateUniqueSlugTrait;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
//    use GenerateUniqueSlugTrait;
    public $timestamps = false;
    public $fillable = [
        'title',
        'slug'
    ];
}
