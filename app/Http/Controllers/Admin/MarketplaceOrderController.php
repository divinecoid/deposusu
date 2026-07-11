<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrxOrder;

class MarketplaceOrderController extends Controller
{
    public function index()
    {
        $orders = TrxOrder::whereIn('source', ['shopee', 'tokopedia', 'tiktok'])
                    ->latest()
                    ->paginate(15);
                    
        return view('admin.marketplace.order-sync', compact('orders'));
    }
}
