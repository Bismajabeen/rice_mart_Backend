<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * One city has one courier charge.
     */
    public function courierCharge()
    {
        return $this->hasOne(CourierCharge::class);
    }
}