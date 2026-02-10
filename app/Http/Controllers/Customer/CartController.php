<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MdxProduct;
use App\Models\TrxOrder;
use App\Models\TrxOrderItem;
use App\Models\TrxCart;
use App\Models\TrxCartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Get or create cart for current user/session
     */
    private function getCurrentCart()
    {
        if (auth()->check()) {
            // For logged-in users
            $cart = TrxCart::firstOrCreate([
                'user_id' => auth()->id()
            ]);
        } else {
            // For guest users
            $sessionId = Session::getId();
            $cart = TrxCart::firstOrCreate([
                'session_id' => $sessionId
            ]);
        }

        return $cart;
    }

    /**
     * Display cart page
     */
    public function index()
    {
        $cart = $this->getCurrentCart();
        $cartItems = $cart->items()->with('product')->get();
        $totalItems = $cart->getTotalItems();
        $totalPrice = $cart->getTotalPrice();

        return view('customer.cart', compact('cart', 'cartItems', 'totalItems', 'totalPrice'));
    }

    /**
     * Add item to cart (AJAX)
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:mdx_products,id',
            'quantity' => 'integer|min:1',
        ]);

        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        // Check product exists and has stock
        $product = MdxProduct::findOrFail($productId);

        if ($product->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        $cart = $this->getCurrentCart();
        $cartItem = $cart->addItem($productId, $quantity);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart' => [
                'total_items' => $cart->getTotalItems(),
                'total_price' => $cart->getTotalPrice(),
            ]
        ]);
    }

    /**
     * Update cart item quantity (AJAX)
     */
    public function update(Request $request, $cartItemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = $this->getCurrentCart();
        $quantity = $request->quantity;

        // Check if cart item belongs to this cart
        $cartItem = TrxCartItem::where('id', $cartItemId)
            ->where('cart_id', $cart->id)
            ->firstOrFail();

        // Check stock
        if ($quantity > $cartItem->product->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        $updatedItem = $cart->updateItemQuantity($cartItemId, $quantity);

        return response()->json([
            'success' => true,
            'message' => $quantity > 0 ? 'Jumlah berhasil diupdate' : 'Item berhasil dihapus',
            'cart' => [
                'total_items' => $cart->getTotalItems(),
                'total_price' => $cart->getTotalPrice(),
            ],
            'item' => $updatedItem ? [
                'subtotal' => $updatedItem->getSubtotal()
            ] : null
        ]);
    }

    /**
     * Remove item from cart (AJAX)
     */
    public function remove($cartItemId)
    {
        $cart = $this->getCurrentCart();

        // Check if cart item belongs to this cart
        $cartItem = TrxCartItem::where('id', $cartItemId)
            ->where('cart_id', $cart->id)
            ->firstOrFail();

        $cart->removeItem($cartItemId);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang',
            'cart' => [
                'total_items' => $cart->getTotalItems(),
                'total_price' => $cart->getTotalPrice(),
            ]
        ]);
    }

    /**
     * Clear all items from cart
     */
    public function clear()
    {
        $cart = $this->getCurrentCart();
        $cart->clearCart();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan',
            'cart' => [
                'total_items' => 0,
                'total_price' => 0,
            ]
        ]);
    }

    /**
     * Handle checkout process
     */
    public function checkout(Request $request)
    {
        $cart = $this->getCurrentCart();
        $cartItems = $cart->items()->with('product')->get();

        if ($cartItems->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $totalDiscount = 0;

            // Generate order number
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));

            $order = TrxOrder::create([
                'order_number' => $orderNumber,
                'customer_name' => auth()->check() ? auth()->user()->name : 'Guest',
                'total_amount' => 0, // Placeholder
                'total_discount' => 0, // Placeholder
                'status' => 'pending',
                'payment_status' => 'UNPAID',
            ]);

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                $activeDiscount = $product->active_discount;

                $originalPrice = $product->price;
                $discountPrice = $product->discounted_price;
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

                // Update stock
                $product->decrement('stock', $cartItem->quantity);
            }

            // Update order totals
            $order->update([
                'total_amount' => $totalAmount,
                'total_discount' => $totalDiscount,
            ]);

            // Clear cart
            $cart->clearCart();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'order_id' => $order->id,
                'order_number' => $order->order_number
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
