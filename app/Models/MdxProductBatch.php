<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdxProductBatch extends Model
{
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'expiry_date',
        'quantity',
        'initial_quantity',
        'rack_location',
        'reference',
        'received_at',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'received_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(MdxProduct::class, 'product_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(MdxWarehouse::class, 'warehouse_id');
    }

    /** Days until expiry — negative once past due. */
    public function daysUntilExpiry(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->expiry_date->startOfDay(), false);
    }

    public function expiryStatus(): string
    {
        $days = $this->daysUntilExpiry();

        if ($days < 0) {
            return 'expired';
        }

        if ($days <= 30) {
            return 'near_expiry';
        }

        return 'safe';
    }
}
