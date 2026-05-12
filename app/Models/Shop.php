<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RiceCategory;
use App\Models\User;

class Shop extends Model
{
    protected $fillable = [
        'user_id',
        'cnic',
        'cnic_image',
        'shop_name',
        'owner_name',
        'phone',
        'address',
        'description',

        'status',
        'is_approved'
    ];

    public function riceCategories()
    {
        return $this->hasMany(RiceCategory::class);
    }

    public function user()
{
    return $this->belongsTo(User::class);
}
}