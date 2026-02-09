<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MdxProduct;
use App\Models\MdxCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = MdxProduct::with('category')->latest()->get();
        return view('admin.master.products.index', compact('products'));
    }

    public function create()
    {
        $categories = MdxCategory::all();
        return view('admin.master.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:mdx_categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url', // Assuming simple URL input for now as per previous code style
            'sku' => 'nullable|string|unique:mdx_products,sku',
        ]);

        MdxProduct::create($request->all());

        return redirect()->route('admin.master.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(MdxProduct $product)
    {
        $categories = MdxCategory::all();
        return view('admin.master.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, MdxProduct $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:mdx_categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'sku' => 'nullable|string|unique:mdx_products,sku,' . $product->id,
        ]);

        $product->update($request->all());

        return redirect()->route('admin.master.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(MdxProduct $product)
    {
        $product->delete();
        return redirect()->route('admin.master.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
