<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrxCartItem extends Model
{
    use HasFactory;

    protected $table = 'trx_cart_items';

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
        'is_routine',
        'routine_schedule',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_routine' => 'boolean',
        'routine_schedule' => 'array',
    ];

    /**
     * Relationships
     */
    public function cart()
    {
        return $this->belongsTo(TrxCart::class, 'cart_id');
    }

    public function product()
    {
        return $this->belongsTo(MdxProduct::class, 'product_id');
    }

    /**
     * Get subtotal for this cart item
     */
    public function getSubtotal()
    {
        return $this->quantity * $this->price;
    }

    /**
     * Accessor for subtotal
     */
    public function getSubtotalAttribute()
    {
        return $this->getSubtotal();
    }
}
