<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use App\Models\TrxXenditPayment;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XenditPaymentController extends Controller
{
    public function __construct(private XenditService $xendit)
    {
    }

    /**
     * Polled by the payment-waiting screen every few seconds. Refreshes the
     * status straight from Xendit rather than trusting only the webhook,
     * since local/dev environments can't receive Xendit's callback at all.
     */
    public function status(TrxXenditPayment $payment)
    {
        $this->authorizeOwnership($payment->order);

        $payment = $this->xendit->refreshStatus($payment);

        return response()->json([
            'success' => true,
            'status' => $payment->status,
            'order_id' => $payment->order_id,
            'redirect_url' => $payment->status === 'SUCCEEDED'
                ? route('transactions.show', $payment->order_id)
                : null,
        ]);
    }

    /**
     * Retry payment for an order that's still unpaid — e.g. the customer's
     * QRIS/VA code expired, or they want to switch channels.
     */
    public function retry(Request $request, TrxOrder $order)
    {
        $this->authorizeOwnership($order);

        if ($order->payment_status === 'PAID') {
            return response()->json(['success' => false, 'message' => 'Pesanan ini sudah dibayar'], 422);
        }

        $request->validate(['payment_method' => 'required|string']);
        $channelCode = $request->input('payment_method');

        if (!$this->xendit->channel($channelCode)) {
            return response()->json(['success' => false, 'message' => 'Metode pembayaran tidak valid'], 422);
        }

        try {
            $payment = $this->xendit->createPayment($order, $channelCode);
        } catch (\Throwable $e) {
            Log::error('Xendit retry payment failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal membuat pembayaran: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'payment' => self::formatPayment($payment, $this->xendit->channel($channelCode)),
        ]);
    }

    /**
     * Xendit calls this when a payment's status changes. Public route,
     * authenticated via the x-callback-token header instead of a session.
     */
    public function webhook(Request $request)
    {
        if (!$this->xendit->verifyCallbackToken($request->header('x-callback-token'))) {
            return response()->json(['message' => 'Invalid callback token'], 401);
        }

        $payload = $request->all();
        $data = $payload['data'] ?? [];

        $payment = TrxXenditPayment::where('xendit_payment_request_id', $data['payment_request_id'] ?? null)
            ->orWhere('reference_id', $data['reference_id'] ?? null)
            ->first();

        if (!$payment) {
            Log::warning('Xendit webhook: payment not found', $data);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->update(['webhook_payload' => $payload]);
        $this->xendit->applyStatus($payment, $data);

        return response()->json(['message' => 'ok']);
    }

    public static function formatPayment(\App\Models\TrxXenditPayment $payment, ?array $channelMeta, string $pollRouteName = 'checkout.payment.status'): array
    {
        return [
            'group' => $channelMeta['group'] ?? null,
            'channel_code' => $payment->channel_code,
            'channel_label' => $channelMeta['label'] ?? $payment->channel_code,
            'status' => $payment->status,
            'gross_amount' => (float) $payment->gross_amount,
            'convenience_fee' => (float) $payment->convenience_fee,
            'total_amount' => (float) $payment->total_amount,
            'qr_string' => $payment->qr_string,
            'virtual_account_number' => $payment->virtual_account_number,
            'virtual_account_bank' => $payment->virtual_account_bank,
            'checkout_url' => $payment->checkout_url,
            'expires_at' => optional($payment->expires_at)->toIso8601String(),
            'poll_url' => route($pollRouteName, $payment->id),
            'order_id' => $payment->order_id,
        ];
    }

    private function authorizeOwnership(TrxOrder $order): void
    {
        abort_unless(
            auth()->check() && (int) $order->customer_id === (int) auth()->id(),
            403,
            'Anda tidak memiliki akses ke pesanan ini'
        );
    }
}
