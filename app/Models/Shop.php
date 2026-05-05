<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cnic_number',
        'cnic_image',
        'shop_name',
        'owner_name',
        'phone',
        'address',
        'description',
        'is_approved',
        'rice_categories', // ✅ JSON column — no shop_id, no separate table
    ];

    protected $casts = [
        'is_approved'     => 'boolean',
        'rice_categories' => 'array', // ✅ auto encode/decode JSON
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ✅ No riceCategories() relationship — stored directly in this model
}
