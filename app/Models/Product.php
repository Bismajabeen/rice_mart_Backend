<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;

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
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function riceCategory()
    {
        return $this->belongsTo(RiceCategory::class);
    }
}