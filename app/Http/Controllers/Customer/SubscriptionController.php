<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\TrxSubscription;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = TrxSubscription::with('product')
            ->where('customer_id', Auth::id())
            ->orderByDesc('is_active')
            ->orderByDesc('created_at')
            ->get();

        return view('customer.subscriptions.index', compact('subscriptions'));
    }

    public function toggle(TrxSubscription $subscription)
    {
        abort_unless($subscription->customer_id === Auth::id(), 403);

        $subscription->update(['is_active' => !$subscription->is_active]);

        return back()->with('success', $subscription->is_active
            ? 'Langganan diaktifkan kembali.'
            : 'Langganan dijeda. Tidak akan ada pesanan otomatis sampai Anda aktifkan lagi.');
    }

    public function destroy(TrxSubscription $subscription)
    {
        abort_unless($subscription->customer_id === Auth::id(), 403);

        $subscription->delete();

        return back()->with('success', 'Langganan dihapus.');
    }
}
