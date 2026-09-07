<?php

namespace App\Observers;

use App\Models\TrxComplaint;
use App\Notifications\ComplaintStatusNotification;

class TrxComplaintObserver
{
    public function created(TrxComplaint $complaint): void
    {
        $complaint->customer?->notify(new ComplaintStatusNotification(
            $complaint,
            "Komplain #{$complaint->id}",
            'Komplain Anda telah kami terima dan sedang kami tinjau.',
        ));
    }

    public function updated(TrxComplaint $complaint): void
    {
        if (!$complaint->wasChanged('status')) {
            return;
        }

        $messages = [
            'in_progress' => 'Komplain Anda sedang kami tindak lanjuti.',
            'resolved' => 'Komplain Anda telah diselesaikan. ' . ($complaint->resolution_note ?? ''),
        ];

        $body = $messages[$complaint->status] ?? null;

        if ($body) {
            $complaint->customer?->notify(new ComplaintStatusNotification(
                $complaint,
                "Komplain #{$complaint->id}",
                trim($body),
            ));
        }
    }
}
