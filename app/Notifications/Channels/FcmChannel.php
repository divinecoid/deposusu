<?php

namespace App\Notifications\Channels;

use App\Services\FcmService;
use Illuminate\Notifications\Notification;

class FcmChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $token = $notifiable->fcm_token ?? null;

        if (!$token || !method_exists($notification, 'toFcm')) {
            return;
        }

        app(FcmService::class)->send($token, $notification->toFcm($notifiable));
    }
}
