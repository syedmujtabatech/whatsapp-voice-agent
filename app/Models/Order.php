<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'voice_conversation_id',
        'customer_name',
        'customer_phone',
        'delivery_address',
        'total_amount',
        'payment_method',
        'status',
        'channel',
        'voice_transcript',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function conversation()
    {
        return $this->belongsTo(VoiceConversation::class, 'voice_conversation_id');
    }
}
