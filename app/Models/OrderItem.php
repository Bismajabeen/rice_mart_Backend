<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'shop_id',
        'product_id',
        'quantity',
        'price',
        'status',
        'commission_amount',
        'net_amount',
        'customer_confirmed_at',
    ];

    protected $casts = [
        'price' => 'float',
        'commission_amount' => 'float',
        'net_amount' => 'float',
        'customer_confirmed_at' => 'datetime',
    ];

    // product Relation
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Shop realation
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // =========================
    // ORDER RELATION
    // =========================
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // review relation
    public function review()
    {
        return $this->hasOne(ShopReview::class);
    }
}