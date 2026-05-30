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
            'variants',
            'wholesales',
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
            'image' => 'nullable|image|max:2048',
            'sku' => 'nullable|string|unique:mdx_products,sku',
            'barcode' => 'nullable|string|unique:mdx_products,barcode',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string',
            'variants.*.sku' => 'nullable|string',
            'variants.*.price' => 'required|numeric',
            'variants.*.stock' => 'required|integer',
            'wholesales' => 'nullable|array',
            'wholesales.*.min_qty' => 'required|integer|min:2',
            'wholesales.*.price' => 'required|numeric',
        ]);

        $data = $request->except(['categories', 'variants', 'wholesales']);

        if (empty($data['sku'])) {
            $data['sku'] = 'DP-' . strtoupper(Str::random(6));
        }
        
        if (empty($data['barcode'])) {
            $data['barcode'] = $data['sku'];
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = 'storage/' . $path;
        }

        $product = MdxProduct::create($data);
        $product->categories()->attach($request->categories);

        if ($request->has('variants') && is_array($request->variants)) {
            foreach ($request->variants as $variant) {
                if (empty($variant['sku'])) {
                    $variant['sku'] = $product->sku . '-' . strtoupper(Str::random(3));
                }
                $product->variants()->create($variant);
            }
        }

        if ($request->has('wholesales') && is_array($request->wholesales)) {
            foreach ($request->wholesales as $wholesale) {
                $product->wholesales()->create($wholesale);
            }
        }

        return redirect()->route('admin.master.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(MdxProduct $product)
    {
        $categories = MdxCategory::all();
        $product->load(['discounts', 'categories', 'variants', 'wholesales']);
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
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|exists:mdx_product_variants,id',
            'variants.*.name' => 'required|string',
            'variants.*.sku' => 'nullable|string',
            'variants.*.price' => 'required|numeric',
            'variants.*.stock' => 'required|integer',
            'wholesales' => 'nullable|array',
            'wholesales.*.id' => 'nullable|exists:mdx_product_wholesales,id',
            'wholesales.*.min_qty' => 'required|integer|min:2',
            'wholesales.*.price' => 'required|numeric',
        ]);

        $data = $request->except(['categories', 'variants', 'wholesales']);

        if (empty($data['sku'])) {
            $data['sku'] = 'DP-' . strtoupper(Str::random(6));
        }
        
        if (empty($data['barcode'])) {
            $data['barcode'] = $data['sku'];
        }

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

        // Update variants
        $variantIdsToKeep = [];
        if ($request->has('variants') && is_array($request->variants)) {
            foreach ($request->variants as $variantData) {
                if (empty($variantData['sku'])) {
                    $variantData['sku'] = $product->sku . '-' . strtoupper(Str::random(3));
                }
                
                if (isset($variantData['id']) && $variantData['id']) {
                    $variant = $product->variants()->find($variantData['id']);
                    if ($variant) {
                        $variant->update($variantData);
                        $variantIdsToKeep[] = $variant->id;
                    }
                } else {
                    $newVariant = $product->variants()->create($variantData);
                    $variantIdsToKeep[] = $newVariant->id;
                }
            }
        }
        $product->variants()->whereNotIn('id', $variantIdsToKeep)->delete();

        // Update wholesales
        $wholesaleIdsToKeep = [];
        if ($request->has('wholesales') && is_array($request->wholesales)) {
            foreach ($request->wholesales as $wholesaleData) {
                if (isset($wholesaleData['id']) && $wholesaleData['id']) {
                    $wholesale = $product->wholesales()->find($wholesaleData['id']);
                    if ($wholesale) {
                        $wholesale->update($wholesaleData);
                        $wholesaleIdsToKeep[] = $wholesale->id;
                    }
                } else {
                    $newWholesale = $product->wholesales()->create($wholesaleData);
                    $wholesaleIdsToKeep[] = $newWholesale->id;
                }
            }
        }
        $product->wholesales()->whereNotIn('id', $wholesaleIdsToKeep)->delete();

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
