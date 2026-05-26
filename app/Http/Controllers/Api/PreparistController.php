<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PreparistController extends Controller
{
    /**
     * Dashboard stats for preparist performance
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $now = Carbon::now();

        $stats = [
            'hour' => TrxOrder::where('preparist_id', $user->id)
                ->where('status', OrderStatusEnum::PREPARED)
                ->where('prepared_at', '>=', $now->copy()->startOfHour())
                ->count(),
            'day' => TrxOrder::where('preparist_id', $user->id)
                ->where('status', OrderStatusEnum::PREPARED)
                ->where('prepared_at', '>=', $now->copy()->startOfDay())
                ->count(),
            'week' => TrxOrder::where('preparist_id', $user->id)
                ->where('status', OrderStatusEnum::PREPARED)
                ->where('prepared_at', '>=', $now->copy()->startOfWeek())
                ->count(),
            'month' => TrxOrder::where('preparist_id', $user->id)
                ->where('status', OrderStatusEnum::PREPARED)
                ->where('prepared_at', '>=', $now->copy()->startOfMonth())
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'performance' => $stats
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

        // Optional: Check if the current user is the one who started it
        if ($order->preparist_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not the assigned preparist for this order.'
            ], 403);
        }

        $order->update([
            'status' => OrderStatusEnum::PREPARED,
            'prepared_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order successfully prepared.',
            'order' => $order
        ]);
    }
}
