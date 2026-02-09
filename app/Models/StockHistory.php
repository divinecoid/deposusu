<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'old_stock',
        'new_stock',
        'difference',
        'type',
        'reference',
        'user_id',
    ];

    public function product()
    {
        return $this->belongsTo(MdxProduct::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
