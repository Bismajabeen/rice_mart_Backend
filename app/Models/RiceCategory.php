<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'price_per_kg',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
