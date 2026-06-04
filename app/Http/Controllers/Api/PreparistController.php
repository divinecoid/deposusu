<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PreparistController extends Controller
{
    /**
     * Dashboard stats for preparist performance
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        $newOrders = TrxOrder::where('status', OrderStatusEnum::ON_PROCESS)->count();
        $processingOrders = TrxOrder::where('preparist_id', $user->id)
            ->where('status', OrderStatusEnum::ON_PREPARATION)
            ->count();
        // Priority orders: status onprocess created more than 15 minutes ago
        $priorityOrders = TrxOrder::where('status', OrderStatusEnum::ON_PROCESS)
            ->where('created_at', '<=', now()->subMinutes(15))
            ->count();
        $completedToday = TrxOrder::where('preparist_id', $user->id)
            ->whereIn('status', [OrderStatusEnum::PREPARED, OrderStatusEnum::ON_DELIVERY, OrderStatusEnum::DELIVERED, OrderStatusEnum::DONE])
            ->whereDate('prepared_at', $today)
            ->count();

        return response()->json([
            'success' => true,
            'performance' => [
                'newOrders' => $newOrders,
                'processingOrders' => $processingOrders,
                'priorityOrders' => $priorityOrders,
                'completedTodayOrders' => $completedToday,
            ]
        ]);
    }

    /**
     * List orders for packing
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'onprocess');

        // Validate status
        if (!in_array($status, ['onprocess', 'onpreparation'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status filter'
            ], 400);
        }

        $orders = TrxOrder::with(['items.product'])
            ->where('status', $status)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Start preparation of an order
     */
    public function startPreparation(Request $request, TrxOrder $order)
    {
        if ($order->status !== OrderStatusEnum::ON_PROCESS) {
            return response()->json([
                'success' => false,
                'message' => 'Order is not in a state to be prepared (must be onprocess).'
            ], 400);
        }

        $order->update([
            'status' => OrderStatusEnum::ON_PREPARATION,
            'preparist_id' => $request->user()->id,
            'on_preparation_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Preparation started.',
            'order' => $order
        ]);
    }

    /**
     * Sync preparation of an order (logs, checked quantities)
     */
    public function syncOrder(Request $request, TrxOrder $order)
    {
        if ($order->status !== OrderStatusEnum::ON_PREPARATION) {
            return response()->json(['success' => false, 'message' => 'Order is not being prepared.'], 400);
        }

        if ($request->has('logs')) {
            $order->update(['packing_logs' => json_encode($request->input('logs'))]);
        }

        if ($request->has('items')) {
            foreach ($request->input('items') as $itemData) {
                $item = $order->items()->find($itemData['id']);
                if ($item) {
                    $item->update(['checked_quantity' => $itemData['checked_quantity']]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Order synced.']);
    }

    /**
     * Finish preparation of an order (submit)
     */
    public function finishPreparation(Request $request, TrxOrder $order)
    {
        if ($order->status !== OrderStatusEnum::ON_PREPARATION) {
            return response()->json([
                'success' => false,
                'message' => 'Order is not being prepared (must be onpreparation).'
            ], 400);
        }

        if ($order->preparist_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not the assigned preparist for this order.'
            ], 403);
        }

        $photoIsiPath = null;
        $photoFinalPath = null;

        if ($request->hasFile('photo_isi')) {
            $photoIsiPath = $request->file('photo_isi')->store('packing_photos', 'public');
        }

        if ($request->hasFile('photo_final')) {
            $photoFinalPath = $request->file('photo_final')->store('packing_photos', 'public');
        }

        $driver = \App\Models\User::where('role', 'driver')->where('email', 'driver@deposusu.com')->first()
            ?? \App\Models\User::where('role', 'driver')->first();

        $order->update([
            'status' => OrderStatusEnum::PREPARED,
            'driver_id' => $driver ? $driver->id : null,
            'prepared_at' => now(),
            'packing_photo_isi' => $photoIsiPath,
            'packing_photo_final' => $photoFinalPath,
            'packing_logs' => $request->has('logs') ? json_encode($request->input('logs')) : $order->packing_logs,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order successfully prepared.',
            'order' => $order
        ]);
    }
}
