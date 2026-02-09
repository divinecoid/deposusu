<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MdxWarehouse;
use App\Models\MdxRack;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = MdxWarehouse::withCount('racks')->get();
        return view('admin.master.warehouses.index', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'address' => 'nullable|string']);
        MdxWarehouse::create($request->all());
        return back()->with('success', 'Warehouse created successfully.');
    }

    public function update(Request $request, MdxWarehouse $warehouse)
    {
        $request->validate(['name' => 'required|string|max:255', 'address' => 'nullable|string']);
        $warehouse->update($request->all());
        return back()->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(MdxWarehouse $warehouse)
    {
        $warehouse->delete();
        return back()->with('success', 'Warehouse deleted successfully.');
    }

    // Rack Management could be here or separate controller. 
    // For simplicity, let's keep it here but accessed via different routes or strict REST.
    // I'll add separate methods for Rack CRUD if needed, or just nested resource.
    // Let's create a RackController to be clean.
}
