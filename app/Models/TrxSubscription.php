<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxSubscription extends Model
{
    protected $fillable = [
        'customer_id',
        'product_id',
        'quantity',
        'days_of_week',
        'shipping_address',
        'payment_method',
        'is_active',
        'last_generated_date',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'is_active' => 'boolean',
        'last_generated_date' => 'date',
        'quantity' => 'float',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(MdxProduct::class, 'product_id');
    }

    public function isDueOn(string $indonesianDayName): bool
    {
        return $this->is_active && in_array($indonesianDayName, $this->days_of_week ?? [], true);
    }
}
