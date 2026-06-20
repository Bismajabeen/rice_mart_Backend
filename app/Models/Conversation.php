<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['buyer_id', 'seller_id', 'shop_id', 'last_message_at'];

    public function buyer() {
        return $this->belongsTo(User::class, 'buyer_id');
    }
    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }
    public function shop() {
        return $this->belongsTo(Shop::class);
    }
    public function messages() {
        return $this->hasMany(Message::class);
    }
    public function lastMessage() {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}