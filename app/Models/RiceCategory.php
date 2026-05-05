<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [

        'name',
        'price_per_kg',
        'stock_kg',
        'image',
    ];

    protected $casts = [
        'price_per_kg' => 'float',
        'stock_kg'     => 'float',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
