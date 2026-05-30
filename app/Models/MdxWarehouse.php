<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdxWarehouse extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address'];

    public function racks()
    {
        return $this->hasMany(MdxRack::class, 'warehouse_id');
    }

    public function orders()
    {
        return $this->hasMany(TrxOrder::class);
    }

    public function warehouseStocks()
    {
        return $this->hasMany(MdxWarehouseStock::class, 'warehouse_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(MdxStockMovement::class, 'warehouse_id');
    }
}
