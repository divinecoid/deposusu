<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketplaceStore;

class MarketplaceConnectionController extends Controller
{
    public function index()
    {
        $stores = MarketplaceStore::all();
        
        if ($stores->isEmpty()) {
            MarketplaceStore::insert([
                ['platform_name' => 'shopee', 'store_name' => 'Deposusu Official Shopee', 'status' => 'disconnected', 'created_at' => now(), 'updated_at' => now()],
                ['platform_name' => 'tokopedia', 'store_name' => 'Deposusu Tokopedia', 'status' => 'disconnected', 'created_at' => now(), 'updated_at' => now()],
                ['platform_name' => 'tiktok', 'store_name' => 'Deposusu TikTok Shop', 'status' => 'disconnected', 'created_at' => now(), 'updated_at' => now()],
            ]);
            $stores = MarketplaceStore::all();
        }

        return view('admin.marketplace.connection', compact('stores'));
    }

    public function connect($id)
    {
        $store = MarketplaceStore::findOrFail($id);
        $store->update(['status' => 'connected', 'last_sync' => now()]);
        return back()->with('success', 'Berhasil menghubungkan akun ' . ucfirst($store->platform_name));
    }

    public function disconnect($id)
    {
        $store = MarketplaceStore::findOrFail($id);
        $store->update(['status' => 'disconnected']);
        return back()->with('success', 'Berhasil memutuskan akun ' . ucfirst($store->platform_name));
    }
}
