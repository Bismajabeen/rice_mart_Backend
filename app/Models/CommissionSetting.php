<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    protected $fillable = ['percentage', 'updated_by'];
    protected $casts = ['percentage' => 'float'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    public static function current(): float
    {
        return static::latest()->value('percentage') ?? 5.00;
    }
}