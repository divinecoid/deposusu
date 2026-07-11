<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceSyncLog extends Model
{
    protected $fillable = [
        'marketplace_store_id',
        'mdx_product_id',
        'stock_before',
        'stock_after',
        'status',
        'sync_type',
    ];

    public function store()
    {
        return $this->belongsTo(MarketplaceStore::class, 'marketplace_store_id');
    }

    public function product()
    {
        return $this->belongsTo(MdxProduct::class, 'mdx_product_id');
    }
}
