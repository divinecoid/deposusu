<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrxCart;
use App\Models\TrxCartItem;

class CheckoutController extends Controller
{
    /**
     * Display the fast checkout page.
     */
    public function index(Request $request)
    {
        // Require auth to checkout (if not already handled by middleware)
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login untuk melanjutkan checkout.');
        }

        // Retrieve current cart
        $cart = TrxCart::where('user_id', auth()->id())->first();
        
        if (!$cart || $cart->items()->count() == 0) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $cartItems = $cart->items()->with('product')->get();
        $totalItems = $cart->getTotalItems();
        $subtotalBeforeDiscount = $cart->getSubtotalBeforeDiscount();
        $totalDiscount = $cart->getTotalDiscount();
        $totalPrice = $cart->getTotalPrice();

        // Get user profile for address
        $user = auth()->user();
        // Fallback dummy address if none exists
        $address = "Jl. Contoh Alamat No. 123, Kelurahan Dummy, Kecamatan Tester, Jakarta";

        return view('customer.checkout.index', compact(
            'cartItems',
            'totalItems',
            'subtotalBeforeDiscount',
            'totalDiscount',
            'totalPrice',
            'user',
            'address'
        ));
    }
}
