<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MdxProduct;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get products from database with discounts eager loaded
        $products = MdxProduct::with(['discounts', 'category'])->orderBy('created_at', 'desc')->get();
        // Get all categories
        $categories = \App\Models\MdxCategory::orderBy('name')->get();

        return view('customer.home', compact('products', 'categories'));
    }

    public function search(Request $request)
    {
        $query = MdxProduct::with(['discounts', 'category'])->orderBy('created_at', 'desc');

        // Filter by Category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        // Filter by Search Query
        if ($request->has('q') && !empty($request->q)) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $products = $query->get();

        return view('customer.partials.product_grid', compact('products'))->render();
    }
}
