<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use App\Models\User;
use App\Models\MdxDriver;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $customer = $request->query('customer');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $source = $request->query('source', 'all');

        $query = TrxOrder::with(['driver', 'items.product']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($customer)) {
            $query->where('customer_name', 'like', '%' . $customer . '%');
        }

        if (!empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($source !== 'all') {
            $query->where('source', $source);
        }

        $orders = $query->latest()->paginate(10);

        // Build base queries for count calculations preserving other active filters
        $countQuery = TrxOrder::query();
        if (!empty($customer)) {
            $countQuery->where('customer_name', 'like', '%' . $customer . '%');
        }
        if (!empty($startDate)) {
            $countQuery->whereDate('created_at', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $countQuery->whereDate('created_at', '<=', $endDate);
        }
        if ($source !== 'all') {
            $countQuery->where('source', $source);
        }

        $counts = [
            'all' => (clone $countQuery)->count(),
            'pending' => (clone $countQuery)->where('status', OrderStatusEnum::PENDING)->count(),
            'onprocess' => (clone $countQuery)->where('status', OrderStatusEnum::ON_PROCESS)->count(),
            'onpreparation' => (clone $countQuery)->where('status', OrderStatusEnum::ON_PREPARATION)->count(),
            'prepared' => (clone $countQuery)->where('status', OrderStatusEnum::PREPARED)->count(),
            'ondelivery' => (clone $countQuery)->where('status', OrderStatusEnum::ON_DELIVERY)->count(),
            'delivered' => (clone $countQuery)->where('status', OrderStatusEnum::DELIVERED)->count(),
            'partialdelivered' => (clone $countQuery)->where('status', OrderStatusEnum::PARTIAL_DELIVERED)->count(),
            'done' => (clone $countQuery)->where('status', OrderStatusEnum::DONE)->count(),
            'cancelled' => (clone $countQuery)->where('status', OrderStatusEnum::CANCELLED)->count(),
            'rejected' => (clone $countQuery)->where('status', OrderStatusEnum::REJECTED)->count(),
        ];

        return view('admin.orders.index', compact('orders', 'status', 'counts', 'customer', 'startDate', 'endDate', 'source'));
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

    public function create()
    {
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $products = \App\Models\MdxProduct::with(['discounts', 'categories'])
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('admin.orders.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:users,id',
            'customer_name' => 'required_without:customer_id|nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:mdx_products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            if ($request->customer_id) {
                $user = User::find($request->customer_id);
                $customerName = $user->name;
            } else {
                $customerName = $request->customer_name;
            }

            $today = now()->format('Ymd');
            $lastOrder = TrxOrder::whereDate('created_at', now()->today())->orderBy('id', 'desc')->first();
            $sequence = $lastOrder ? intval(substr($lastOrder->order_number, -5)) + 1 : 1;
            $orderNumber = 'INV-' . $today . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

            $order = TrxOrder::create([
                'order_number' => $orderNumber,
                'customer_name' => $customerName,
                'total_amount' => 0,
                'total_discount' => 0,
                'status' => OrderStatusEnum::PENDING,
                'payment_status' => 'UNPAID',
                'source' => 'admin',
            ]);

            $totalAmount = 0;
            $totalDiscount = 0;

            foreach ($request->products as $item) {
                $product = \App\Models\MdxProduct::lockForUpdate()->find($item['id']);
                $qty = intval($item['quantity']);

                if ($product->stock < $qty) {
                    throw new \Exception("Stok produk '{$product->name}' tidak mencukupi (Tersedia: {$product->stock})");
                }

                $activeDiscount = $product->active_discount;
                $originalPrice = $product->price;
                $discountPrice = $product->discounted_price;
                $discountAmount = 0;
                $discountId = null;

                if ($activeDiscount) {
                    $discountId = $activeDiscount->id;
                    $discountAmount = ($originalPrice - $discountPrice) * $qty;
                }

                $subtotal = $discountPrice * $qty;

                \App\Models\TrxOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'original_price' => $originalPrice,
                    'quantity' => $qty,
                    'price' => $discountPrice,
                    'discount_amount' => $discountAmount,
                    'discount_id' => $discountId,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
                $totalDiscount += $discountAmount;

                $product->decrement('stock', $qty);
            }

            $order->update([
                'total_amount' => $totalAmount,
                'total_discount' => $totalDiscount,
            ]);

            // Create Invoice immediately on order creation
            $this->createInvoice($order);

            DB::commit();

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', "Order #{$order->order_number} berhasil dibuat secara manual.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat order: ' . $e->getMessage());
        }
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
