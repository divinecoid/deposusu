<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use App\Models\MdxProduct;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = TrxOrder::count();
        $pendingOrders = TrxOrder::where('status', OrderStatusEnum::PENDING)->count();
        $totalProducts = MdxProduct::count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Recent Orders
        $recentOrders = TrxOrder::with('driver', 'warehouse')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalOrders',
            'pendingOrders',
            'totalProducts',
            'totalCustomers',
            'recentOrders'
        ));
    }
}
