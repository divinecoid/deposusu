<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MarketplaceCustomerController extends Controller
{
    public function index()
    {
        return view('admin.marketplace.customers');
    }
}
