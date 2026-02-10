<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MdxBranch;
use App\Models\MdxArea;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    public function index()
    {
        $branches = MdxBranch::with('areas')->withCount('areas')->get();
        return view('admin.master.branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:mdx_branches,code',
            'address' => 'nullable|string',
        ]);

        MdxBranch::create($request->all());

        return back()->with('success', 'Branch created successfully.');
    }

    public function update(Request $request, MdxBranch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('mdx_branches')->ignore($branch->id)],
            'address' => 'nullable|string',
        ]);

        $branch->update($request->all());

        return back()->with('success', 'Branch updated successfully.');
    }

    public function destroy(MdxBranch $branch)
    {
        $branch->delete();
        return back()->with('success', 'Branch deleted successfully.');
    }
}
