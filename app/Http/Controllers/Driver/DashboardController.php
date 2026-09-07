<?php

namespace App\Http\Controllers\Driver;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\DriverAttendance;
use App\Models\DriverCashCollection;
use App\Models\TrxOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user->isDriver(), 403);

        $today = Carbon::today();
        $attendance = DriverAttendance::where('user_id', $user->id)->whereDate('date', $today)->first();

        $stats = [
            'pending_tasks' => TrxOrder::where('driver_id', $user->id)->where('status', OrderStatusEnum::PREPARED)->count(),
            'active_deliveries' => TrxOrder::where('driver_id', $user->id)->where('status', OrderStatusEnum::ON_DELIVERY)->count(),
            'completed_today' => TrxOrder::where('driver_id', $user->id)->where('status', OrderStatusEnum::DELIVERED)->whereDate('delivered_at', $today)->count(),
        ];

        $pendingCash = DriverCashCollection::where('driver_id', $user->id)->where('is_deposited', false)->sum('amount');

        return view('driver.dashboard', [
            'stats' => $stats,
            'checkedIn' => (bool) $attendance?->check_in_at,
            'checkedOut' => (bool) $attendance?->check_out_at,
            'pendingCash' => $pendingCash,
        ]);
    }

    public function checkIn(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isDriver(), 403);

        $today = Carbon::today();

        DriverAttendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['check_in_at' => now()]
        );

        return back()->with('success', 'Check-in berhasil. Selamat bertugas!');
    }

    public function checkOut(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isDriver(), 403);

        $today = Carbon::today();

        DriverAttendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['check_out_at' => now()]
        );

        return back()->with('success', 'Check-out berhasil. Sampai jumpa!');
    }
}
