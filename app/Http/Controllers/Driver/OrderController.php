<?php

namespace App\Http\Controllers\Driver;

use App\Enums\OrderStatusEnum;
use App\Models\DriverActivityLog;
use App\Models\DriverCashCollection;
use App\Models\EmployeePerformancePoint;
use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Web (session-auth) counterpart to Api\DriverController's order actions,
 * so drivers can work from a browser instead of only the Flutter app.
 */
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureDriver();

        $status = $request->query('status', 'prepared');
        $status = in_array($status, ['prepared', 'ondelivery', 'history']) ? $status : 'prepared';

        $query = TrxOrder::with(['items.product'])->where('driver_id', Auth::id());

        if ($status === 'history') {
            $query->whereIn('status', [OrderStatusEnum::DELIVERED, OrderStatusEnum::DONE])->orderByDesc('delivered_at');
        } else {
            $query->where('status', $status)->orderBy('prepared_at');
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('driver.orders.index', compact('orders', 'status'));
    }

    public function show(TrxOrder $order)
    {
        $this->ensureDriver();
        $this->ensureOwnOrder($order);

        $order->load(['items.product', 'customerUser']);

        return view('driver.orders.show', compact('order'));
    }

    public function pickup(Request $request, TrxOrder $order)
    {
        $this->ensureDriver();
        $this->ensureOwnOrder($order);

        if ($order->status !== OrderStatusEnum::PREPARED) {
            return back()->with('error', 'Pesanan harus berstatus "Prepared" untuk diambil.');
        }

        $order->update([
            'status' => OrderStatusEnum::ON_DELIVERY,
            'picked_up_at' => now(),
        ]);

        DriverActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'pickup_order',
            'description' => "Mengambil pesanan #{$order->order_number} dari gudang.",
            'created_at' => now(),
        ]);

        return redirect()->route('driver.orders.show', $order->id)->with('success', 'Pesanan berhasil diambil & siap dikirim.');
    }

    public function finish(Request $request, TrxOrder $order)
    {
        $this->ensureDriver();
        $this->ensureOwnOrder($order);

        if ($order->status !== OrderStatusEnum::ON_DELIVERY) {
            return back()->with('error', 'Pesanan harus sedang dalam proses pengiriman.');
        }

        $request->validate([
            'photo' => 'required|image|max:5120',
            'recipient_name' => 'required|string|max:255',
            'recipient_signature' => 'required|string',
        ]);

        $path = $request->file('photo')->store('delivery_proofs', 'public');

        $order->update([
            'status' => OrderStatusEnum::DELIVERED,
            'delivered_at' => now(),
            'delivery_proof_photo' => asset('storage/' . $path),
            'recipient_name' => $request->recipient_name,
            'recipient_signature' => $request->recipient_signature,
            'payment_status' => $order->payment_method === 'COD' ? 'PAID' : $order->payment_status,
        ]);

        if ($order->payment_method === 'COD') {
            DriverCashCollection::create([
                'driver_id' => Auth::id(),
                'order_id' => $order->id,
                'amount' => $order->total_amount + $order->convenience_fee,
                'collected_at' => now(),
            ]);
        }

        try {
            EmployeePerformancePoint::awardDelivery(Auth::id(), $order->id, "Delivery order #{$order->order_number} selesai");
        } catch (\Exception $e) {
            Log::error('Failed to award delivery point: ' . $e->getMessage());
        }

        if ($order->invoice) {
            $order->invoice->update(['status' => 'PAID']);
        }

        DriverActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'complete_delivery',
            'description' => "Menyelesaikan pengantaran pesanan #{$order->order_number} kepada {$request->recipient_name}.",
            'created_at' => now(),
        ]);

        return redirect()->route('driver.orders.index', ['status' => 'ondelivery'])->with('success', "Pesanan #{$order->order_number} berhasil diantar.");
    }

    private function ensureDriver(): void
    {
        abort_unless(Auth::user()->isDriver(), 403);
    }

    private function ensureOwnOrder(TrxOrder $order): void
    {
        abort_unless($order->driver_id === Auth::id(), 403);
    }
}
