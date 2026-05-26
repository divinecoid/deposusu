<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdxCustomer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'phone', 'address', 'area_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function area()
    {
        return $this->belongsTo(MdxArea::class, 'area_id');
    }
}
