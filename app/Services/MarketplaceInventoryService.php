<?php

namespace App\Services;

use App\Models\MarketplaceProductMap;
use App\Models\MarketplaceSyncLog;
use App\Models\MdxProduct;
use Illuminate\Support\Facades\Log;

class MarketplaceInventoryService
{
    /**
     * Calculate and sync stock to marketplace based on Local SSOT.
     * Available Stock = Physical - Reserved - Safety Stock
     */
    public function syncProductStock(MarketplaceProductMap $map, $syncType = 'event')
    {
        if (!$map->sync_stock) {
            return false;
        }

        $product = $map->product;
        if (!$product) {
            return false;
        }

        // Logic for SSOT (Single Source of Truth) from Deposusu Inventory
        // This is a mockup assuming MdxProduct has a method or attribute for calculating real stock
        // For demonstration, we'll assume $product->stock is physical, and reserved is 0
        $physicalStock = $product->stock ?? 0; 
        $reservedStock = 0; // Ideally: TrxOrder::where('status', 'processing')->whereHas('items', function($q) use($product) { $q->where('mdx_product_id', $product->id); })->sum('qty');
        
        $availableStock = $physicalStock - $reservedStock - $map->safety_stock;

        // Auto disable / 0 if negative
        $stockToPush = $availableStock > 0 ? $availableStock : 0;

        // Determine stock before (for log)
        // In real app, we might query the marketplace API or store the last known stock locally.
        // For simulation, we'll just say before was whatever it was in log or 0
        $lastLog = MarketplaceSyncLog::where('marketplace_store_id', $map->marketplace_store_id)
            ->where('mdx_product_id', $map->mdx_product_id)
            ->latest()
            ->first();
            
        $stockBefore = $lastLog ? $lastLog->stock_after : 0;

        // Simulated API Call to Marketplace (Shopee/Tokped/Tiktok)
        $apiSuccess = $this->mockPushToMarketplace($map, $stockToPush);

        // Record the Sync Log
        MarketplaceSyncLog::create([
            'marketplace_store_id' => $map->marketplace_store_id,
            'mdx_product_id' => $product->id,
            'stock_before' => $stockBefore,
            'stock_after' => $stockToPush,
            'status' => $apiSuccess ? 'success' : 'failed',
            'sync_type' => $syncType,
        ]);

        return $apiSuccess;
    }

    private function mockPushToMarketplace(MarketplaceProductMap $map, $stockToPush)
    {
        // Simulate network delay and success
        // In a real scenario, use Guzzle to hit Shopee/Tokopedia API
        Log::info("Marketplace API (Mock): Pushing stock {$stockToPush} for SKU {$map->marketplace_sku} to Store {$map->marketplace_store_id}");
        return true; 
    }
}
