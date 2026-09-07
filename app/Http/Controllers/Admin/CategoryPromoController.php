<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MdxCategory;
use App\Models\MdxProductDiscount;
use Illuminate\Http\Request;

/**
 * Category-wide promos ("promo per kategori/merek" from the legacy app) —
 * distinct from ProductController's per-product discount management since
 * a category promo isn't tied to any single product's edit page.
 */
class CategoryPromoController extends Controller
{
    public function index()
    {
        $promos = MdxProductDiscount::where('scope', MdxProductDiscount::SCOPE_CATEGORY)
            ->with('category')
            ->latest()
            ->get();

        $categories = MdxCategory::orderBy('name')->get();

        return view('admin.master.promos.index', compact('promos', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'nullable|string|max:255',
            'category_id' => 'required|exists:mdx_categories,id',
            'discount_type' => 'required|in:PERCENTAGE,FIXED',
            'discount_value' => 'required|numeric|min:0',
            'minimum_quantity' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        MdxProductDiscount::create([
            'scope' => MdxProductDiscount::SCOPE_CATEGORY,
            'category_id' => $request->category_id,
            'label' => $request->label,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'minimum_quantity' => $request->minimum_quantity,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.master.promos.index')->with('success', 'Promo kategori berhasil ditambahkan.');
    }

    public function toggle(MdxProductDiscount $discount)
    {
        $discount->update(['is_active' => !$discount->is_active]);

        return back()->with('success', 'Status promo diperbarui.');
    }

    public function destroy(MdxProductDiscount $discount)
    {
        $discount->delete();

        return back()->with('success', 'Promo dihapus.');
    }
}
