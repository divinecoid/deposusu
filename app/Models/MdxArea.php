<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdxArea extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'branch_id', 'latitude', 'longitude'];

    public function branch()
    {
        return $this->belongsTo(MdxBranch::class, 'branch_id');
    }
}
