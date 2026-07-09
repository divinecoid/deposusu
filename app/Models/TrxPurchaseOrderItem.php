<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxPurchaseOrderItem extends Model
{
    protected $table = 'trx_purchase_order_items';

    protected $fillable = [
        'purchase_order_id', 'product_id', 'product_name',
        'unit', 'quantity', 'received_qty', 'unit_price', 'subtotal',
    ];

    protected $casts = [
        'quantity'     => 'decimal:2',
        'received_qty' => 'decimal:2',
        'unit_price'   => 'decimal:2',
        'subtotal'     => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(TrxPurchaseOrder::class, 'purchase_order_id');
    }

    public function product()
    {
        return $this->belongsTo(MdxProduct::class, 'product_id');
    }
}
