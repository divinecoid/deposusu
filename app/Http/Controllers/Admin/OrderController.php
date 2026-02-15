<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use App\Models\User;
use App\Models\MdxDriver;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

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
            'pending' => TrxOrder::where('status', OrderStatusEnum::PENDING)->count(),
            'onprocess' => TrxOrder::where('status', OrderStatusEnum::ON_PROCESS)->count(),
            'onpreparation' => TrxOrder::where('status', OrderStatusEnum::ON_PREPARATION)->count(),
            'prepared' => TrxOrder::where('status', OrderStatusEnum::PREPARED)->count(),
            'ondelivery' => TrxOrder::where('status', OrderStatusEnum::ON_DELIVERY)->count(),
            'delivered' => TrxOrder::where('status', OrderStatusEnum::DELIVERED)->count(),
            'partialdelivered' => TrxOrder::where('status', OrderStatusEnum::PARTIAL_DELIVERED)->count(),
            'done' => TrxOrder::where('status', OrderStatusEnum::DONE)->count(),
            'cancelled' => TrxOrder::where('status', OrderStatusEnum::CANCELLED)->count(),
            'rejected' => TrxOrder::where('status', OrderStatusEnum::REJECTED)->count(),
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
            'status' => ['required', new Enum(OrderStatusEnum::class)],
            'driver_id' => 'nullable|exists:users,id',
        ]);

        $order->status = $request->status;

        if ($request->has('driver_id')) {
            $order->driver_id = $request->driver_id;
        }

        $order->save();

        // Check if invoice exists, if not create one when order is confirmed (e.g. onprocess)
        if ($order->status === OrderStatusEnum::ON_PROCESS && !$order->invoice) {
            $this->createInvoice($order);
        }

        // Sync Invoice Status
        if ($order->invoice) {
            if ($order->status === OrderStatusEnum::DONE || $order->status === OrderStatusEnum::DELIVERED) {
                $order->invoice->update(['status' => 'PAID']);
            } elseif ($order->status === OrderStatusEnum::CANCELLED || $order->status === OrderStatusEnum::REJECTED) {
                $order->invoice->update(['status' => 'CANCELLED']);
            }
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
