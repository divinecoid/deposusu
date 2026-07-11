<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use App\Models\TrxOrderItem;
use App\Models\MdxProduct;
use App\Models\User;
use App\Models\FinanceTransaction;
use App\Models\MdxWarehouse;
use App\Models\MdxWarehouseStock;
use App\Models\MdxStockMovement;
use App\Models\TrxPurchaseOrder;
use App\Models\MdxSupplier;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    private function getReportData($tab, $startDate, $endDate, $warehouseId = null)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->endOfDay();

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'tab' => $tab,
            'warehouseId' => $warehouseId,
        ];

        // ─── FINANCIAL REPORT ─────────────────────────────────
        if ($tab === 'finance') {
            // Revenue breakdown
            $revenueQuery = TrxOrder::whereBetween('created_at', [$start, $end])
                ->whereIn('status', [OrderStatusEnum::DONE, OrderStatusEnum::DELIVERED]);
            if ($warehouseId) {
                $revenueQuery->where('warehouse_id', $warehouseId);
            }
            $data['totalRevenue'] = $revenueQuery->sum('total_amount');

            // Expense breakdown
            $expenseQuery = FinanceTransaction::whereBetween('transaction_date', [$startDate, $endDate])
                ->where('type', 'expense');
            $data['totalExpense'] = $expenseQuery->sum('amount');
            $data['expensesByCategory'] = (clone $expenseQuery)
                ->select('category', DB::raw('SUM(amount) as total'))
                ->groupBy('category')
                ->get();

            // COGS (Modal Barang)
            $cogsQuery = DB::table('trx_order_items')
                ->join('trx_orders', 'trx_order_items.order_id', '=', 'trx_orders.id')
                ->join('mdx_products', 'trx_order_items.product_id', '=', 'mdx_products.id')
                ->whereBetween('trx_orders.created_at', [$start, $end])
                ->whereIn('trx_orders.status', [OrderStatusEnum::DONE->value, OrderStatusEnum::DELIVERED->value]);
            if ($warehouseId) {
                $cogsQuery->where('trx_orders.warehouse_id', $warehouseId);
            }
            $data['totalModal'] = $cogsQuery->sum(DB::raw('mdx_products.cost_price * trx_order_items.quantity'));

            $data['grossProfit'] = $data['totalRevenue'] - $data['totalModal'];
            $data['netProfit'] = $data['grossProfit'] - $data['totalExpense'];

            // Piutang Customer (Accounts Receivable)
            $data['piutangList'] = TrxOrder::where('payment_status', 'UNPAID')
                ->where('payment_method', 'piutang')
                ->latest()
                ->take(15)
                ->get();
            $data['totalPiutang'] = TrxOrder::where('payment_status', 'UNPAID')
                ->where('payment_method', 'piutang')
                ->sum('total_amount');

            // Hutang Supplier (Accounts Payable)
            $data['hutangList'] = TrxPurchaseOrder::with('supplier')
                ->whereIn('payment_status', ['unpaid', 'partial'])
                ->latest()
                ->take(15)
                ->get();
            $data['totalHutang'] = TrxPurchaseOrder::whereIn('payment_status', ['unpaid', 'partial'])
                ->sum(DB::raw('total_amount - paid_amount'));
        }

        // ─── SALES REPORT ─────────────────────────────────────
        if ($tab === 'sales') {
            $salesQuery = TrxOrder::whereBetween('created_at', [$start, $end])
                ->whereIn('status', [OrderStatusEnum::DONE, OrderStatusEnum::DELIVERED]);
            if ($warehouseId) {
                $salesQuery->where('warehouse_id', $warehouseId);
            }

            $data['totalSalesCount'] = (clone $salesQuery)->count();
            $data['totalSalesAmount'] = (clone $salesQuery)->sum('total_amount');

            // Sales by channel
            $data['salesByChannel'] = (clone $salesQuery)
                ->select('source', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
                ->groupBy('source')
                ->get();

            // Daily Sales Breakdown
            $data['dailySales'] = (clone $salesQuery)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            // Top Products
            $data['topProducts'] = DB::table('trx_order_items')
                ->join('trx_orders', 'trx_order_items.order_id', '=', 'trx_orders.id')
                ->join('mdx_products', 'trx_order_items.product_id', '=', 'mdx_products.id')
                ->whereBetween('trx_orders.created_at', [$start, $end])
                ->whereIn('trx_orders.status', ['done', 'delivered'])
                ->select('mdx_products.name', DB::raw('SUM(trx_order_items.quantity) as qty_sold'), DB::raw('SUM(trx_order_items.subtotal) as total_rev'))
                ->groupBy('mdx_products.id', 'mdx_products.name')
                ->orderByDesc('qty_sold')
                ->take(10)
                ->get();
        }

        // ─── STOCK REPORT ─────────────────────────────────────
        if ($tab === 'stock') {
            $stockQuery = MdxWarehouseStock::with(['product', 'warehouse']);
            if ($warehouseId) {
                $stockQuery->where('warehouse_id', $warehouseId);
            }
            $data['stocks'] = $stockQuery->get();

            // Stock movements
            $movementQuery = MdxStockMovement::with(['product', 'warehouse', 'toWarehouse'])
                ->whereBetween('created_at', [$start, $end]);
            if ($warehouseId) {
                $movementQuery->where(function ($q) use ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId)
                      ->orWhere('to_warehouse_id', $warehouseId);
                });
            }
            $data['movements'] = $movementQuery->latest()->take(20)->get();

            // Expired stock tracking
            $data['lowStockProducts'] = MdxProduct::where('stock', '<=', 10)->orderBy('stock', 'asc')->get();
        }

        // ─── CUSTOMER REPORT ──────────────────────────────────
        if ($tab === 'customer') {
            $custQuery = TrxOrder::whereBetween('created_at', [$start, $end])
                ->whereIn('status', [OrderStatusEnum::DONE, OrderStatusEnum::DELIVERED]);

            $data['topCustomers'] = (clone $custQuery)
                ->select('customer_name', 'customer_phone', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(total_amount) as total_spend'))
                ->groupBy('customer_name', 'customer_phone')
                ->orderByDesc('total_spend')
                ->take(15)
                ->get();

            $data['newCustomersCount'] = User::where('role', 'customer')
                ->whereBetween('created_at', [$start, $end])
                ->count();
            
            $data['totalCustomersCount'] = User::where('role', 'customer')->count();
        }

        // ─── OPERATIONAL REPORT ───────────────────────────────
        if ($tab === 'operational') {
            $opQuery = TrxOrder::whereBetween('created_at', [$start, $end]);
            if ($warehouseId) {
                $opQuery->where('warehouse_id', $warehouseId);
            }

            $data['totalOrders'] = (clone $opQuery)->count();
            $data['completedOrders'] = (clone $opQuery)->where('status', OrderStatusEnum::DONE)->count();
            $data['processingOrders'] = (clone $opQuery)->whereIn('status', [OrderStatusEnum::ON_PROCESS, OrderStatusEnum::PENDING])->count();
            
            // Driver/Delivery performance
            $data['deliveries'] = (clone $opQuery)
                ->whereNotNull('driver_id')
                ->select('driver_id', DB::raw('COUNT(*) as total_deliveries'), DB::raw('SUM(CASE WHEN status="done" THEN 1 ELSE 0 END) as completed_deliveries'))
                ->groupBy('driver_id')
                ->with('driver')
                ->get();
        }

        return $data;
    }

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'finance');
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->query('end_date', now()->endOfMonth()->toDateString());
        $warehouseId = $request->query('warehouse_id');

        $warehouses = MdxWarehouse::all();
        $reportData = $this->getReportData($tab, $startDate, $endDate, $warehouseId);

        return view('admin.reports.index', array_merge([
            'warehouses' => $warehouses,
            'selectedWarehouseId' => $warehouseId,
        ], $reportData));
    }

    public function exportPdf(Request $request)
    {
        $tab = $request->query('tab', 'finance');
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->query('end_date', now()->endOfMonth()->toDateString());
        $warehouseId = $request->query('warehouse_id');

        $reportData = $this->getReportData($tab, $startDate, $endDate, $warehouseId);
        
        $warehouseName = 'Semua Gudang';
        if ($warehouseId) {
            $w = MdxWarehouse::find($warehouseId);
            if ($w) {
                $warehouseName = $w->name;
            }
        }
        $reportData['warehouseName'] = $warehouseName;

        $pdf = Pdf::loadView('admin.reports.pdf', $reportData);
        $filename = "Laporan-" . ucfirst($tab) . "-" . $startDate . "-to-" . $endDate . ".pdf";
        return $pdf->download($filename);
    }
}
