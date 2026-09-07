<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\MdxProduct;
use App\Models\TrxCart;
use App\Models\TrxCartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function cart(Request $request): TrxCart
    {
        return TrxCart::firstOrCreate(['user_id' => $request->user()->id]);
    }

    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'cart' => $this->formatCart($this->cart($request)),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:mdx_products,id',
            'quantity' => 'integer|min:1',
        ]);

        $product = MdxProduct::findOrFail($request->product_id);
        $quantity = $request->input('quantity', 1);

        if ($product->stock < $quantity) {
            return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi'], 400);
        }

        $cart = $this->cart($request);
        $cart->addItem($product->id, $quantity);

        return response()->json(['success' => true, 'cart' => $this->formatCart($cart)]);
    }

    public function update(Request $request, TrxCartItem $item)
    {
        $request->validate(['quantity' => 'required|integer|min:0']);

        $cart = $this->cart($request);
        abort_unless($item->cart_id === $cart->id, 403);

        if ($item->quantity && $request->quantity > $item->product->stock) {
            return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi'], 400);
        }

        $cart->updateItemQuantity($item->id, $request->quantity);

        return response()->json(['success' => true, 'cart' => $this->formatCart($cart->fresh())]);
    }

    public function remove(Request $request, TrxCartItem $item)
    {
        $cart = $this->cart($request);
        abort_unless($item->cart_id === $cart->id, 403);

        $cart->removeItem($item->id);

        return response()->json(['success' => true, 'cart' => $this->formatCart($cart->fresh())]);
    }

    private function formatCart(TrxCart $cart): array
    {
        $items = $cart->items()->with('product')->get();

        return [
            'total_items' => $cart->getTotalItems(),
            'subtotal_before_discount' => (float) $cart->getSubtotalBeforeDiscount(),
            'total_discount' => (float) $cart->getTotalDiscount(),
            'total_price' => (float) $cart->getTotalPrice(),
            'items' => $items->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name ?? 'Produk',
                'image' => $item->product && $item->product->image
                    ? (str_starts_with($item->product->image, 'storage/') ? asset($item->product->image) : $item->product->image)
                    : null,
                'price' => (float) $item->price,
                'quantity' => $item->quantity,
                'subtotal' => (float) $item->getSubtotal(),
                'stock' => (int) ($item->product->stock ?? 0),
            ]),
        ];
    }
}
