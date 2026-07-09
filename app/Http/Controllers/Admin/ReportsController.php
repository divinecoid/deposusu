<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use App\Models\TrxOrderItem;
use App\Models\MdxProduct;
use App\Models\User;
use App\Models\FinanceTransaction;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->query('end_date', now()->endOfMonth()->toDateString());

        // Parse dates safely
        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->endOfDay();

        // ─── 1. SALES REPORT ──────────────────────────────────
        $salesQuery = TrxOrder::whereBetween('created_at', [$start, $end])
            ->whereIn('status', [OrderStatusEnum::DONE, OrderStatusEnum::DELIVERED]);

        $totalSalesCount = (clone $salesQuery)->count();
        $totalSalesAmount = (clone $salesQuery)->sum('total_amount');
        $totalSalesDiscount = (clone $salesQuery)->sum('total_discount');

        // Sales by source/channel
        $salesBySource = (clone $salesQuery)
            ->select('source', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('source')
            ->get();

        // ─── 2. PRODUCT REPORTS (FAST & SLOW MOVING) ──────────
        // Top sold products (Fast-moving)
        $topProducts = DB::table('trx_order_items')
            ->join('trx_orders', 'trx_order_items.order_id', '=', 'trx_orders.id')
            ->join('mdx_products', 'trx_order_items.product_id', '=', 'mdx_products.id')
            ->whereBetween('trx_orders.created_at', [$start, $end])
            ->whereIn('trx_orders.status', ['done', 'delivered'])
            ->select('mdx_products.name', 'mdx_products.stock', DB::raw('SUM(trx_order_items.quantity) as qty_sold'), DB::raw('SUM(trx_order_items.subtotal) as total_rev'))
            ->groupBy('mdx_products.id', 'mdx_products.name', 'mdx_products.stock')
            ->orderByDesc('qty_sold')
            ->take(10)
            ->get();

        // Slow moving (products in stock but lowest sold/never sold)
        $slowProducts = MdxProduct::leftJoin('trx_order_items', 'mdx_products.id', '=', 'trx_order_items.product_id')
            ->select('mdx_products.name', 'mdx_products.stock', DB::raw('SUM(CASE WHEN trx_order_items.created_at BETWEEN "' . $start . '" AND "' . $end . '" THEN trx_order_items.quantity ELSE 0 END) as qty_sold'))
            ->groupBy('mdx_products.id', 'mdx_products.name', 'mdx_products.stock')
            ->orderBy('qty_sold', 'asc')
            ->orderByDesc('mdx_products.stock')
            ->take(10)
            ->get();

        // ─── 3. CUSTOMER REPORTS ──────────────────────────────
        // Customer list with order counts & total spend
        $topCustomers = TrxOrder::whereBetween('created_at', [$start, $end])
            ->whereIn('status', [OrderStatusEnum::DONE, OrderStatusEnum::DELIVERED])
            ->select('customer_name', 'customer_phone', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(total_amount) as total_spend'))
            ->groupBy('customer_name', 'customer_phone')
            ->orderByDesc('total_spend')
            ->take(10)
            ->get();

        // ─── 4. FINANCE SUMMARY (REVENUE VS EXPENSE) ──────────
        // Expenses in the period (if finance_transactions exists)
        $totalExpense = 0;
        if (\Schema::hasTable('finance_transactions')) {
            $totalExpense = DB::table('finance_transactions')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->where('type', 'expense')
                ->sum('amount');
        }

        $estimatedProfit = $totalSalesAmount - $totalExpense;

        return view('admin.reports.index', compact(
            'startDate', 'endDate',
            'totalSalesCount', 'totalSalesAmount', 'totalSalesDiscount', 'salesBySource',
            'topProducts', 'slowProducts',
            'topCustomers',
            'totalExpense', 'estimatedProfit'
        ));
    }
}
