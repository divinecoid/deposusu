<?php

namespace App\Observers;

use App\Enums\OrderStatusEnum;
use App\Models\TrxOrder;
use App\Notifications\OrderStatusNotification;

/**
 * Fires customer notifications from one place regardless of which app
 * changed the order (admin panel, driver app, preparist app, Xendit
 * webhook, or the subscription generator) — a single source of truth
 * instead of duplicating "notify the customer" calls across every
 * controller that can move an order forward.
 */
class TrxOrderObserver
{
    /** @var array<string,string> */
    private const STATUS_MESSAGES = [
        'onprocess' => 'Pesanan Anda telah kami terima dan sedang diproses.',
        'prepared' => 'Pesanan Anda sudah dikemas dan menunggu kurir.',
        'ondelivery' => 'Pesanan Anda sedang dalam pengantaran.',
        'delivered' => 'Pesanan Anda telah tiba. Terima kasih!',
        'partialdelivered' => 'Sebagian pesanan Anda telah diantar.',
        'cancelled' => 'Pesanan Anda dibatalkan.',
        'rejected' => 'Pesanan Anda ditolak.',
    ];

    public function created(TrxOrder $order): void
    {
        $order->customer?->notify(new OrderStatusNotification(
            $order,
            "Pesanan #{$order->order_number}",
            'Pesanan Anda telah kami terima dan sedang diproses.',
        ));
    }

    public function updated(TrxOrder $order): void
    {
        $customer = $order->customer;

        if (!$customer) {
            return;
        }

        if ($order->wasChanged('status')) {
            $status = $order->status instanceof OrderStatusEnum ? $order->status->value : $order->status;
            $body = self::STATUS_MESSAGES[$status] ?? null;

            if ($body) {
                $customer->notify(new OrderStatusNotification(
                    $order,
                    "Pesanan #{$order->order_number}",
                    $body,
                ));
            }
        }

        if ($order->wasChanged('payment_status') && $order->payment_status === 'PAID') {
            $customer->notify(new OrderStatusNotification(
                $order,
                "Pesanan #{$order->order_number}",
                'Pembayaran Anda telah kami terima.',
            ));
        }
    }
}
