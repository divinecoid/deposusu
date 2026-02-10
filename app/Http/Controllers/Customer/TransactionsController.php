<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $orders = TrxOrder::with(['items.product', 'invoice'])
            ->where('customer_name', auth()->user()->name)
            ->latest()
            ->paginate(10);

        return view('customer.transactions.index', compact('orders'));
    }

    public function show(TrxOrder $order)
    {
        if ($order->customer_name !== auth()->user()->name) {
            abort(404);
        }

        $order->load(['items.product', 'invoice', 'driver', 'warehouse']);

        return view('customer.transactions.show', compact('order'));
    }
}
