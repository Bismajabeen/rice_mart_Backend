<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'payment_status',
    ];

    // =========================
    // ORDER ITEMS
    // =========================
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // =========================
    // USER RELATION
    // =========================
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}