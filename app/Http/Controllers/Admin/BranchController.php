<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MdxBranch;
use App\Models\MdxArea;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    public function index()
    {
        $branches = MdxBranch::with(['areas.deliverySchedules.driver'])->withCount('areas')->latest()->get();
        $drivers  = User::where('role', 'driver')->orderBy('name')->get();
        return view('admin.master.branches.index', compact('branches', 'drivers'));
    }

    public function show(MdxBranch $branch)
    {
        $branch->load(['areas']);
        return view('admin.master.branches.show', compact('branch'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'code'     => 'required|string|max:50|unique:mdx_branches,code',
            'address'  => 'nullable|string',
            'phone'    => 'nullable|string|max:30',
            'pic_name' => 'nullable|string|max:100',
        ]);

        MdxBranch::create(array_merge($request->only(['name','code','address','phone','pic_name']), [
            'is_active' => true,
        ]));

        return back()->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function update(Request $request, MdxBranch $branch)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'code'     => ['required', 'string', 'max:50', Rule::unique('mdx_branches')->ignore($branch->id)],
            'address'  => 'nullable|string',
            'phone'    => 'nullable|string|max:30',
            'pic_name' => 'nullable|string|max:100',
        ]);

        $branch->update($request->only(['name','code','address','phone','pic_name']));

        return back()->with('success', 'Data cabang berhasil diperbarui.');
    }

    public function toggleStatus(MdxBranch $branch)
    {
        $branch->update(['is_active' => !$branch->is_active]);
        $status = $branch->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Cabang berhasil {$status}.");
    }

    public function destroy(MdxBranch $branch)
    {
        $branch->delete();
        return back()->with('success', 'Cabang berhasil dihapus.');
    }
}
