<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdxWarehouseStock extends Model
{
    protected $fillable = ['warehouse_id', 'product_id', 'quantity', 'min_stock', 'rack_location'];

    public function warehouse()
    {
        return $this->belongsTo(MdxWarehouse::class, 'warehouse_id');
    }

    public function product()
    {
        return $this->belongsTo(MdxProduct::class, 'product_id');
    }
}
