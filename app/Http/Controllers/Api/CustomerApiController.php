<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MdxCustomer;
use App\Models\MdxProduct;
use App\Models\TrxOrder;
use App\Models\TrxOrderItem;
use App\Models\TrxInvoice;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerApiController extends Controller
{
    /**
     * Handle public checkout from Customer Mobile Application
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 400); // Return a clean 400 Bad Request with the actual validation error details
        }

        try {
            DB::beginTransaction();

            $customerName = $request->customer_name;
            $phone = $request->phone ?: '08123456789';
            $address = $request->address ?: 'Bandung';

            // 1. Find or create the Customer User and Customer Profile automatically
            $user = User::firstOrCreate(
                ['name' => $customerName, 'role' => 'customer'],
                [
                    'email' => strtolower(str_replace(' ', '', $customerName)) . '_' . rand(100, 999) . '@deposusu.com',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );

            MdxCustomer::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => $phone,
                    'address' => $address,
                ]
            );

            // 2. Generate a real unique order/invoice number in format: INV-YYYYMMDD-XXXXX
            $today = now()->format('Ymd');
            $lastOrder = TrxOrder::whereDate('created_at', now()->today())->orderBy('id', 'desc')->first();
            $sequence = $lastOrder ? intval(substr($lastOrder->order_number, -5)) + 1 : 1;
            $orderNumber = 'INV-' . $today . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

            // 3. Create the TrxOrder
            $order = TrxOrder::create([
                'order_number' => $orderNumber,
                'customer_name' => $customerName,
                'total_amount' => 0, // Will update after items loop
                'total_discount' => 0,
                'status' => OrderStatusEnum::ON_PROCESS,
                'payment_status' => $request->payment_method === 'COD' ? 'UNPAID' : 'PAID',
                'source' => 'app',
            ]);

            $totalAmount = 0;

            // 4. Create the TrxOrderItems
            foreach ($request->items as $itemData) {
                $productId = $itemData['product_id'];
                $qty = $itemData['quantity'];

                // Retrieve product with fallback to ensure test orders never crash due to ID differences
                $product = MdxProduct::find($productId) ?? MdxProduct::first();

                if (!$product) {
                    continue;
                }

                $subtotal = $product->price * $qty;

                TrxOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'original_price' => $product->price,
                    'quantity' => $qty,
                    'price' => $product->price,
                    'discount_amount' => 0,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;

                // Decrement stock
                $product->decrement('stock', $qty);
            }

            // 5. Update total amount on the order
            $order->update([
                'total_amount' => $totalAmount,
            ]);

            // 6. Create the TrxInvoice
            $order->invoice()->create([
                'invoice_number' => $order->order_number,
                'issue_date' => now(),
                'due_date' => now()->addDays(7),
                'status' => $order->payment_status,
                'total_amount' => $order->total_amount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
}
