<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use App\Models\TrxInvoice;
use App\Models\TrxPayment;
use App\Models\MdxProduct;
use App\Models\MdxSupplier;
use App\Models\FinanceTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'today');

        // Determine date range
        $now = now();
        switch ($period) {
            case 'week':
                $startDate = $now->copy()->startOfWeek();
                $endDate   = $now->copy()->endOfWeek();
                break;
            case 'month':
                $startDate = $now->copy()->startOfMonth();
                $endDate   = $now->copy()->endOfMonth();
                break;
            default: // today
                $startDate = $now->copy()->startOfDay();
                $endDate   = $now->copy()->endOfDay();
                $period    = 'today';
                break;
        }

        // ─── ORDER METRICS ────────────────────────────────────
        $ordersInPeriod = TrxOrder::whereBetween('created_at', [$startDate, $endDate]);
        $totalOrders    = (clone $ordersInPeriod)->count();
        $pendingOrders  = (clone $ordersInPeriod)->where('status', OrderStatusEnum::PENDING)->count();

        // Revenue per channel
        $revenueOnline = (clone $ordersInPeriod)->where('source', '!=', 'admin')
            ->whereIn('status', [OrderStatusEnum::DONE, OrderStatusEnum::DELIVERED])
            ->sum('total_amount');
        $revenueOffline = (clone $ordersInPeriod)->where('source', 'admin')
            ->whereIn('status', [OrderStatusEnum::DONE, OrderStatusEnum::DELIVERED])
            ->sum('total_amount');
        $totalRevenue = $revenueOnline + $revenueOffline;

        // ─── PAYMENT METRICS ─────────────────────────────────
        $pendingPayments = TrxPayment::where('status', 'pending')->count();
        $pendingPaymentAmount = TrxPayment::where('status', 'pending')->sum('amount');
        $unpaidInvoices = TrxInvoice::where('status', 'UNPAID')->count();

        // ─── PRODUCT / STOCK METRICS ─────────────────────────
        $totalProducts  = MdxProduct::count();
        $lowStockCount  = MdxProduct::where('stock', '<=', 10)->where('stock', '>', 0)->count();
        $outOfStock     = MdxProduct::where('stock', '<=', 0)->count();
        $lowStockProducts = MdxProduct::where('stock', '<=', 10)->orderBy('stock', 'asc')->take(5)->get();

        // ─── CUSTOMER METRICS ────────────────────────────────
        $totalCustomers = User::where('role', 'customer')->count();

        // ─── SUPPLIER METRICS ────────────────────────────────
        $totalSuppliers = MdxSupplier::count();

        // ─── REVENUE CHART (last 7 days) ─────────────────────
        $chartDays = collect();
        for ($i = 6; $i >= 0; $i--) {
            $day  = now()->subDays($i);
            $label = $day->format('d M');
            $dayRevenue = TrxOrder::whereDate('created_at', $day->toDateString())
                ->whereIn('status', [OrderStatusEnum::DONE, OrderStatusEnum::DELIVERED])
                ->sum('total_amount');
            $chartDays->push(['label' => $label, 'revenue' => (float) $dayRevenue]);
        }

        // ─── RECENT ORDERS ──────────────────────────────────
        $recentOrders = TrxOrder::with('driver', 'warehouse')
            ->latest()
            ->take(8)
            ->get();

        // ─── TOP PRODUCTS (by order count) ──────────────────
        $topProducts = DB::table('trx_order_items')
            ->join('mdx_products', 'trx_order_items.product_id', '=', 'mdx_products.id')
            ->select('mdx_products.name', DB::raw('SUM(trx_order_items.quantity) as total_qty'), DB::raw('SUM(trx_order_items.subtotal) as total_revenue'))
            ->groupBy('mdx_products.id', 'mdx_products.name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'period', 'totalOrders', 'pendingOrders',
            'totalRevenue', 'revenueOnline', 'revenueOffline',
            'pendingPayments', 'pendingPaymentAmount', 'unpaidInvoices',
            'totalProducts', 'lowStockCount', 'outOfStock', 'lowStockProducts',
            'totalCustomers', 'totalSuppliers',
            'chartDays', 'recentOrders', 'topProducts'
        ));
    }
}
