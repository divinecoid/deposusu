<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MdxProduct;
use App\Models\User;

class MdxProductDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(MdxProduct::class, 'product_id');
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
}
