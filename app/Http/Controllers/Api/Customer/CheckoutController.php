<?php

namespace App\Http\Controllers\Api\Customer;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\XenditPaymentController;
use App\Models\TrxCart;
use App\Models\TrxOrder;
use App\Models\TrxOrderItem;
use App\Models\TrxSubscription;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Payment channels + precomputed fees for the current cart, so the app
     * can render the picker without doing any fee math itself.
     */
    public function paymentOptions(Request $request, XenditService $xendit)
    {
        $cart = TrxCart::firstOrCreate(['user_id' => $request->user()->id]);
        $totalPrice = (float) $cart->getTotalPrice();

        $channels = collect($xendit->channels())->map(function ($channel, $code) use ($xendit, $totalPrice) {
            $fee = $xendit->computeFee($code, $totalPrice);

            return array_merge($channel, [
                'code' => $code,
                'convenience_fee' => $fee['fee'],
                'total_with_fee' => $fee['total'],
            ]);
        })->values();

        return response()->json([
            'success' => true,
            'channels' => $channels,
            'cod_available' => (bool) optional($request->user()->customerProfile)->is_verified,
            'total_price' => $totalPrice,
        ]);
    }

    /**
     * Mirrors the web checkout flow (Customer\CartController::checkout) so
     * both clients share identical fee/verification/order rules.
     */
    public function checkout(Request $request, XenditService $xendit)
    {
        $user = $request->user();
        $cart = TrxCart::firstOrCreate(['user_id' => $user->id]);
        $cartItems = $cart->items()->with('product')->get();

        if ($cartItems->count() === 0) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong'], 400);
        }

        $request->validate([
            'shipping_address' => 'required|string|min:10',
            'payment_method' => 'required|string',
        ]);

        $channelCode = $request->input('payment_method');
        $isCod = $channelCode === 'COD';

        if (!$isCod && !$xendit->channel($channelCode)) {
            return response()->json(['success' => false, 'message' => 'Metode pembayaran tidak valid'], 422);
        }

        if ($isCod && !optional($user->customerProfile)->is_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda belum terverifikasi untuk COD. Silakan pilih metode pembayaran online.',
            ], 403);
        }

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $totalDiscount = 0;

            $today = now()->format('Ymd');
            $lastOrder = TrxOrder::whereDate('created_at', now()->today())->orderBy('id', 'desc')->first();
            $sequence = $lastOrder ? intval(substr($lastOrder->order_number, -5)) + 1 : 1;
            $orderNumber = 'INV-' . $today . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

            $order = TrxOrder::create([
                'order_number' => $orderNumber,
                'customer_id' => $user->id,
                'customer_name' => $user->name,
                'customer_address' => $request->input('shipping_address'),
                'payment_method' => $isCod ? 'COD' : $channelCode,
                'total_amount' => 0,
                'total_discount' => 0,
                'status' => OrderStatusEnum::ON_PROCESS,
                'payment_status' => 'UNPAID',
                'source' => 'app',
            ]);

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                $originalPrice = $product->price;

                ['price' => $discountPrice, 'discount' => $activeDiscount] = $product->priceForQuantity((int) $cartItem->quantity);
                $discountAmount = 0;
                $discountId = null;

                if ($activeDiscount) {
                    $discountId = $activeDiscount->id;
                    $discountAmount = ($originalPrice - $discountPrice) * $cartItem->quantity;
                }

                $subtotal = $discountPrice * $cartItem->quantity;

                TrxOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'original_price' => $originalPrice,
                    'quantity' => $cartItem->quantity,
                    'price' => $discountPrice,
                    'discount_amount' => $discountAmount,
                    'discount_id' => $discountId,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
                $totalDiscount += $discountAmount;

                $product->decrement('stock', $cartItem->quantity);

                if ($cartItem->is_routine && !empty($cartItem->routine_schedule)) {
                    TrxSubscription::updateOrCreate(
                        ['customer_id' => $user->id, 'product_id' => $product->id],
                        [
                            'quantity' => $cartItem->quantity,
                            'days_of_week' => $cartItem->routine_schedule,
                            'shipping_address' => $request->input('shipping_address'),
                            'payment_method' => $isCod ? 'COD' : $channelCode,
                            'is_active' => true,
                            'last_generated_date' => now()->toDateString(),
                        ]
                    );
                }
            }

            $order->update([
                'total_amount' => $totalAmount,
                'total_discount' => $totalDiscount,
            ]);

            $order->invoice()->create([
                'invoice_number' => $order->order_number,
                'issue_date' => now(),
                'due_date' => now()->addDays(7),
                'status' => 'UNPAID',
                'total_amount' => $order->total_amount,
            ]);

            $cart->clearCart();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal membuat pesanan: ' . $e->getMessage()], 500);
        }

        if ($isCod) {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment' => ['group' => 'cod'],
            ]);
        }

        try {
            $payment = $xendit->createPayment($order, $channelCode);
        } catch (\Throwable $e) {
            Log::error('Xendit createPayment failed (mobile)', ['order_id' => $order->id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan dibuat, tapi pembayaran gagal diproses. Silakan coba lagi dari halaman pesanan.',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment' => ['group' => 'failed'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat!',
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'payment' => XenditPaymentController::formatPayment($payment, $xendit->channel($channelCode), 'api.customer.payments.status'),
        ]);
    }
}
