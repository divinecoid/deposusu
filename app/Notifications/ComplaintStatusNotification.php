<?php

namespace App\Notifications;

use App\Models\TrxComplaint;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Notifications\Notification;

class ComplaintStatusNotification extends Notification
{
    public function __construct(
        public TrxComplaint $complaint,
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
            'complaint_id' => $this->complaint->id,
            'order_id' => $this->complaint->order_id,
        ];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'data' => ['complaint_id' => $this->complaint->id],
        ];
    }
}
