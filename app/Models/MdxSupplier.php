<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdxSupplier extends Model
{
    protected $table = 'mdx_suppliers';

    protected $fillable = [
        'name', 'code', 'contact_person', 'phone', 'email', 'address', 'city',
        'bank_name', 'bank_account', 'bank_account_name',
        'credit_limit', 'notes', 'is_active',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'credit_limit' => 'decimal:2',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(TrxPurchaseOrder::class, 'supplier_id');
    }

    public function getTotalHutangAttribute(): float
    {
        return $this->purchaseOrders()
            ->where('payment_status', '!=', 'paid')
            ->sum(\DB::raw('total_amount - paid_amount'));
    }
}
