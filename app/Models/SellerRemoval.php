<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerRemoval extends Model
{
    protected $fillable = [
        'shop_id',
        'user_id',
        'removed_by',
        'reason',
        'permanently_banned',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'removed_by');
    }
}