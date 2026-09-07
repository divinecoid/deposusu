<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class XenditService
{
    /**
     * Every channel this store accepts, keyed by Xendit's channel_code.
     */
    public function channels(): array
    {
        return config('xendit.channels', []);
    }

    public function channel(string $channelCode): ?array
    {
        return $this->channels()[$channelCode] ?? null;
    }

    public function isConfigured(): bool
    {
        return filled(config('xendit.secret_key'));
    }

    /**
     * Convenience fee (and resulting total) the customer pays for a channel,
     * mirroring Xendit's own merchant fee so the store doesn't absorb it.
     */
    public function computeFee(string $channelCode, float $amount): array
    {
        $channel = $this->channel($channelCode);

        if (!$channel) {
            throw new RuntimeException("Unknown payment channel: {$channelCode}");
        }

        $percentFee = $amount * (($channel['fee_percent'] ?? 0) / 100);
        $fee = round($percentFee + ($channel['fee_flat'] ?? 0));

        return [
            'fee' => $fee,
            'total' => $amount + $fee,
        ];
    }

    /**
     * Create a Xendit Payment Request for an order and persist the attempt.
     *
     * @return \App\Models\TrxXenditPayment
     */
    public function createPayment(\App\Models\TrxOrder $order, string $channelCode)
    {
        $channel = $this->channel($channelCode);

        if (!$channel) {
            throw new RuntimeException("Unknown payment channel: {$channelCode}");
        }

        $grossAmount = (float) $order->total_amount;
        ['fee' => $fee, 'total' => $totalAmount] = $this->computeFee($channelCode, $grossAmount);

        if (($channel['group'] ?? null) === 'qris') {
            $max = $channel['max_amount'] ?? null;
            if ($max && $totalAmount > $max) {
                throw new RuntimeException(
                    'Total pembayaran melebihi batas QRIS (maks Rp ' . number_format($max, 0, ',', '.') . '). Pilih metode pembayaran lain.'
                );
            }
        }

        $referenceId = $order->order_number . '-' . Str::upper(Str::random(6));
        $expiresAt = now()->addMinutes(config('xendit.expiry_minutes.' . $channel['group'], 60));

        $payload = $this->buildPayload($channel, $channelCode, $referenceId, $totalAmount, $expiresAt, $order);

        $response = $this->client()
            ->post('/v3/payment_requests', $payload)
            ->throw();

        $data = $response->json();

        $payment = \App\Models\TrxXenditPayment::create([
            'order_id' => $order->id,
            'channel_code' => $channelCode,
            'reference_id' => $referenceId,
            'xendit_payment_request_id' => $data['payment_request_id'] ?? null,
            'gross_amount' => $grossAmount,
            'convenience_fee' => $fee,
            'total_amount' => $totalAmount,
            'status' => $this->mapStatus($data['status'] ?? 'PENDING'),
            'expires_at' => $expiresAt,
            'request_payload' => $payload,
            'response_payload' => $data,
            ...$this->extractActions($data),
        ]);

        $order->update([
            'payment_method' => $channelCode,
            'convenience_fee' => $fee,
        ]);

        return $payment;
    }

    /**
     * Pull the QR string / VA number / redirect URL out of the "actions"
     * array Xendit returns, whichever applies to this channel.
     */
    private function extractActions(array $data): array
    {
        $extracted = [
            'qr_string' => null,
            'virtual_account_number' => null,
            'virtual_account_bank' => null,
            'checkout_url' => null,
        ];

        foreach ($data['actions'] ?? [] as $action) {
            $descriptor = $action['descriptor'] ?? null;
            $value = $action['value'] ?? null;

            match ($descriptor) {
                'QR_STRING' => $extracted['qr_string'] = $value,
                'VIRTUAL_ACCOUNT_NUMBER' => $extracted['virtual_account_number'] = $value,
                'WEB_URL' => $extracted['checkout_url'] = $value,
                default => null,
            };
        }

        if ($extracted['virtual_account_number']) {
            $extracted['virtual_account_bank'] = str_replace('_VIRTUAL_ACCOUNT', '', $data['channel_code'] ?? '');
        }

        return $extracted;
    }

    private function buildPayload(array $channel, string $channelCode, string $referenceId, float $amount, $expiresAt, \App\Models\TrxOrder $order): array
    {
        $customer = auth()->user();

        $base = [
            'reference_id' => $referenceId,
            'type' => 'PAY',
            'country' => 'ID',
            'currency' => 'IDR',
            'request_amount' => (int) round($amount),
            'capture_method' => 'AUTOMATIC',
            'channel_code' => $channelCode,
            'description' => 'Pembayaran pesanan ' . $order->order_number,
        ];

        $base['channel_properties'] = match ($channel['group']) {
            'qris' => [
                'qr_string_type' => 'DYNAMIC',
                'expires_at' => $expiresAt->toIso8601String(),
            ],
            'ewallet' => [
                'success_return_url' => route('transactions.show', $order->id),
                'failure_return_url' => route('checkout.index'),
            ],
            'va' => [
                'display_name' => Str::limit($customer->name ?? 'DEPOSUSU Customer', 18, ''),
                'expires_at' => $expiresAt->toIso8601String(),
            ],
            default => [],
        };

        return $base;
    }

    private function mapStatus(string $xenditStatus): string
    {
        return match ($xenditStatus) {
            'SUCCEEDED' => 'SUCCEEDED',
            'FAILED' => 'FAILED',
            'EXPIRED' => 'EXPIRED',
            'CANCELED' => 'CANCELED',
            default => 'PENDING',
        };
    }

    /**
     * Refresh a payment's status directly from Xendit. Used by the polling
     * endpoint so local/dev environments (which Xendit's webhook can't
     * reach) still see accurate status, and as a safety net in case a
     * webhook delivery is ever missed in production.
     */
    public function refreshStatus(\App\Models\TrxXenditPayment $payment): \App\Models\TrxXenditPayment
    {
        if (!$payment->isPending() || !$payment->xendit_payment_request_id) {
            return $payment;
        }

        if ($payment->isExpired()) {
            $payment->update(['status' => 'EXPIRED']);
            return $payment;
        }

        $response = $this->client()->get('/v3/payment_requests/' . $payment->xendit_payment_request_id);

        if (!$response->successful()) {
            return $payment;
        }

        $data = $response->json();
        $this->applyStatus($payment, $data);

        return $payment->fresh();
    }

    /**
     * Apply a Xendit payment_request payload (from webhook or a status
     * refresh) to our stored payment + order, idempotently.
     */
    public function applyStatus(\App\Models\TrxXenditPayment $payment, array $data): void
    {
        $status = $this->mapStatus($data['status'] ?? $payment->status);

        if ($payment->status === $status) {
            return;
        }

        $payment->update([
            'status' => $status,
            'paid_at' => $status === 'SUCCEEDED' ? now() : $payment->paid_at,
            'response_payload' => $data,
        ]);

        if ($status === 'SUCCEEDED') {
            $payment->order()->update(['payment_status' => 'PAID']);
        }
    }

    public function verifyCallbackToken(?string $token): bool
    {
        $expected = config('xendit.callback_token');

        return filled($expected) && filled($token) && hash_equals($expected, $token);
    }

    private function client()
    {
        return Http::baseUrl(config('xendit.base_url'))
            ->withBasicAuth(config('xendit.secret_key'), '')
            ->withHeaders([
                'api-version' => config('xendit.api_version'),
                'Content-Type' => 'application/json',
            ]);
    }
}
