<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdxBranch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'address'];

    public function areas()
    {
        return $this->hasMany(MdxArea::class, 'branch_id');
    }
}
