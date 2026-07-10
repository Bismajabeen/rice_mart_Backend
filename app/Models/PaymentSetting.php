<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'easypaisa_number',
        'easypaisa_account_name',
        'jazzcash_number',
        'jazzcash_account_name',
    ];
}