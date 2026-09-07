<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MdxProduct;
use App\Models\TrxOrder;
use App\Models\TrxOrderItem;
use App\Models\TrxInvoice;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CashierApiController extends Controller
{
    /**
     * Authenticate cashier and issue Sanctum token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }

        if ($user->role !== 'cashier') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda bukan kasir.'
            ], 403);
        }

        $token = $user->createToken('cashier-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    /**
     * Get active products for POS
     */
    public function products(Request $request)
    {
        // Only return active products that can be sold
        $products = MdxProduct::orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Process POS checkout
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();

            $customerName = $request->customer_name;
            
            // Generate a real unique order/invoice number in format: INV-YYYYMMDD-XXXXX
            $today = now()->format('Ymd');
            $lastOrder = TrxOrder::whereDate('created_at', now()->today())->orderBy('id', 'desc')->first();
            $sequence = $lastOrder ? intval(substr($lastOrder->order_number, -5)) + 1 : 1;
            $orderNumber = 'INV-' . $today . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

            // Create the TrxOrder
            // Since it's POS, we can assume it's DONE immediately or PREPARED if it doesn't need delivery
            // We'll set it to DONE because Cashier implies direct purchase
            $order = TrxOrder::create([
                'order_number' => $orderNumber,
                'customer_name' => $customerName,
                'total_amount' => 0, 
                'total_discount' => 0,
                'status' => OrderStatusEnum::DONE,
                'payment_status' => 'PAID',
                'source' => 'kasir',
            ]);

            $totalAmount = 0;

            foreach ($request->items as $itemData) {
                $productId = $itemData['product_id'];
                $qty = $itemData['quantity'];

                $product = MdxProduct::find($productId);

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

            // Update total amount
            $order->update([
                'total_amount' => $totalAmount,
            ]);

            // Create the TrxInvoice
            $order->invoice()->create([
                'invoice_number' => $order->order_number,
                'issue_date' => now(),
                'due_date' => now(),
                'status' => 'PAID',
                'total_amount' => $order->total_amount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi POS berhasil disimpan!',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction history for cashier
     */
    public function orders(Request $request)
    {
        $orders = TrxOrder::with('items.product')
            ->where('source', 'kasir')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}
