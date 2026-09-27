<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderShopCharge extends Model
{
    protected $fillable = ['order_id', 'shop_id', 'weight_kg', 'delivery_charge'];

    protected $casts = [
        'weight_kg' => 'float',
        'delivery_charge' => 'float',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}