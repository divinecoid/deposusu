<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MarketplacePaymentController extends Controller
{
    public function index()
    {
        return view('admin.marketplace.payments');
    }
}
