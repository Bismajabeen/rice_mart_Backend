<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerPayout extends Model
{
    protected $fillable = [
        'order_id', 'shop_id',
        'gross_amount', 'commission_amount', 'net_amount',
        'status', 'payout_method', 'transaction_id', 'proof_path',
        'paid_at', 'paid_by',
    ];

    protected $casts = [
        'gross_amount' => 'float',
        'commission_amount' => 'float',
        'net_amount' => 'float',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}