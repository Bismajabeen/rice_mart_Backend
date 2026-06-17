<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
    'seller_id',
    'name',
    'owner_name',
    'phone',
    'address',
    'description',
    'logo',
    'cnic_number',
    'cnic_image',
    'status',
];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}