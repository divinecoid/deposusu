<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\MdxCustomer;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $customer = MdxCustomer::where('user_id', $user->id)->first();

        return view('customer.account', compact('user', 'customer'));
    }
}
