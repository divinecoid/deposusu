<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MdxArea;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:mdx_areas,code',
            'description' => 'nullable|string',
            'branch_id' => 'required|exists:mdx_branches,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->all();
        $daysData = [
            'is_monday' => $request->has('is_monday'),
            'is_tuesday' => $request->has('is_tuesday'),
            'is_wednesday' => $request->has('is_wednesday'),
            'is_thursday' => $request->has('is_thursday'),
            'is_friday' => $request->has('is_friday'),
            'is_saturday' => $request->has('is_saturday'),
            'is_sunday' => $request->has('is_sunday'),
        ];

        MdxArea::create(array_merge($data, $daysData));

        return back()->with('success', 'Area created successfully.');
    }

    public function update(Request $request, MdxArea $area)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('mdx_areas')->ignore($area->id)],
            'description' => 'nullable|string',
            'branch_id' => 'required|exists:mdx_branches,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->all();
        $daysData = [
            'is_monday' => $request->has('is_monday'),
            'is_tuesday' => $request->has('is_tuesday'),
            'is_wednesday' => $request->has('is_wednesday'),
            'is_thursday' => $request->has('is_thursday'),
            'is_friday' => $request->has('is_friday'),
            'is_saturday' => $request->has('is_saturday'),
            'is_sunday' => $request->has('is_sunday'),
        ];

        $area->update(array_merge($data, $daysData));

        return back()->with('success', 'Area updated successfully.');
    }

    public function destroy(MdxArea $area)
    {
        $area->delete();
        return back()->with('success', 'Area deleted successfully.');
    }
}
