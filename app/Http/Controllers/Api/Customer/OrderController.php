<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\XenditPaymentController;
use App\Models\TrxOrder;
use App\Models\TrxXenditPayment;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = TrxOrder::where('customer_id', $request->user()->id)
            ->with('items.product')
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'orders' => $orders->through(fn ($order) => $this->formatOrder($order))->items(),
            'has_more' => $orders->hasMorePages(),
        ]);
    }

    public function show(Request $request, TrxOrder $order)
    {
        $this->authorizeOwnership($request, $order);
        $order->load('items.product');

        return response()->json([
            'success' => true,
            'order' => $this->formatOrder($order, detailed: true),
        ]);
    }

    public function pay(Request $request, TrxOrder $order, XenditService $xendit)
    {
        $this->authorizeOwnership($request, $order);

        if ($order->payment_status === 'PAID') {
            return response()->json(['success' => false, 'message' => 'Pesanan ini sudah dibayar'], 422);
        }

        $request->validate(['payment_method' => 'required|string']);
        $channelCode = $request->input('payment_method');

        if (!$xendit->channel($channelCode)) {
            return response()->json(['success' => false, 'message' => 'Metode pembayaran tidak valid'], 422);
        }

        try {
            $payment = $xendit->createPayment($order, $channelCode);
        } catch (\Throwable $e) {
            Log::error('Xendit retry payment failed (mobile)', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal membuat pembayaran: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'payment' => XenditPaymentController::formatPayment($payment, $xendit->channel($channelCode), 'api.customer.payments.status'),
        ]);
    }

    public function paymentStatus(Request $request, TrxXenditPayment $payment, XenditService $xendit)
    {
        $this->authorizeOwnership($request, $payment->order);

        $payment = $xendit->refreshStatus($payment);

        return response()->json([
            'success' => true,
            'status' => $payment->status,
            'order_id' => $payment->order_id,
        ]);
    }

    private function authorizeOwnership(Request $request, TrxOrder $order): void
    {
        abort_unless((int) $order->customer_id === (int) $request->user()->id, 403, 'Anda tidak memiliki akses ke pesanan ini');
    }

    private function formatOrder(TrxOrder $order, bool $detailed = false): array
    {
        $data = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status?->value,
            'status_label' => $order->status?->label(),
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
            'total_amount' => (float) $order->total_amount,
            'total_discount' => (float) $order->total_discount,
            'convenience_fee' => (float) $order->convenience_fee,
            'grand_total' => (float) $order->total_amount + (float) $order->convenience_fee,
            'created_at' => $order->created_at->toIso8601String(),
        ];

        if ($detailed) {
            $data['shipping_address'] = $order->customer_address;
            $data['items'] = $order->items->map(fn ($item) => [
                'product_name' => $item->product->name ?? 'Produk',
                'quantity' => $item->quantity,
                'price' => (float) $item->price,
                'subtotal' => (float) $item->subtotal,
            ]);
        }

        return $data;
    }
}
