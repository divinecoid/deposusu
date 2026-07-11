<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MdxProduct;

use App\Models\MarketplaceSyncLog;

class MarketplaceSyncController extends Controller
{
    public function products()
    {
        $products = MdxProduct::paginate(10);
        return view('admin.marketplace.product-sync', compact('products'));
    }

    public function inventory()
    {
        $logs = MarketplaceSyncLog::with(['product', 'store'])->latest()->paginate(15);
        return view('admin.marketplace.inventory-sync', compact('logs'));
    }
}
