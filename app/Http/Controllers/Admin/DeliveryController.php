<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use App\Models\User;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $driverId = $request->query('driver_id', 'all');

        $query = TrxOrder::with(['driver', 'items.product'])
            ->whereIn('status', [
                OrderStatusEnum::PREPARED,
                OrderStatusEnum::ON_DELIVERY,
                OrderStatusEnum::DELIVERED,
                OrderStatusEnum::DONE
            ]);

        if ($status === 'pending') {
            $query->where('status', OrderStatusEnum::PREPARED);
        } elseif ($status === 'shipping') {
            $query->where('status', OrderStatusEnum::ON_DELIVERY);
        } elseif ($status === 'completed') {
            $query->whereIn('status', [OrderStatusEnum::DELIVERED, OrderStatusEnum::DONE]);
        }

        if ($driverId !== 'all') {
            $query->where('driver_id', $driverId);
        }

        $deliveries = $query->latest()->paginate(15);
        $drivers = User::where('role', 'driver')->orderBy('name')->get();

        $counts = [
            'pending'   => TrxOrder::where('status', OrderStatusEnum::PREPARED)->count(),
            'shipping'  => TrxOrder::where('status', OrderStatusEnum::ON_DELIVERY)->count(),
            'completed' => TrxOrder::whereIn('status', [OrderStatusEnum::DELIVERED, OrderStatusEnum::DONE])->count(),
        ];

        return view('admin.deliveries.index', compact('deliveries', 'drivers', 'status', 'driverId', 'counts'));
    }

    public function assignBulk(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'exists:trx_orders,id',
            'driver_id' => 'required|exists:users,id',
        ]);

        TrxOrder::whereIn('id', $request->order_ids)
            ->where('status', OrderStatusEnum::PREPARED)
            ->update([
                'driver_id' => $request->driver_id,
                'status' => OrderStatusEnum::ON_DELIVERY,
                'picked_up_at' => now(),
            ]);

        return back()->with('success', 'Driver berhasil ditugaskan secara massal.');
    }

    public function updateDelivery(Request $request, TrxOrder $order)
    {
        $request->validate([
            'status' => 'required|in:delivered,done,failed',
            'recipient_name' => 'nullable|string|max:100',
            'delivery_proof_photo' => 'nullable|image|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        $data = [];
        if ($request->status === 'delivered' || $request->status === 'done') {
            $order->status = OrderStatusEnum::DONE;
            $order->delivered_at = now();
            $order->recipient_name = $request->recipient_name ?: $order->customer_name;
            if ($order->invoice) {
                $order->invoice->update(['status' => 'PAID']);
            }
        } else {
            $order->status = OrderStatusEnum::PREPARED; // Return to prepared for reschedule
        }

        if ($request->hasFile('delivery_proof_photo')) {
            $path = $request->file('delivery_proof_photo')->store('deliveries', 'public');
            $order->delivery_proof_photo = $path;
        }

        if ($request->notes) {
            $order->notes = ($order->notes ? $order->notes . "\n" : "") . "Delivery Log: " . $request->notes;
        }

        $order->save();

        return back()->with('success', 'Status pengiriman berhasil diperbarui.');
    }
}
