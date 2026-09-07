<?php

namespace App\Http\Controllers\Preparist;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\EmployeePerformancePoint;
use App\Models\TrxOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Web (session-auth) counterpart to Api\PreparistController, so preparists
 * can work from a browser instead of only the Flutter app. Same business
 * rules, same TrxOrder columns — just views instead of JSON.
 */
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $this->ensurePreparist();

        $status = $request->query('status', 'onprocess');
        $status = in_array($status, ['onprocess', 'onpreparation', 'prepared', 'history']) ? $status : 'onprocess';

        $query = TrxOrder::with(['items.product']);

        if ($status === 'history') {
            $query->where('preparist_id', Auth::id())
                ->whereIn('status', [OrderStatusEnum::PREPARED, OrderStatusEnum::ON_DELIVERY, OrderStatusEnum::DELIVERED, OrderStatusEnum::DONE])
                ->orderByDesc('prepared_at');
        } elseif ($status === 'onprocess') {
            $query->where('status', $status)
                ->orderByRaw("CASE WHEN delivery_type IN ('instant', 'sameday') THEN 1 ELSE 2 END ASC")
                ->orderBy('created_at');
        } elseif ($status === 'prepared') {
            $query->where('status', $status)->where('preparist_id', Auth::id())->orderByDesc('prepared_at');
        } else {
            $query->where('status', $status)->orderBy('created_at');
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('preparist.orders.index', compact('orders', 'status'));
    }

    public function show(TrxOrder $order)
    {
        $this->ensurePreparist();

        $order->load(['items.product', 'customerUser']);

        return view('preparist.orders.show', compact('order'));
    }

    public function start(TrxOrder $order)
    {
        $this->ensurePreparist();

        if ($order->status !== OrderStatusEnum::ON_PROCESS) {
            return back()->with('error', 'Pesanan tidak dalam status yang bisa disiapkan.');
        }

        $order->update([
            'status' => OrderStatusEnum::ON_PREPARATION,
            'preparist_id' => Auth::id(),
            'packer_name' => Auth::user()->name,
            'on_preparation_at' => now(),
        ]);

        return redirect()->route('preparist.orders.show', $order->id)->with('success', 'Mulai menyiapkan pesanan.');
    }

    public function cancel(TrxOrder $order)
    {
        $this->ensurePreparist();

        if ($order->status !== OrderStatusEnum::ON_PREPARATION) {
            return back()->with('error', 'Pesanan tidak sedang disiapkan.');
        }

        $order->update([
            'status' => OrderStatusEnum::ON_PROCESS,
            'preparist_id' => null,
            'packer_name' => null,
            'on_preparation_at' => null,
        ]);

        return redirect()->route('preparist.orders.index')->with('success', 'Persiapan dibatalkan, pesanan kembali ke antrian.');
    }

    public function finish(Request $request, TrxOrder $order)
    {
        $this->ensurePreparist();

        if ($order->status !== OrderStatusEnum::ON_PREPARATION) {
            return back()->with('error', 'Pesanan tidak sedang disiapkan.');
        }

        if ($order->preparist_id != Auth::id()) {
            return back()->with('error', 'Anda bukan preparist yang ditugaskan untuk pesanan ini.');
        }

        $request->validate([
            'photo_isi' => 'nullable|image|max:15360',
            'photo_final' => 'nullable|image|max:15360',
            'items' => 'nullable|array',
        ]);

        $photoIsiPath = $request->hasFile('photo_isi') ? $request->file('photo_isi')->store('packing_photos', 'public') : null;
        $photoFinalPath = $request->hasFile('photo_final') ? $request->file('photo_final')->store('packing_photos', 'public') : null;

        foreach ($request->input('items', []) as $itemId => $checkedQuantity) {
            $item = $order->items()->find($itemId);
            if ($item) {
                $item->update(['checked_quantity' => $checkedQuantity]);
            }
        }

        $driver = \App\Models\User::where('role', 'driver')->where('email', 'driver@deposusu.com')->first()
            ?? \App\Models\User::where('role', 'driver')->first();

        $order->update([
            'status' => OrderStatusEnum::PREPARED,
            'driver_id' => $order->driver_id ?? $driver?->id,
            'prepared_at' => now(),
            'packing_photo_isi' => $photoIsiPath ?? $order->packing_photo_isi,
            'packing_photo_final' => $photoFinalPath ?? $order->packing_photo_final,
        ]);

        try {
            EmployeePerformancePoint::awardPacking(Auth::id(), $order->id, "Packing order #{$order->order_number} selesai");
        } catch (\Exception $e) {
            Log::error('Failed to award packing point: ' . $e->getMessage());
        }

        return redirect()->route('preparist.orders.index', ['status' => 'onprocess'])->with('success', "Pesanan #{$order->order_number} berhasil disiapkan.");
    }

    private function ensurePreparist(): void
    {
        abort_unless(Auth::user()->isPreparist(), 403);
    }
}
