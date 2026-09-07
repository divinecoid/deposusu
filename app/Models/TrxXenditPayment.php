<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxXenditPayment extends Model
{
    protected $table = 'trx_xendit_payments';

    protected $fillable = [
        'order_id',
        'channel_code',
        'reference_id',
        'xendit_payment_request_id',
        'gross_amount',
        'convenience_fee',
        'total_amount',
        'status',
        'qr_string',
        'virtual_account_number',
        'virtual_account_bank',
        'checkout_url',
        'expires_at',
        'paid_at',
        'request_payload',
        'response_payload',
        'webhook_payload',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'convenience_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'webhook_payload' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(TrxOrder::class, 'order_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    public function isSucceeded(): bool
    {
        return $this->status === 'SUCCEEDED';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast() && $this->isPending();
    }
}
