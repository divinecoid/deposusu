<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxPayment extends Model
{
    protected $table = 'trx_payments';

    protected $fillable = [
        'invoice_id', 'order_id', 'confirmed_by',
        'payment_method', 'amount', 'payment_date',
        'reference_number', 'proof_image', 'status', 'notes',
        'confirmed_at', 'receipt_number', 'company_name', 'payment_purpose',
    ];

    protected $casts = [
        'payment_date'  => 'date',
        'confirmed_at'  => 'datetime',
        'amount'        => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(TrxInvoice::class, 'invoice_id');
    }

    public function order()
    {
        return $this->belongsTo(TrxOrder::class, 'order_id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'transfer' => '🏦 Transfer Bank',
            'cash'     => '💵 Cash',
            'qris'     => '📱 QRIS',
            'wallet'   => '👜 E-Wallet',
            'cod'      => '🚚 COD',
            'piutang'  => '📋 Piutang',
            default    => ucfirst($this->payment_method),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'emerald',
            'rejected' => 'red',
            default    => 'amber',
        };
    }
}
