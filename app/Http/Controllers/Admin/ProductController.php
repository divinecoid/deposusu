<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MdxProduct;
use App\Models\MdxCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\MdxProductDiscount;

class ProductController extends Controller
{
    public function index()
    {
        $products = MdxProduct::with([
            'categories',
            'discounts' => function ($q) {
                $q->active();
            }
        ])->latest()->get();
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
            'categories' => 'required|array',
            'categories.*' => 'exists:mdx_categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // Validation for image file
            'sku' => 'nullable|string|unique:mdx_products,sku',
            'barcode' => 'nullable|string|unique:mdx_products,barcode',
        ]);

        $data = $request->except('categories');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = 'storage/' . $path;
        }

        $product = MdxProduct::create($data);
        $product->categories()->attach($request->categories);

        return redirect()->route('admin.master.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(MdxProduct $product)
    {
        $categories = MdxCategory::all();
        $product->load(['discounts', 'categories']);
        return view('admin.master.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, MdxProduct $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'categories' => 'required|array',
            'categories.*' => 'exists:mdx_categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'sku' => 'nullable|string|unique:mdx_products,sku,' . $product->id,
            'barcode' => 'nullable|string|unique:mdx_products,barcode,' . $product->id,
        ]);

        $data = $request->except('categories');

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && str_starts_with($product->image, 'storage/')) {
                $oldPath = str_replace('storage/', '', $product->image);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('products', 'public');
            $data['image'] = 'storage/' . $path;
        }

        $product->update($data);
        $product->categories()->sync($request->categories);

        return redirect()->route('admin.master.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(MdxProduct $product)
    {
        $product->delete();
        return redirect()->route('admin.master.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function storeDiscount(Request $request, MdxProduct $product)
    {
        $request->validate([
            'discount_type' => 'required|in:PERCENTAGE,FIXED',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $product->discounts()->create([
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.master.products.edit', $product->id)
            ->with('success', 'Discount added successfully.');
    }

    public function toggleDiscountStatus(MdxProductDiscount $discount)
    {
        $discount->update([
            'is_active' => !$discount->is_active
        ]);

        return back()->with('success', 'Discount status updated.');
    }

    public function updateDiscount(Request $request, MdxProductDiscount $discount)
    {
        $request->validate([
            'discount_type' => 'required|in:PERCENTAGE,FIXED',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $discount->update([
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return back()->with('success', 'Discount updated successfully.');
    }

    public function destroyDiscount(MdxProductDiscount $discount)
    {
        $discount->delete();
        return back()->with('success', 'Discount deleted successfully.');
    }
}
