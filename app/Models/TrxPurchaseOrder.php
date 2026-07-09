<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxPurchaseOrder extends Model
{
    protected $table = 'trx_purchase_orders';

    protected $fillable = [
        'po_number', 'supplier_id', 'created_by',
        'order_date', 'expected_date', 'received_date',
        'subtotal', 'tax', 'total_amount', 'paid_amount',
        'status', 'payment_status', 'payment_due_date', 'notes',
    ];

    protected $casts = [
        'order_date'       => 'date',
        'expected_date'    => 'date',
        'received_date'    => 'date',
        'payment_due_date' => 'date',
        'subtotal'         => 'decimal:2',
        'tax'              => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'paid_amount'      => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(MdxSupplier::class, 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(TrxPurchaseOrderItem::class, 'purchase_order_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float)$this->total_amount - (float)$this->paid_amount;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'received'         => 'emerald',
            'partial_received' => 'blue',
            'ordered'          => 'violet',
            'cancelled'        => 'slate',
            default            => 'amber',
        };
    }
}
