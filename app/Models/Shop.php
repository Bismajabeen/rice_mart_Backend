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
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function riceCategories()
    {
        return $this->hasMany(RiceCategory::class);
    }
}
