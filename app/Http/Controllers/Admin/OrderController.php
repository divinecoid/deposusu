<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use App\Models\User;
use App\Models\MdxDriver;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = TrxOrder::with(['driver', 'items.product']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(10);

        $counts = [
            'all' => TrxOrder::count(),
            'pending' => TrxOrder::where('status', 'pending')->count(),
            'onprocess' => TrxOrder::where('status', 'onprocess')->count(),
            'ondelivery' => TrxOrder::where('status', 'ondelivery')->count(),
            'delivered' => TrxOrder::where('status', 'delivered')->count(),
            'partialdelivered' => TrxOrder::where('status', 'partialdelivered')->count(),
            'done' => TrxOrder::where('status', 'done')->count(),
            'cancelled' => TrxOrder::where('status', 'cancelled')->count(),
            'rejected' => TrxOrder::where('status', 'rejected')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'status', 'counts'));
    }

    public function show(TrxOrder $order)
    {
        $order->load(['items.product', 'driver', 'warehouse', 'invoice']);
        $drivers = User::where('role', 'driver')->get(); // Assuming users with role driver

        return view('admin.orders.show', compact('order', 'drivers'));
    }

    public function updateStatus(Request $request, TrxOrder $order)
    {
        $request->validate([
            'status' => 'required|in:pending,onprocess,ondelivery,delivered,partialdelivered,done,cancelled,rejected',
            'driver_id' => 'nullable|exists:users,id',
        ]);

        $order->status = $request->status;

        if ($request->has('driver_id')) {
            $order->driver_id = $request->driver_id;
        }

        $order->save();

        // Check if invoice exists, if not create one when order is confirmed (e.g. onprocess)
        if ($order->status === 'onprocess' && !$order->invoice) {
            $this->createInvoice($order);
        }

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Order status updated successfully.');
    }

    private function createInvoice(TrxOrder $order)
    {
        $order->invoice()->create([
            'invoice_number' => 'INV-' . $order->order_number,
            'issue_date' => now(),
            'due_date' => now()->addDays(7), // Example due date
            'status' => 'UNPAID',
            'total_amount' => $order->total_amount,
        ]);
    }
}
