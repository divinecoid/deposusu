<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdxProduct extends Model
{
    use HasFactory;

    protected $table = 'mdx_products';

    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
        'stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Relationship with cart items
     */
    public function cartItems()
    {
        return $this->hasMany(TrxCartItem::class, 'product_id');
    }
}
