<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MdxRack;
use App\Models\MdxWarehouse;
use Illuminate\Http\Request;

class RackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:mdx_warehouses,id',
        ]);
        MdxRack::create($request->all());
        return back()->with('success', 'Rack created successfully.');
    }

    public function destroy(MdxRack $rack)
    {
        $rack->delete();
        return back()->with('success', 'Rack deleted successfully.');
    }
}
