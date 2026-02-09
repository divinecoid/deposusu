<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrxOrder extends Model
{
    use HasFactory;

    protected $table = 'trx_orders';

    protected $fillable = [
        'order_number',
        'customer_name',
        'total_amount',
        'status',          // pending, onprocess, ondelivery, delivered, done, cancelled, rejected
        'payment_status',  // UNPAID, PAID, CANCELLED
        'driver_id',
        'warehouse_id',
    ];

    public function invoice()
    {
        return $this->hasOne(TrxInvoice::class, 'order_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(MdxWarehouse::class);
    }

    public function items()
    {
        return $this->hasMany(TrxOrderItem::class, 'order_id');
    }
}

