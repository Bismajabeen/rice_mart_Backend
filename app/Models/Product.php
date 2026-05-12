<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [

        'user_id',
        'shop_id',
        'rice_category_id',
        'name',
        'price',
        'stock',
        'image',
    ];

    // =========================
    // USER RELATION
    // =========================
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // =========================
    // SHOP RELATION
    // =========================
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // =========================
    // RICE CATEGORY RELATION
    // =========================
    public function riceCategory()
    {
        return $this->belongsTo(RiceCategory::class);
    }
}