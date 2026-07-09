<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveChat extends Model
{
    use HasFactory;

    protected $table = 'live_chats';

    protected $fillable = [
        'customer_name',
        'customer_email',
        'status',        // unread, active, history
        'last_message',
        'agent_name',
    ];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id');
    }
}
