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
        'city_id',
        'address',
        'notes',
        'total_price',
        'delivery_charge',
        'status',
        'payment_status',
    ];

    protected $casts = [
        'total_price' => 'float',
        'delivery_charge' => 'float',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // =========================
    // CITY RELATION
    // =========================
    public function cityModel()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
