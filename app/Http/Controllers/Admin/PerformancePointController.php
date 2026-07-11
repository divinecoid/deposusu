<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeePerformancePoint;
use App\Models\User;
use App\Models\TrxOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformancePointController extends Controller
{
    /**
     * Dashboard — leaderboard + summary
     */
    public function index(Request $request)
    {
        $period    = $request->get('period', 'month'); // today, week, month, all
        $role      = $request->get('role', '');
        $search    = $request->get('search', '');

        // Date range based on period
        $dateFrom = match ($period) {
            'today' => now()->startOfDay(),
            'week'  => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => null,
        };

        // Leaderboard query
        $leaderboardQuery = EmployeePerformancePoint::query()
            ->select('user_id', DB::raw('SUM(points) as total_points'), DB::raw('COUNT(*) as total_activities'))
            ->groupBy('user_id')
            ->orderByDesc('total_points');

        if ($dateFrom) {
            $leaderboardQuery->where('activity_date', '>=', $dateFrom);
        }

        // Get user IDs first to filter by role/search
        $userQuery = User::whereIn('role', ['driver', 'preparist']);
        if ($role) {
            $userQuery->where('role', $role);
        }
        if ($search) {
            $userQuery->where('name', 'like', "%{$search}%");
        }
        $filteredUserIds = $userQuery->pluck('id');
        $leaderboardQuery->whereIn('user_id', $filteredUserIds);

        $leaderboard = $leaderboardQuery->get()->map(function ($item) {
            $item->user = User::find($item->user_id);
            return $item;
        })->filter(fn($item) => $item->user !== null);

        // Summary stats
        $summaryQuery = EmployeePerformancePoint::query();
        if ($dateFrom) {
            $summaryQuery->where('activity_date', '>=', $dateFrom);
        }

        $totalPoints     = (clone $summaryQuery)->sum('points');
        $totalActivities = (clone $summaryQuery)->count();
        $packingPoints   = (clone $summaryQuery)->where('activity_type', 'packing_completed')->sum('points');
        $deliveryPoints  = (clone $summaryQuery)->where('activity_type', 'delivery_completed')->sum('points');
        $activeEmployees = (clone $summaryQuery)->distinct('user_id')->count('user_id');

        // Recent activities
        $recentActivities = EmployeePerformancePoint::with(['user', 'order'])
            ->latest('activity_date')
            ->limit(20)
            ->get();

        return view('admin.performance.index', compact(
            'leaderboard', 'totalPoints', 'totalActivities', 'packingPoints',
            'deliveryPoints', 'activeEmployees', 'recentActivities',
            'period', 'role', 'search'
        ));
    }

    /**
     * Employee detail — history poin per karyawan
     */
    public function show(User $user, Request $request)
    {
        $period = $request->get('period', 'month');
        $dateFrom = match ($period) {
            'today' => now()->startOfDay(),
            'week'  => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => null,
        };

        $pointsQuery = EmployeePerformancePoint::where('user_id', $user->id)
            ->with('order')
            ->latest('activity_date');

        if ($dateFrom) {
            $pointsQuery->where('activity_date', '>=', $dateFrom);
        }

        $points      = $pointsQuery->paginate(30);
        $totalPoints = EmployeePerformancePoint::where('user_id', $user->id)
            ->when($dateFrom, fn($q) => $q->where('activity_date', '>=', $dateFrom))
            ->sum('points');
        $totalActivities = EmployeePerformancePoint::where('user_id', $user->id)
            ->when($dateFrom, fn($q) => $q->where('activity_date', '>=', $dateFrom))
            ->count();

        // Monthly breakdown (last 6 months)
        $monthlyBreakdown = EmployeePerformancePoint::where('user_id', $user->id)
            ->where('activity_date', '>=', now()->subMonths(6)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(activity_date, '%Y-%m') as month"),
                DB::raw('SUM(points) as total_points'),
                DB::raw('COUNT(*) as total_activities')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.performance.show', compact(
            'user', 'points', 'totalPoints', 'totalActivities', 'monthlyBreakdown', 'period'
        ));
    }

    /**
     * Manual point assignment (admin override)
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'activity_type' => 'required|in:packing_completed,delivery_completed,bonus,penalty',
            'points'        => 'required|integer|min:-100|max:100',
            'description'   => 'required|string|max:255',
        ]);

        EmployeePerformancePoint::create([
            'user_id'       => $request->user_id,
            'order_id'      => null,
            'activity_type' => $request->activity_type,
            'points'        => $request->points,
            'description'   => $request->description,
            'activity_date' => now(),
        ]);

        return back()->with('success', 'Poin berhasil ditambahkan.');
    }
}
