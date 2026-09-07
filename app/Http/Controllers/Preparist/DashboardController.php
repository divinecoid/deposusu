<?php

namespace App\Http\Controllers\Preparist;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\TrxOrder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user->isPreparist(), 403);

        $today = Carbon::today();

        $stats = [
            'new_orders' => TrxOrder::where('status', OrderStatusEnum::ON_PROCESS)->count(),
            'processing' => TrxOrder::where('preparist_id', $user->id)->where('status', OrderStatusEnum::ON_PREPARATION)->count(),
            'waiting_driver' => TrxOrder::where('preparist_id', $user->id)->where('status', OrderStatusEnum::PREPARED)->count(),
            'completed_today' => TrxOrder::where('preparist_id', $user->id)
                ->whereIn('status', [OrderStatusEnum::PREPARED, OrderStatusEnum::ON_DELIVERY, OrderStatusEnum::DELIVERED, OrderStatusEnum::DONE])
                ->whereDate('prepared_at', $today)
                ->count(),
        ];

        return view('preparist.dashboard', compact('stats'));
    }
}
