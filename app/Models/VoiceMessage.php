<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoiceMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'voice_conversation_id',
        'sender',
        'type',
        'audio_path',
        'transcript',
        'intent',
        'entities',
        'response_text',
        'response_audio_path',
    ];

    protected $casts = [
        'entities' => 'array',
    ];

    public function conversation()
    {
        return $this->belongsTo(VoiceConversation::class, 'voice_conversation_id');
    }
}
