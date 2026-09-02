<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiceDetectionLog extends Model
{
    protected $fillable = [
        'user_id',
        'rice_category_id',
        'image_path',
        'confidence',
        'processing_time_ms',
    ];

    public function category()
    {
        return $this->belongsTo(RiceCategory::class, 'rice_category_id');
    }
}