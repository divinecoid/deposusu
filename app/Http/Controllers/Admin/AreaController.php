<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MdxArea;
use App\Models\MdxBranch;
use App\Models\AreaDeliverySchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    public function index()
    {
        $areas    = MdxArea::with(['branch', 'deliverySchedules.driver'])->latest()->get();
        $branches = MdxBranch::where('is_active', true)->get();
        return view('admin.master.branches.index', compact('areas', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:50|unique:mdx_areas,code',
            'description' => 'nullable|string',
            'branch_id' => 'required|exists:mdx_branches,id',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        MdxArea::create(array_merge(
            $request->only(['name', 'code', 'description', 'branch_id', 'latitude', 'longitude']),
            ['is_active' => true]
        ));

        return back()->with('success', 'Area berhasil ditambahkan.');
    }

    public function update(Request $request, MdxArea $area)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => ['required', 'string', 'max:50', Rule::unique('mdx_areas')->ignore($area->id)],
            'description' => 'nullable|string',
            'branch_id'   => 'required|exists:mdx_branches,id',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
        ]);

        $area->update($request->only(['name', 'code', 'description', 'branch_id', 'latitude', 'longitude']));

        return back()->with('success', 'Area berhasil diperbarui.');
    }

    public function toggleStatus(MdxArea $area)
    {
        $area->update(['is_active' => !$area->is_active]);
        $status = $area->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Area berhasil {$status}.");
    }

    public function destroy(MdxArea $area)
    {
        $area->delete();
        return back()->with('success', 'Area berhasil dihapus.');
    }

    // ─── Delivery Schedule Management ────────────────────────────

    public function storeSchedule(Request $request, MdxArea $area)
    {
        $request->validate([
            'day_of_week' => ['required', 'integer', 'between:1,7', Rule::unique('area_delivery_schedules')->where('area_id', $area->id)],
            'driver_id'   => 'nullable|exists:users,id',
        ]);

        $area->deliverySchedules()->create([
            'day_of_week' => $request->day_of_week,
            'driver_id'   => $request->driver_id,
            'is_active'   => true,
        ]);

        return back()->with('success', 'Jadwal pengiriman berhasil ditambahkan.');
    }

    public function updateSchedule(Request $request, MdxArea $area, AreaDeliverySchedule $schedule)
    {
        $request->validate([
            'day_of_week' => ['required', 'integer', 'between:1,7', Rule::unique('area_delivery_schedules')->where('area_id', $area->id)->ignore($schedule->id)],
            'driver_id'   => 'nullable|exists:users,id',
        ]);

        $schedule->update([
            'day_of_week' => $request->day_of_week,
            'driver_id'   => $request->driver_id,
        ]);

        return back()->with('success', 'Jadwal pengiriman berhasil diperbarui.');
    }

    public function deleteSchedule(MdxArea $area, AreaDeliverySchedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal pengiriman berhasil dihapus.');
    }
}
