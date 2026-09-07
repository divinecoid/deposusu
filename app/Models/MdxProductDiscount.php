<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MdxProduct;
use App\Models\User;

class MdxProductDiscount extends Model
{
    use HasFactory;

    const SCOPE_PRODUCT = 'PRODUCT';
    const SCOPE_CATEGORY = 'CATEGORY';

    protected $fillable = [
        'product_id',
        'scope',
        'category_id',
        'discount_type',
        'discount_value',
        'minimum_quantity',
        'label',
        'start_date',
        'end_date',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'minimum_quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(MdxProduct::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(MdxCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for active discounts based on current time.
     */
    public function scopeActive($query)
    {
        $today = now()->toDateString();
        return $query->where('is_active', true)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);
    }

    /**
     * Price for a given quantity of the product this discount applies to,
     * respecting minimum_quantity ("minimum beli") — returns the original
     * price unchanged if the quantity doesn't qualify.
     */
    public function priceFor(float $originalPrice, int $quantity): float
    {
        if ($this->minimum_quantity && $quantity < $this->minimum_quantity) {
            return $originalPrice;
        }

        if ($this->discount_type === 'PERCENTAGE') {
            return $originalPrice * (1 - ($this->discount_value / 100));
        }

        return max(0, $originalPrice - $this->discount_value);
    }

    public function qualifiesForQuantity(int $quantity): bool
    {
        return !$this->minimum_quantity || $quantity >= $this->minimum_quantity;
    }
}
