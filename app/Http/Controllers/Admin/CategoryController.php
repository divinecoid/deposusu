<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MdxCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = MdxCategory::withCount('products')->get();
        return view('admin.master.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        MdxCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon, // Optional
        ]);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, MdxCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon,
        ]);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy(MdxCategory $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
