<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Shop;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

   protected $fillable = [
    'name',
    'email',
    'password',
    'otp',
    'otp_expires_at',
    'is_verified',
    'account_status',
    'removed_reason',
    'removed_at',
];


    protected $hidden = [
        'password',
        'remember_token',
         'otp',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'removed_at' => 'datetime',
        ];
    }

    public function shop()
    {
       return $this->hasOne(Shop::class);
    }

    public function isRemoved(): bool
    {
        return $this->account_status === 'removed';
    }
}


