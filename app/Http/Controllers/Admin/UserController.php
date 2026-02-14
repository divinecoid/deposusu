<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MdxDriver;
use App\Models\MdxCustomer; // Assuming this model exists
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function indexCustomers()
    {
        $customers = User::where('role', 'customer')->with('customerProfile')->latest()->paginate(20);
        return view('admin.master.customers.index', compact('customers'));
    }

    public function indexDrivers()
    {
        $drivers = User::where('role', 'driver')->with('driverProfile')->latest()->paginate(20);
        return view('admin.master.drivers.index', compact('drivers'));
    }

    public function storeDriver(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'license_plate' => 'required|string',
            'vehicle_type' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'driver',
        ]);

        MdxDriver::create([
            'user_id' => $user->id,
            'license_plate' => $request->license_plate,
            'vehicle_type' => $request->vehicle_type,
        ]);

        return back()->with('success', 'Driver created successfully.');
    }

    public function updateDriver(Request $request, User $driver)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $driver->id,
            'password' => 'nullable|string|min:8',
            'license_plate' => 'required|string',
            'vehicle_type' => 'required|string',
        ]);

        $driver->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $driver->update(['password' => Hash::make($request->password)]);
        }

        $driver->driverProfile->update([
            'license_plate' => $request->license_plate,
            'vehicle_type' => $request->vehicle_type,
        ]);

        return back()->with('success', 'Driver updated successfully.');
    }

    public function destroyDriver(User $driver)
    {
        $driver->driverProfile->delete();
        $driver->delete();
        return back()->with('success', 'Driver deleted successfully.');
    }
}
