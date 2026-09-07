<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverCashCollection;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Tracks cash a driver has physically collected from COD customers until
 * it's handed over ("disetor") to the office — a separate concern from the
 * order itself already being marked PAID.
 */
class DriverCashController extends Controller
{
    public function index(Request $request)
    {
        $drivers = User::where('role', 'driver')->orderBy('name')->get();

        $selectedDriverId = $request->query('driver_id');

        $query = DriverCashCollection::with(['driver', 'order'])
            ->orderBy('is_deposited')
            ->orderByDesc('collected_at');

        if ($selectedDriverId) {
            $query->where('driver_id', $selectedDriverId);
        }

        $collections = $query->paginate(20)->withQueryString();

        $pendingTotal = DriverCashCollection::where('is_deposited', false)
            ->when($selectedDriverId, fn ($q) => $q->where('driver_id', $selectedDriverId))
            ->sum('amount');

        return view('admin.driver-cash.index', compact('drivers', 'collections', 'pendingTotal', 'selectedDriverId'));
    }

    public function confirm(DriverCashCollection $collection)
    {
        $collection->update([
            'is_deposited' => true,
            'deposited_at' => now(),
            'confirmed_by' => auth()->id(),
        ]);

        return back()->with('success', "Setoran #{$collection->id} dikonfirmasi diterima.");
    }

    public function confirmAllForDriver(Request $request, User $driver)
    {
        DriverCashCollection::where('driver_id', $driver->id)
            ->where('is_deposited', false)
            ->update([
                'is_deposited' => true,
                'deposited_at' => now(),
                'confirmed_by' => auth()->id(),
            ]);

        return back()->with('success', "Semua setoran {$driver->name} dikonfirmasi diterima.");
    }
}
