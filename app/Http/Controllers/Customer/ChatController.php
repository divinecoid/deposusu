<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\LiveChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $chat = $this->currentChat()->load('messages');

        return view('customer.chat.index', compact('chat'));
    }

    /**
     * Lightweight polling endpoint so the customer sees new agent replies
     * without a full page reload — no websocket/broadcasting infra here,
     * so plain polling is the simplest thing that actually works.
     */
    public function poll(Request $request)
    {
        $chat = $this->currentChat();

        $messages = $chat->messages()
            ->when($request->query('after_id'), fn ($q, $afterId) => $q->where('id', '>', $afterId))
            ->orderBy('id')
            ->get();

        return response()->json(['success' => true, 'messages' => $messages]);
    }

    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $chat = $this->currentChat();

        if ($chat->status === 'history') {
            $chat->status = 'unread';
        } elseif ($chat->status !== 'active') {
            $chat->status = 'unread';
        }

        $chat->last_message = $request->message;
        $chat->save();

        $message = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender' => 'customer',
            'message' => $request->message,
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }

    private function currentChat(): LiveChat
    {
        $user = Auth::user();

        return LiveChat::firstOrCreate(
            ['customer_id' => $user->id],
            [
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'status' => 'unread',
            ]
        );
    }
}
