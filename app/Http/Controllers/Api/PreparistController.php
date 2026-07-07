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
        // Waiting for driver: status prepared
        $waitingDriverOrders = TrxOrder::where('status', OrderStatusEnum::PREPARED)->count();
        $completedToday = TrxOrder::where('preparist_id', $user->id)
            ->whereIn('status', [OrderStatusEnum::PREPARED, OrderStatusEnum::ON_DELIVERY, OrderStatusEnum::DELIVERED, OrderStatusEnum::DONE])
            ->whereDate('prepared_at', $today)
            ->count();

        return response()->json([
            'success' => true,
            'performance' => [
                'newOrders' => $newOrders,
                'processingOrders' => $processingOrders,
                'waitingDriverOrders' => $waitingDriverOrders,
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
        $sort = $request->query('sort', 'desc');

        if (!in_array($sort, ['asc', 'desc'])) {
            $sort = 'desc';
        }

        // Validate status
        if (!in_array($status, ['onprocess', 'onpreparation', 'prepared', 'history'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status filter'
            ], 400);
        }

        $query = TrxOrder::with(['items.product', 'preparist']);

        if ($status === 'history') {
            $query->whereIn('status', [OrderStatusEnum::PREPARED, OrderStatusEnum::ON_DELIVERY, OrderStatusEnum::DELIVERED, OrderStatusEnum::DONE]);
            
            if ($request->has('history_status')) {
                $subStatus = $request->query('history_status');
                if ($subStatus === 'prepared') {
                    $query->where('status', OrderStatusEnum::PREPARED);
                } else if ($subStatus === 'ondelivery') {
                    $query->where('status', OrderStatusEnum::ON_DELIVERY);
                } else if ($subStatus === 'completed') {
                    $query->whereIn('status', [OrderStatusEnum::DELIVERED, OrderStatusEnum::DONE]);
                }
            }
            
            $orders = $query->orderBy('prepared_at', 'desc')->paginate(15);
        } else if ($status === 'onprocess') {
            $query->where('status', $status);
            // Priority Queue (Instant/Sameday first), then sort
            $query->orderByRaw("CASE WHEN delivery_type IN ('instant', 'sameday') THEN 1 ELSE 2 END ASC")
                  ->orderBy('created_at', $sort);
            $orders = $query->paginate(15);
        } else {
            $query->where('status', $status);
            if ($status === 'prepared') {
                $orders = $query->orderBy('prepared_at', 'desc')->paginate(15);
            } else {
                $orders = $query->orderBy('created_at', $sort)->paginate(15);
            }
        }

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
            'packer_name' => $request->input('assigned_to'),
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
    public function cancelPreparation(Request $request, TrxOrder $order)
    {
        if ($order->status !== OrderStatusEnum::ON_PREPARATION) {
            return response()->json([
                "success" => false,
                "message" => "Order is not in preparation state."
            ], 400);
        }

        $order->update([
            "status" => OrderStatusEnum::ON_PROCESS,
            "preparist_id" => null,
            "packer_name" => null,
            "on_preparation_at" => null,
        ]);

        return response()->json([
            "success" => true,
            "message" => "Preparation canceled, order is back to onprocess.",
            "order" => $order
        ]);
    }

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

        if ($order->preparist_id != $request->user()->id) {
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

        if ($request->has('items')) {
            $itemsData = $request->input('items');
            if (is_string($itemsData)) {
                $itemsData = json_decode($itemsData, true);
            }
            if (is_array($itemsData)) {
                foreach ($itemsData as $itemData) {
                    if (isset($itemData['id']) && isset($itemData['checked_quantity'])) {
                        $item = $order->items()->find($itemData['id']);
                        if ($item) {
                            $item->update(['checked_quantity' => $itemData['checked_quantity']]);
                        }
                    }
                }
            }
        }

        $logsData = $order->packing_logs;
        if ($request->has('logs')) {
            $reqLogs = $request->input('logs');
            if (is_string($reqLogs)) {
                $reqLogs = json_decode($reqLogs, true);
            }
            if (is_array($reqLogs)) {
                $logsData = json_encode($reqLogs);
            }
        }

        $driver = \App\Models\User::where('role', 'driver')->where('email', 'driver@deposusu.com')->first()
            ?? \App\Models\User::where('role', 'driver')->first();

        $order->update([
            'status' => OrderStatusEnum::PREPARED,
            'driver_id' => $driver ? $driver->id : null,
            'prepared_at' => now(),
            'packing_photo_isi' => $photoIsiPath,
            'packing_photo_final' => $photoFinalPath,
            'packing_logs' => $logsData,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order successfully prepared.',
            'order' => $order
        ]);
    }
}
