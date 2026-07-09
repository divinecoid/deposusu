<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use App\Models\TrxOrder;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $typeFilter = $request->query('type', 'all');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Query transactions
        $query = FinanceTransaction::query();

        if ($typeFilter !== 'all') {
            $query->where('type', $typeFilter);
        }

        if (!empty($startDate)) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(15);

        // Calculate Accounting stats (always dynamic based on DB)
        $totalOmzet = FinanceTransaction::where('type', 'income')->sum('amount');
        
        // Dynamic Cost of Goods Sold (Modal Barang)
        $totalModal = DB::table('trx_order_items')
            ->join('trx_orders', 'trx_order_items.order_id', '=', 'trx_orders.id')
            ->join('mdx_products', 'trx_order_items.product_id', '=', 'mdx_products.id')
            ->whereIn('trx_orders.status', [OrderStatusEnum::DONE->value, OrderStatusEnum::DELIVERED->value])
            ->sum(DB::raw('mdx_products.cost_price * trx_order_items.quantity'));

        $totalExpenses = FinanceTransaction::where('type', 'expense')->sum('amount');
        $totalRefunds = FinanceTransaction::where('type', 'refund')->sum('amount');

        $grossProfit = $totalOmzet - $totalModal;
        $netProfit = $grossProfit - $totalExpenses - $totalRefunds;
        $cashBalance = $totalOmzet - $totalExpenses - $totalRefunds;

        return view('admin.finance.index', compact(
            'transactions',
            'typeFilter',
            'startDate',
            'endDate',
            'totalOmzet',
            'totalModal',
            'grossProfit',
            'netProfit',
            'cashBalance',
            'totalExpenses',
            'totalRefunds'
        ));
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:500',
            'transaction_date' => 'required|date',
        ]);

        FinanceTransaction::create([
            'type' => 'expense',
            'category' => $request->category,
            'amount' => $request->amount,
            'reference_id' => 'EXP-' . rand(10000, 99999),
            'description' => $request->description,
            'transaction_date' => $request->transaction_date,
        ]);

        return redirect()->route('admin.finance.index')
            ->with('success', 'Biaya operasional berhasil dicatat.');
    }
}
