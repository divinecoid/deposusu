<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::ordered()->get();
        return view('admin.master.hero-slides.index', compact('slides'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:text,image',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'nullable|integer',
        ]);

        $data = $request->only(['type', 'title', 'subtitle', 'order']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('hero-slides', 'public');
            $data['image_path'] = $path;
        }

        HeroSlide::create($data);

        return back()->with('success', 'Hero slide created successfully.');
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $request->validate([
            'type' => 'required|in:text,image',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['type', 'title', 'subtitle']);

        // Handle active status
        $data['is_active'] = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($heroSlide->image_path) {
                Storage::disk('public')->delete($heroSlide->image_path);
            }

            $path = $request->file('image')->store('hero-slides', 'public');
            $data['image_path'] = $path;
        }

        $heroSlide->update($data);

        return back()->with('success', 'Hero slide updated successfully.');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        // Delete image if exists
        if ($heroSlide->image_path) {
            Storage::disk('public')->delete($heroSlide->image_path);
        }

        $heroSlide->delete();

        return back()->with('success', 'Hero slide deleted successfully.');
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'slides' => 'required|array',
            'slides.*.id' => 'required|exists:hero_slides,id',
            'slides.*.order' => 'required|integer',
        ]);

        foreach ($request->slides as $slide) {
            HeroSlide::where('id', $slide['id'])->update(['order' => $slide['order']]);
        }

        return response()->json(['success' => true]);
    }
}
