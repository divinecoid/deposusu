<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdxStockMovement extends Model
{
    protected $fillable = [
        'product_id', 'warehouse_id', 'to_warehouse_id',
        'type', 'quantity', 'stock_before', 'stock_after',
        'reference', 'notes', 'user_id'
    ];

    public function product()
    {
        return $this->belongsTo(MdxProduct::class, 'product_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(MdxWarehouse::class, 'warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(MdxWarehouse::class, 'to_warehouse_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'IN' => 'Barang Masuk',
            'OUT' => 'Barang Keluar',
            'TRANSFER' => 'Transfer',
            'OPNAME' => 'Stock Opname',
            'RETURN' => 'Retur',
            default => $this->type,
        };
    }

    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'IN' => 'bg-green-100 text-green-800',
            'OUT' => 'bg-red-100 text-red-800',
            'TRANSFER' => 'bg-blue-100 text-blue-800',
            'OPNAME' => 'bg-yellow-100 text-yellow-800',
            'RETURN' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
