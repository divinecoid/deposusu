<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\XenditService;
use Illuminate\Http\Request;
use App\Models\TrxCart;
use App\Models\TrxCartItem;

class CheckoutController extends Controller
{
    /**
     * Display the fast checkout page.
     */
    public function index(Request $request, XenditService $xendit)
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

        // Prefill the shipping address from the customer profile. Leave it empty
        // when there is none so the customer types a real address instead of
        // submitting placeholder text.
        $user = auth()->user();
        $address = optional($user->customerProfile)->address ?? '';

        // COD ("bayar nanti") is a manual admin decision per customer —
        // unverified customers must pay upfront via an online channel.
        $isVerifiedCustomer = (bool) optional($user->customerProfile)->is_verified;

        // Every channel this store accepts, with the convenience fee and
        // resulting grand total precomputed for THIS cart's amount so the
        // page can render fees without any client-side math.
        $paymentChannels = collect($xendit->channels())->map(function ($channel, $code) use ($xendit, $totalPrice) {
            $fee = $xendit->computeFee($code, (float) $totalPrice);

            return array_merge($channel, [
                'code' => $code,
                'convenience_fee' => $fee['fee'],
                'total_with_fee' => $fee['total'],
            ]);
        })->groupBy('group');

        return view('customer.checkout.index', compact(
            'cartItems',
            'totalItems',
            'subtotalBeforeDiscount',
            'totalDiscount',
            'totalPrice',
            'user',
            'address',
            'paymentChannels',
            'isVerifiedCustomer'
        ));
    }
}
