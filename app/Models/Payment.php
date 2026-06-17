<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_type',
        'amount',
        'transaction_id',
        'screenshot_path',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'gateway_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
        'gateway_response' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}