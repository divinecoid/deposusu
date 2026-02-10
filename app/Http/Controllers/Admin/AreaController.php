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
        ]);

        MdxArea::create($request->all());

        return back()->with('success', 'Area created successfully.');
    }

    public function update(Request $request, MdxArea $area)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('mdx_areas')->ignore($area->id)],
            'description' => 'nullable|string',
            'branch_id' => 'required|exists:mdx_branches,id',
        ]);

        $area->update($request->all());

        return back()->with('success', 'Area updated successfully.');
    }

    public function destroy(MdxArea $area)
    {
        $area->delete();
        return back()->with('success', 'Area deleted successfully.');
    }
}
