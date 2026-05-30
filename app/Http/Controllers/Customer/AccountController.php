<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MdxCustomer;
use App\Models\Order;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $customer = MdxCustomer::where('user_id', $user->id)->first();
        
        // Fetch recent orders
        $recentOrders = Order::where('customer_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        return view('customer.account', compact('user', 'customer', 'recentOrders'));
    }
}
