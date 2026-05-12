<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiceCategory extends Model
{
    protected $fillable = [

        'name',
        'image',
        'status',
    ];

    // =========================
    // PRODUCTS RELATION
    // =========================
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}