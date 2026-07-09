<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MdxProduct;
use App\Models\TrxOrder;
use App\Models\TrxOrderItem;
use App\Models\TrxInvoice;
use App\Models\FinanceTransaction;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KasirController extends Controller
{
    public function index()
    {
        $products = MdxProduct::with('discounts')->orderBy('name')->get();
        
        $customers = User::where('role', 'customer')
            ->orderBy('name')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->customer_phone ?: '-',
                    'membership' => $user->customerProfile ? $user->customerProfile->membership : 'Silver',
                ];
            });

        return view('admin.kasir.index', compact('products', 'customers'));
    }

    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:users,id',
            'customer_name' => 'required_without:customer_id|nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:mdx_products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string|in:CASH,QRIS,TRANSFER,DEBIT',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all()),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $customerName = '';
            if ($request->customer_id) {
                $user = User::find($request->customer_id);
                $customerName = $user->name;
            } else {
                $customerName = $request->customer_name;
            }

            // Generate order number in format: INV-YYYYMMDD-XXXXX
            $today = now()->format('Ymd');
            $lastOrder = TrxOrder::whereDate('created_at', now()->today())->orderBy('id', 'desc')->first();
            $sequence = $lastOrder ? intval(substr($lastOrder->order_number, -5)) + 1 : 1;
            $orderNumber = 'INV-' . $today . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

            // Create Order
            $order = TrxOrder::create([
                'order_number' => $orderNumber,
                'customer_name' => $customerName,
                'total_amount' => 0, // calculated below
                'total_discount' => $request->discount_amount ?: 0,
                'status' => OrderStatusEnum::DONE,
                'payment_status' => 'PAID',
                'source' => 'kasir',
            ]);

            $totalAmount = 0;

            foreach ($request->items as $itemData) {
                $product = MdxProduct::lockForUpdate()->find($itemData['product_id']);
                $qty = $itemData['quantity'];

                if ($product->stock < $qty) {
                    throw new \Exception("Stok untuk '{$product->name}' tidak mencukupi. Tersedia: {$product->stock}");
                }

                $originalPrice = $product->price;
                $price = $product->discounted_price;
                $discountAmount = ($originalPrice - $price) * $qty;
                $subtotal = $price * $qty;

                TrxOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'original_price' => $originalPrice,
                    'quantity' => $qty,
                    'price' => $price,
                    'discount_amount' => $discountAmount,
                    'subtotal' => $subtotal,
                    'checked_quantity' => $qty,
                ]);

                $totalAmount += $subtotal;

                // Decrement stock
                $product->decrement('stock', $qty);
            }

            // Apply cart-level discount
            $finalAmount = max(0, $totalAmount - ($request->discount_amount ?: 0));

            $order->update([
                'total_amount' => $finalAmount,
            ]);

            // Create Invoice
            $invoice = $order->invoice()->create([
                'invoice_number' => 'INV-' . $order->order_number,
                'issue_date' => now(),
                'due_date' => now(),
                'status' => 'PAID',
                'total_amount' => $finalAmount,
            ]);

            // Create Finance Transaction Log
            FinanceTransaction::create([
                'type' => 'income',
                'category' => 'sales_pos',
                'amount' => $finalAmount,
                'reference_id' => $order->order_number,
                'description' => "Penjualan Kasir (POS Offline) ke customer {$customerName} via {$request->payment_method}",
                'transaction_date' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi POS berhasil diselesaikan!',
                'invoice' => [
                    'order_number' => $order->order_number,
                    'customer_name' => $customerName,
                    'payment_method' => $request->payment_method,
                    'total' => $finalAmount,
                    'discount' => $request->discount_amount ?: 0,
                    'subtotal' => $totalAmount,
                    'date' => now()->format('d M Y H:i'),
                    'items' => $order->items->map(function($item) {
                        return [
                            'name' => $item->product->name,
                            'qty' => $item->quantity,
                            'price' => $item->price,
                            'subtotal' => $item->subtotal,
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal checkout: ' . $e->getMessage()
            ], 500);
        }
    }
}
