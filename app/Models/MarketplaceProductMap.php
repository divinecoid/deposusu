<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceProductMap extends Model
{
    protected $fillable = [
        'marketplace_store_id',
        'mdx_product_id',
        'marketplace_item_id',
        'marketplace_sku',
        'sync_price',
        'sync_stock',
        'safety_stock',
    ];

    protected $casts = [
        'sync_price' => 'boolean',
        'sync_stock' => 'boolean',
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
