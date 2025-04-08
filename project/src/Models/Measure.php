<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Measure extends Model
{
    public $timestamps = false;
    public $fillable = [
        'title'
    ];
}
