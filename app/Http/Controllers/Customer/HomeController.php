<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MdxProduct;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get products from database
        $products = MdxProduct::orderBy('created_at', 'desc')->get();

        return view('customer.home', compact('products'));
    }
}
