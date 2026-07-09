<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $fillable = [
        'chat_id',
        'sender',   // customer, agent
        'message',
    ];

    public function chat()
    {
        return $this->belongsTo(LiveChat::class, 'chat_id');
    }
}
