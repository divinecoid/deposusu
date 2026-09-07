<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\MdxCategory;
use App\Models\MdxProduct;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = MdxProduct::with(['discounts', 'categories'])->orderBy('created_at', 'desc');

        if ($request->filled('category') && $request->category !== 'all') {
            if ($request->category === 'promo') {
                $query->whereHas('discounts', fn ($q) => $q->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now()));
            } else {
                $query->whereHas('categories', fn ($q) => $q->where('mdx_categories.id', $request->category));
            }
        }

        $products = $query->get();

        if ($request->filled('q')) {
            $term = strtolower(trim($request->q));
            $products = $products->filter(fn ($p) => str_contains(strtolower($p->name), $term))->values();
        }

        return response()->json([
            'success' => true,
            'products' => $products->map(fn ($p) => $this->formatProduct($p)),
        ]);
    }

    public function show(MdxProduct $product)
    {
        $product->load(['discounts', 'categories']);

        $related = MdxProduct::with(['discounts'])
            ->whereHas('categories', fn ($q) => $q->whereIn('mdx_categories.id', $product->categories->pluck('id')))
            ->where('id', '!=', $product->id)
            ->take(6)
            ->get();

        return response()->json([
            'success' => true,
            'product' => $this->formatProduct($product, detailed: true),
            'related' => $related->map(fn ($p) => $this->formatProduct($p)),
        ]);
    }

    public function categories()
    {
        return response()->json([
            'success' => true,
            'categories' => MdxCategory::orderBy('name')->get(['id', 'name']),
        ]);
    }

    private function formatProduct(MdxProduct $product, bool $detailed = false): array
    {
        $data = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'discounted_price' => (float) $product->discounted_price,
            'has_discount' => (bool) $product->active_discount,
            'image' => $product->image
                ? (str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image)
                : null,
            'stock' => (int) $product->stock,
            'categories' => $product->categories->pluck('name'),
        ];

        if ($detailed) {
            $data['description'] = $product->description;
        }

        return $data;
    }
}
