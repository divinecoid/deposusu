<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use App\Models\User;
use App\Models\MdxCustomer;
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

    /**
     * Sales Order index — only shows manually-created (admin) orders.
     */
    public function salesOrderIndex(Request $request)
    {
        $status = $request->query('status', 'all');
        $customer = $request->query('customer');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = TrxOrder::with(['customerUser', 'items.product'])
            ->where('source', 'admin');

        if ($status !== 'all') {
            $query->where('status', $status);
        }
        if (!empty($customer)) {
            $query->where(function ($q) use ($customer) {
                $q->where('customer_name', 'like', '%' . $customer . '%')
                  ->orWhere('customer_phone', 'like', '%' . $customer . '%');
            });
        }
        if (!empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $orders = $query->latest()->paginate(15);

        $countQuery = TrxOrder::where('source', 'admin');
        $counts = [
            'all'       => (clone $countQuery)->count(),
            'pending'   => (clone $countQuery)->where('status', OrderStatusEnum::PENDING)->count(),
            'onprocess' => (clone $countQuery)->where('status', OrderStatusEnum::ON_PROCESS)->count(),
            'done'      => (clone $countQuery)->where('status', OrderStatusEnum::DONE)->count(),
            'cancelled' => (clone $countQuery)->where('status', OrderStatusEnum::CANCELLED)->count(),
        ];

        return view('admin.sales-order.index', compact('orders', 'status', 'counts', 'customer', 'startDate', 'endDate'));
    }

    /**
     * AJAX — search registered customers by name or phone for Sales Order form.
     */
    public function searchCustomers(Request $request)
    {
        $q = $request->query('q', '');

        $users = User::where('role', 'customer')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->with('customerProfile')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id'      => $user->id,
                    'name'    => $user->name,
                    'phone'   => $user->phone ?? ($user->customerProfile->phone ?? ''),
                    'address' => $user->customerProfile->address ?? '',
                    'email'   => $user->email,
                ];
            });

        return response()->json($users);
    }

    public function create()
    {
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $products = \App\Models\MdxProduct::with(['discounts', 'categories'])
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();
        $drivers = User::where('role', 'driver')->orderBy('name')->get();

        return view('admin.sales-order.create', compact('customers', 'products', 'drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'      => 'nullable|exists:users,id',
            'customer_name'    => 'required_without:customer_id|nullable|string|max:255',
            'customer_phone'   => 'nullable|string|max:20',
            'customer_address' => 'nullable|string|max:500',
            'delivery_date'    => 'nullable|date|after_or_equal:today',
            'delivery_slot'    => 'nullable|in:pagi,siang,sore',
            'payment_method'   => 'nullable|in:transfer,cash,cod,wallet,piutang',
            'notes'            => 'nullable|string|max:1000',
            'products'         => 'required|array|min:1',
            'products.*.id'    => 'required|exists:mdx_products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            if ($request->customer_id) {
                $user = User::find($request->customer_id);
                $customerName    = $user->name;
                $customerPhone   = $request->customer_phone ?: ($user->phone ?? '');
                $customerAddress = $request->customer_address ?: ($user->customerProfile->address ?? '');
            } else {
                $customerName    = $request->customer_name;
                $customerPhone   = $request->customer_phone;
                $customerAddress = $request->customer_address;
            }

            $today     = now()->format('Ymd');
            $lastOrder = TrxOrder::whereDate('created_at', now()->today())->orderBy('id', 'desc')->first();
            $sequence  = $lastOrder ? intval(substr($lastOrder->order_number, -5)) + 1 : 1;
            $orderNumber = 'SO-' . $today . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

            $order = TrxOrder::create([
                'order_number'     => $orderNumber,
                'customer_id'      => $request->customer_id,
                'customer_name'    => $customerName,
                'customer_phone'   => $customerPhone,
                'customer_address' => $customerAddress,
                'delivery_date'    => $request->delivery_date,
                'delivery_slot'    => $request->delivery_slot,
                'payment_method'   => $request->payment_method,
                'notes'            => $request->notes,
                'total_amount'     => 0,
                'total_discount'   => 0,
                'status'           => OrderStatusEnum::PENDING,
                'payment_status'   => 'UNPAID',
                'source'           => 'admin',
            ]);

            $totalAmount   = 0;
            $totalDiscount = 0;

            foreach ($request->products as $item) {
                $product = \App\Models\MdxProduct::lockForUpdate()->find($item['id']);
                $qty     = intval($item['quantity']);

                if ($product->stock < $qty) {
                    throw new \Exception("Stok produk '{$product->name}' tidak mencukupi (Tersedia: {$product->stock})");
                }

                $originalPrice  = $product->price;
                ['price' => $discountPrice, 'discount' => $activeDiscount] = $product->priceForQuantity($qty);
                $discountAmount = 0;
                $discountId     = null;

                if ($activeDiscount) {
                    $discountId     = $activeDiscount->id;
                    $discountAmount = ($originalPrice - $discountPrice) * $qty;
                }

                $subtotal = $discountPrice * $qty;

                \App\Models\TrxOrderItem::create([
                    'order_id'        => $order->id,
                    'product_id'      => $product->id,
                    'original_price'  => $originalPrice,
                    'quantity'        => $qty,
                    'price'           => $discountPrice,
                    'discount_amount' => $discountAmount,
                    'discount_id'     => $discountId,
                    'subtotal'        => $subtotal,
                ]);

                $totalAmount   += $subtotal;
                $totalDiscount += $discountAmount;

                $product->decrement('stock', $qty);
            }

            $order->update([
                'total_amount'   => $totalAmount,
                'total_discount' => $totalDiscount,
            ]);

            // Create Invoice immediately
            $this->createInvoice($order);

            DB::commit();

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', "Sales Order #{$order->order_number} berhasil dibuat. Invoice sudah digenerate.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat Sales Order: ' . $e->getMessage());
        }
    }

    private function createInvoice(TrxOrder $order)
    {
        $order->invoice()->create([
            'invoice_number' => $order->order_number,
            'issue_date' => now(),
            'due_date' => now()->addDays(7), // Example due date
            'status' => 'UNPAID',
            'total_amount' => $order->total_amount,
        ]);
    }
}
