<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RiceCategory extends Model
{
    protected $fillable = [
        'name',
        'image',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $appends = ['image_url'];

    // =========================
    // FULL IMAGE URL ACCESSOR
    // =========================
    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : null;
    }

    // =========================
    // PRODUCTS RELATION
    // =========================
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}