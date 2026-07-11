@extends('layouts.admin')

@section('header', 'Detail Performa Karyawan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.performance.index') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-3">
                    Detail Performa: {{ $user->name }}
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Role: <span class="font-semibold text-slate-700">{{ $user->role === 'driver' ? 'Kurir' : 'Preparist' }}</span>
                </p>
            </div>
        </div>
        
        <form method="GET" class="flex items-center gap-2">
            <select name="period" class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white shadow-sm" onchange="this.form.submit()">
                <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Semua</option>
            </select>
        </form>
    </div>

    {{-- Stats Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500 mb-1">Total Poin</p>
                <p class="text-4xl font-extrabold text-amber-600">{{ number_format($totalPoints) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500 mb-1">Total Aktivitas</p>
                <p class="text-4xl font-extrabold text-slate-800">{{ number_format($totalActivities) }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- History Table --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Riwayat Poin & Aktivitas</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left">
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Tanggal & Waktu</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Tipe Aktivitas</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Keterangan</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase text-center">Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($points as $point)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $point->activity_date->format('d M Y') }}</div>
                                <div class="text-xs text-slate-500">{{ $point->activity_date->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($point->activity_type === 'packing_completed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold">
                                        📦 Packing Selesai
                                    </span>
                                @elseif($point->activity_type === 'delivery_completed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">
                                        🚚 Delivery Selesai
                                    </span>
                                @elseif($point->activity_type === 'bonus')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                        ⭐ Bonus
                                    </span>
                                @elseif($point->activity_type === 'penalty')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-red-50 text-red-700 text-xs font-semibold">
                                        ⚠️ Penalti
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                        {{ $point->activity_label }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-700">{{ $point->description }}</div>
                                @if($point->order)
                                <a href="{{ route('admin.orders.show', $point->order_id) }}" class="text-xs font-mono text-violet-600 hover:underline mt-0.5 inline-block">
                                    Order #{{ $point->order->order_number ?? $point->order_id }}
                                </a>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-bold {{ $point->points >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $point->points > 0 ? '+' : '' }}{{ $point->points }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-sm">
                                Belum ada riwayat aktivitas untuk periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($points->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $points->appends(['period' => $period])->links() }}
            </div>
            @endif
        </div>

        {{-- Monthly Breakdown --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden h-fit">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Tren 6 Bulan Terakhir</h3>
            </div>
            <div class="p-6">
                @if($monthlyBreakdown->count() > 0)
                    <div class="space-y-4">
                        @foreach($monthlyBreakdown as $month)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->translatedFormat('F Y') }}</p>
                                <p class="text-xs text-slate-500">{{ $month->total_activities }} aktivitas</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-amber-600">{{ number_format($month->total_points) }} pts</p>
                            </div>
                        </div>
                        @if(!$loop->last)
                        <hr class="border-slate-100">
                        @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-slate-400 text-sm italic py-4">Data bulanan belum tersedia</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
