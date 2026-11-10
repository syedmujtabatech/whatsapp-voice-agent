<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoiceConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'phone_number',
        'customer_name',
        'channel',
        'status',
        'context',
        'last_activity',
    ];

    protected $casts = [
        'context' => 'array',
        'last_activity' => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(VoiceMessage::class)->orderBy('created_at', 'asc');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
