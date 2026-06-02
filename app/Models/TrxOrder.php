<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
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
        'total_discount',
        'status',          // OrderStatusEnum
        'payment_status',  // UNPAID, PAID, CANCELLED
        'driver_id',
        'warehouse_id',
        'preparist_id',
        'source',
        'picked_up_at',
        'delivered_at',
        'delivery_proof_photo',
        'recipient_name',
        'recipient_signature',
        'delivery_latitude',
        'delivery_longitude',
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'on_preparation_at' => 'datetime',
        'prepared_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'delivery_latitude' => 'float',
        'delivery_longitude' => 'float',
    ];

    protected $appends = [
        'customer_phone',
        'customer_address',
        'distance',
        'deadline',
        'order_source',
    ];

    public function getCustomerPhoneAttribute()
    {
        $customer = $this->customer;
        return $customer && $customer->customerProfile ? $customer->customerProfile->phone : '';
    }

    public function getCustomerAddressAttribute()
    {
        $customer = $this->customer;
        return $customer && $customer->customerProfile ? $customer->customerProfile->address : '';
    }

    public function getDistanceAttribute()
    {
        // Stable mock distance based on ID for demo purposes, e.g., between 0.8 and 5.0 km
        return round(0.8 + (($this->id * 7) % 43) / 10, 1);
    }

    public function getDeadlineAttribute()
    {
        // Stable deadline (60 minutes after order creation)
        $baseTime = $this->created_at ?: now();
        return $baseTime->addMinutes(60)->toIso8601String();
    }

    public function getOrderSourceAttribute()
    {
        return $this->source ?: 'app';
    }

    public function preparist()
    {
        return $this->belongsTo(User::class, 'preparist_id');
    }

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

    public function getCustomerAttribute()
    {
        return User::where('name', $this->customer_name)
            ->where('role', 'customer')
            ->first();
    }
}

