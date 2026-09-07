<?php

namespace App\Notifications;

use App\Models\TrxOrder;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    public function __construct(
        public TrxOrder $order,
        public string $title,
        public string $body,
    ) {
    }

    public function via($notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
        ];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'data' => [
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number,
            ],
        ];
    }
}
