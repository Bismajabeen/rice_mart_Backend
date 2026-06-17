<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'phone',
        'city',
        'address',
        'notes',
        'total_price',
        'status',
        'payment_status',
        ];

        protected $casts = [
            'total_price' => 'float',
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

    // =========================
    // PAYMENT RELATION
    // =========================
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}