<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdxRack extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'warehouse_id'];

    public function warehouse()
    {
        return $this->belongsTo(MdxWarehouse::class);
    }
}
