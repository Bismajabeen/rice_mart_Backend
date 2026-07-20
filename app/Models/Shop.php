<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RiceCategory;
use App\Models\User;
use App\Models\OrderItem;

class Shop extends Model
{
    protected $fillable = [
        'user_id',
        'cnic',
        'cnic_image',
        'cnic_back_image',
        'shop_name',
        'owner_name',
        'phone',
        'city',
        'address',
        'description',

        'status',
        'is_approved',

        'correction_reason',
        'correction_requested_at',
    ];

    public function riceCategories()
    {
        return $this->hasMany(RiceCategory::class);
    }
            // $shop->user
    public function user()
   {
    return $this->belongsTo(User::class);
   }

   // $shop->orderItems
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}