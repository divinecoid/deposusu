<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\LiveChat;
use App\Models\TrxComplaint;
use App\Models\TrxOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = TrxComplaint::with('order')
            ->where('customer_id', Auth::id())
            ->latest()
            ->get();

        return view('customer.complaints.index', compact('complaints'));
    }

    public function create(TrxOrder $order)
    {
        abort_unless($order->customer_id === Auth::id(), 403);

        return view('customer.complaints.create', compact('order'));
    }

    public function store(Request $request, TrxOrder $order)
    {
        abort_unless($order->customer_id === Auth::id(), 403);

        $request->validate([
            'category' => 'required|in:' . implode(',', array_keys(TrxComplaint::CATEGORIES)),
            'description' => 'required|string|min:10|max:1000',
        ]);

        $complaint = TrxComplaint::create([
            'customer_id' => Auth::id(),
            'order_id' => $order->id,
            'category' => $request->category,
            'description' => $request->description,
        ]);

        // Same conduit as live chat, mirroring how the legacy system
        // surfaced a new complaint to support staff.
        $chat = LiveChat::firstOrCreate(
            ['customer_id' => Auth::id()],
            ['customer_name' => Auth::user()->name, 'customer_email' => Auth::user()->email, 'status' => 'unread']
        );
        $systemMessage = "Komplain baru untuk pesanan #{$order->order_number} ({$complaint->categoryLabel()}): {$request->description}";
        ChatMessage::create(['chat_id' => $chat->id, 'sender' => 'customer', 'message' => $systemMessage]);
        $chat->update(['status' => 'unread', 'last_message' => $systemMessage]);

        return redirect()->route('complaints.index')->with('success', 'Komplain berhasil dikirim. Tim kami akan segera meninjau.');
    }
}
