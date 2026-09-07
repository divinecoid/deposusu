<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\DriverCashCollection;
use Illuminate\Support\Facades\Auth;

class CashController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->isDriver(), 403);

        $collections = DriverCashCollection::with('order')
            ->where('driver_id', Auth::id())
            ->orderByDesc('collected_at')
            ->paginate(20);

        $pendingTotal = DriverCashCollection::where('driver_id', Auth::id())->where('is_deposited', false)->sum('amount');

        return view('driver.cash.index', compact('collections', 'pendingTotal'));
    }
}
