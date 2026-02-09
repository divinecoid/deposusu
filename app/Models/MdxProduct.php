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
        'sku',
        'barcode',
        'category_id',
        'price',
        'description',
        'image',
        'stock',
        'low_stock_threshold',
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
    public function category()
    {
        return $this->belongsTo(MdxCategory::class);
    }
}
