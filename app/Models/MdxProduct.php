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

    public function categories()
    {
        return $this->belongsToMany(MdxCategory::class, 'mdx_category_product', 'mdx_product_id', 'mdx_category_id');
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

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }

    public function isWishlistedBy($user)
    {
        if (!$user)
            return false;
        return $this->wishlists()->where('user_id', $user->id)->exists();
    }

    public function variants()
    {
        return $this->hasMany(MdxProductVariant::class, 'mdx_product_id');
    }

    public function wholesales()
    {
        return $this->hasMany(MdxProductWholesale::class, 'mdx_product_id');
    }
}
