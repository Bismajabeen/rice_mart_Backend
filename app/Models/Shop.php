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

        'payout_easypaisa_number',
        'payout_easypaisa_account_name',
        'payout_jazzcash_number',
        'payout_jazzcash_account_name',
    ];

    public function riceCategories()
    {
        return $this->hasMany(RiceCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payouts()
    {
        return $this->hasMany(SellerPayout::class);
    }
}