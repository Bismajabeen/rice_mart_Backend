<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannedEmail extends Model
{
    protected $fillable = ['email', 'reason', 'banned_by'];
}