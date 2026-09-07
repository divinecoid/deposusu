<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LiveChat;
use App\Models\ChatMessage;
use App\Models\TrxOrder;
use App\Models\TrxOrderItem;
use App\Models\FinanceTransaction;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LiveChatController extends Controller
{
    public function index()
    {
        $unreadChats = LiveChat::where('status', 'unread')->with('messages')->latest()->get();
        $activeChats = LiveChat::where('status', 'active')->with('messages')->latest()->get();
        $historyChats = LiveChat::where('status', 'history')->with('messages')->latest()->get();

        return view('admin.chat.index', compact('unreadChats', 'activeChats', 'historyChats'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:live_chats,id',
            'message' => 'required|string',
        ]);

        $chat = LiveChat::find($request->chat_id);
        
        // If chat was unread, update it to active under the agent's name
        if ($chat->status === 'unread') {
            $chat->status = 'active';
            $chat->agent_name = 'Felinika Admin';
        }

        $chat->last_message = $request->message;
        $chat->save();

        $message = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender' => 'agent',
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'chat_status' => $chat->status,
            'agent_name' => $chat->agent_name,
        ]);
    }

    public function getCustomerDetails(Request $request)
    {
        $email = $request->query('email');
        $customer = User::where('email', $email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer tidak ditemukan.'
            ]);
        }

        $profile = $customer->customerProfile;
        $lastOrder = TrxOrder::where('customer_name', $customer->name)
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'customer' => [
                'name' => $customer->name,
                'email' => $customer->email,
                'membership' => $profile ? $profile->membership : 'Silver',
            ],
            'last_order' => $lastOrder ? [
                'id' => $lastOrder->id,
                'order_number' => $lastOrder->order_number,
                'total_amount' => $lastOrder->total_amount,
                'status' => $lastOrder->status->value,
                'status_label' => $lastOrder->status->label(),
            ] : null
        ]);
    }

    public function quickAction(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:live_chats,id',
            'action' => 'required|string|in:check_order,refund,repeat_order',
            'email' => 'required|string',
        ]);

        $chat = LiveChat::find($request->chat_id);
        $customer = User::where('email', $request->email)->first();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer tidak ditemukan.']);
        }

        $lastOrder = TrxOrder::where('customer_name', $customer->name)->latest()->first();

        if ($request->action === 'check_order') {
            if (!$lastOrder) {
                return response()->json(['success' => false, 'message' => 'Belum ada riwayat pesanan.']);
            }
            return response()->json([
                'success' => true,
                'message' => "Order #{$lastOrder->order_number} status is currently {$lastOrder->status->label()} (Amount: Rp " . number_format($lastOrder->total_amount, 0, ',', '.') . ")."
            ]);
        }

        if ($request->action === 'refund') {
            if (!$lastOrder) {
                return response()->json(['success' => false, 'message' => 'Belum ada pesanan untuk di-refund.']);
            }

            if ($lastOrder->status === OrderStatusEnum::CANCELLED || $lastOrder->status === OrderStatusEnum::REJECTED) {
                return response()->json(['success' => false, 'message' => 'Pesanan ini sudah dibatalkan/refund.']);
            }

            try {
                DB::beginTransaction();

                // Restore product stocks
                foreach ($lastOrder->items as $item) {
                    $item->product->increment('stock', $item->quantity);
                }

                // Update order & invoice status
                $lastOrder->status = OrderStatusEnum::CANCELLED;
                $lastOrder->payment_status = 'CANCELLED';
                $lastOrder->save();

                if ($lastOrder->invoice) {
                    $lastOrder->invoice->update(['status' => 'CANCELLED']);
                }

                // Log Refund to Finance
                FinanceTransaction::create([
                    'type' => 'refund',
                    'category' => 'refund',
                    'amount' => $lastOrder->total_amount,
                    'reference_id' => $lastOrder->order_number,
                    'description' => "Refund untuk order #{$lastOrder->order_number} via Live Chat quick action.",
                    'transaction_date' => now(),
                ]);

                // Post a system message in the chat
                $systemMsg = "SISTEM: Refund untuk order {$lastOrder->order_number} sebesar Rp " . number_format($lastOrder->total_amount, 0, ',', '.') . " berhasil diproses. Stok barang telah dikembalikan.";
                ChatMessage::create([
                    'chat_id' => $chat->id,
                    'sender' => 'agent',
                    'message' => $systemMsg,
                ]);

                $chat->last_message = $systemMsg;
                $chat->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Refund untuk Order #{$lastOrder->order_number} berhasil diproses!",
                    'system_message' => $systemMsg
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Gagal refund: ' . $e->getMessage()]);
            }
        }

        if ($request->action === 'repeat_order') {
            if (!$lastOrder) {
                return response()->json(['success' => false, 'message' => 'Belum ada pesanan untuk diulang.']);
            }

            try {
                DB::beginTransaction();

                // Generate new order number
                $today = now()->format('Ymd');
                $lastOrderRecord = TrxOrder::whereDate('created_at', now()->today())->orderBy('id', 'desc')->first();
                $sequence = $lastOrderRecord ? intval(substr($lastOrderRecord->order_number, -5)) + 1 : 1;
                $newOrderNumber = 'INV-' . $today . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

                // Create duplicate order
                $newOrder = TrxOrder::create([
                    'order_number' => $newOrderNumber,
                    'customer_name' => $lastOrder->customer_name,
                    'total_amount' => $lastOrder->total_amount,
                    'total_discount' => $lastOrder->total_discount,
                    'status' => OrderStatusEnum::PENDING,
                    'payment_status' => 'UNPAID',
                    'source' => 'app',
                ]);

                // Duplicate items & reduce stocks
                foreach ($lastOrder->items as $item) {
                    if ($item->product->stock < $item->quantity) {
                        throw new \Exception("Stok produk '{$item->product->name}' tidak mencukupi untuk mengulang pesanan ini.");
                    }
                    $item->product->decrement('stock', $item->quantity);

                    TrxOrderItem::create([
                        'order_id' => $newOrder->id,
                        'product_id' => $item->product_id,
                        'original_price' => $item->original_price,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'discount_amount' => $item->discount_amount,
                        'subtotal' => $item->subtotal,
                        'checked_quantity' => 0,
                    ]);
                }

                // Create invoice for new order
                $newOrder->invoice()->create([
                    'invoice_number' => $newOrder->order_number,
                    'issue_date' => now(),
                    'due_date' => now()->addDays(7),
                    'status' => 'UNPAID',
                    'total_amount' => $newOrder->total_amount,
                ]);

                // Post system confirmation message in chat
                $systemMsg = "SISTEM: Repeat order berhasil dibuat! Pesanan baru: #{$newOrder->order_number} senilai Rp " . number_format($newOrder->total_amount, 0, ',', '.') . " status PENDING.";
                ChatMessage::create([
                    'chat_id' => $chat->id,
                    'sender' => 'agent',
                    'message' => $systemMsg,
                ]);

                $chat->last_message = $systemMsg;
                $chat->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Pesanan berulang berhasil dibuat dengan nomor order #{$newOrder->order_number}!",
                    'system_message' => $systemMsg
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Gagal mengulang pesanan: ' . $e->getMessage()]);
            }
        }
    }
}
