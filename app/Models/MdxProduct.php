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

    protected $appends = [
        'active_discount',
        'discounted_price',
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

    /**
     * Relationship with historical discounts
     */
    public function discounts()
    {
        return $this->hasMany(MdxProductDiscount::class, 'product_id');
    }

    /**
     * Get the currently active discount
     */
    public function getActiveDiscountAttribute()
    {
        return $this->discounts()->active()->latest()->first();
    }

    /**
     * Get discounted price if an active discount exists
     */
    public function getDiscountedPriceAttribute()
    {
        $discount = $this->active_discount;

        if (!$discount) {
            return $this->price;
        }

        if ($discount->discount_type === 'PERCENTAGE') {
            return $this->price * (1 - ($discount->discount_value / 100));
        }

        // FIXED discount
        return max(0, $this->price - $discount->discount_value);
    }
}
