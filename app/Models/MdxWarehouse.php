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
        return $this->hasMany(MdxRack::class);
    }

    public function orders()
    {
        return $this->hasMany(TrxOrder::class);
    }
}
