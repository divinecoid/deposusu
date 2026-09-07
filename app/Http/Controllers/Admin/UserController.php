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
        $customers = User::where('role', 'customer')->with(['customerProfile.area'])->latest()->paginate(20);
        $areas = \App\Models\MdxArea::orderBy('name')->get();
        return view('admin.master.customers.index', compact('customers', 'areas'));
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

    public function updateCustomer(Request $request, User $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'area_id' => 'nullable|exists:mdx_areas,id',
        ]);

        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($customer->customerProfile) {
            $customer->customerProfile->update([
                'phone' => $request->phone,
                'address' => $request->address,
                'area_id' => $request->area_id,
            ]);
        } else {
            MdxCustomer::create([
                'user_id' => $customer->id,
                'phone' => $request->phone,
                'address' => $request->address,
                'area_id' => $request->area_id,
            ]);
        }

        return back()->with('success', 'Customer updated successfully.');
    }

    public function destroyCustomer(User $customer)
    {
        if ($customer->customerProfile) {
            $customer->customerProfile->delete();
        }
        $customer->delete();
        return back()->with('success', 'Customer deleted successfully.');
    }

    /**
     * Verification is a manual, admin-only decision: it lets a customer
     * choose "bayar nanti" (COD) at checkout instead of paying upfront.
     */
    public function toggleCustomerVerification(User $customer)
    {
        $profile = $customer->customerProfile ?? MdxCustomer::create(['user_id' => $customer->id]);

        $verified = !$profile->is_verified;

        $profile->update([
            'is_verified' => $verified,
            'verified_by' => $verified ? auth()->id() : null,
            'verified_at' => $verified ? now() : null,
        ]);

        return back()->with('success', $verified
            ? "{$customer->name} sekarang bisa checkout dengan COD (bayar nanti)."
            : "{$customer->name} sekarang harus membayar di muka saat checkout.");
    }
}
