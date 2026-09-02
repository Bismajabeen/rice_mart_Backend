<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiceCategory extends Model
{
    protected $fillable = [
        'name',
        'cooking_time',
        'common_uses',
        'description',
        'model_label',
    ];
}