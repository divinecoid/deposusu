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
     * Per-instance memoization for the resolved active discount.
     */
    protected $discountCache = [];

    /**
     * Wishlisted product ids per user, cached for the lifetime of the request.
     */
    protected static $wishlistIdCache = [];

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
        if (array_key_exists('active_discount', $this->discountCache)) {
            return $this->discountCache['active_discount'];
        }

        // Reuse the eager-loaded `discounts` relation when available so that
        // rendering a product grid does not fire one query per product.
        if ($this->relationLoaded('discounts')) {
            $today = now()->startOfDay();

            $discount = $this->discounts
                ->filter(function ($discount) use ($today) {
                    return $discount->is_active
                        && $discount->start_date
                        && $discount->end_date
                        && $today->gte($discount->start_date->copy()->startOfDay())
                        && $today->lte($discount->end_date->copy()->startOfDay());
                })
                ->sortByDesc('id')
                ->first();
        } else {
            $discount = $this->discounts()->active()->latest()->first();
        }

        return $this->discountCache['active_discount'] = $discount;
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

        if ($this->relationLoaded('wishlists')) {
            return $this->wishlists->contains('user_id', $user->id);
        }

        // Cache the whole wishlist once per request: product grids call this
        // for every card and would otherwise fire one query each.
        if (!array_key_exists($user->id, static::$wishlistIdCache)) {
            static::$wishlistIdCache[$user->id] = Wishlist::where('user_id', $user->id)
                ->pluck('product_id')
                ->all();
        }

        return in_array($this->id, static::$wishlistIdCache[$user->id]);
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
