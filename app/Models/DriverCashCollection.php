<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverCashCollection extends Model
{
    protected $fillable = [
        'driver_id',
        'order_id',
        'amount',
        'collected_at',
        'is_deposited',
        'deposited_at',
        'confirmed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'collected_at' => 'datetime',
        'is_deposited' => 'boolean',
        'deposited_at' => 'datetime',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function order()
    {
        return $this->belongsTo(TrxOrder::class, 'order_id');
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
