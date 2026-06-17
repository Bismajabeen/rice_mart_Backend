<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopReview extends Model
{

    protected $fillable = [

        'customer_id',
        'order_item_id',
        'shop_id',
        'rating',
        'review'

    ];


    public function customer()
    {
        return $this->belongsTo(User::class,'customer_id');
    }


    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }


    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

}