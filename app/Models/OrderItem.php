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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function review()
    {
        return $this->hasOne(ShopReview::class);
    }
}