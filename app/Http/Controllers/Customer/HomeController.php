<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MdxProduct;
use App\Models\HeroSlide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get products from database with discounts eager loaded
        $products = MdxProduct::with(['discounts', 'categories'])->orderBy('created_at', 'desc')->get();
        // Get all categories
        $categories = \App\Models\MdxCategory::orderBy('name')->get();
        // Get active hero slides
        $heroSlides = HeroSlide::active()->ordered()->get();

        return view('customer.home', compact('products', 'categories', 'heroSlides'));
    }

    public function search(Request $request)
    {
        $query = MdxProduct::with(['discounts', 'categories'])->orderBy('created_at', 'desc');

        // Filter by Category
        if ($request->has('category') && $request->category !== 'all') {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('mdx_categories.id', $request->category);
            });
        }

        // Filter by Search Query
        if ($request->has('q') && !empty($request->q)) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $products = $query->get();

        return view('customer.partials.product_grid', compact('products'))->render();
    }
}
