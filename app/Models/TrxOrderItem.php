<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrxOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'original_price',
        'quantity',
        'price',
        'discount_amount',
        'discount_id',
        'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(TrxOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(MdxProduct::class);
    }
}
