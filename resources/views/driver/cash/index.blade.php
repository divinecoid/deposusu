@extends('layouts.ops')

@section('title', 'Setoran Saya - DEPOSUSU')

@php
$opsRoleLabel = 'Driver';
$opsNavItems = [
    ['url' => route('driver.dashboard'), 'icon' => 'home', 'label' => 'Beranda', 'active' => request()->routeIs('driver.dashboard')],
    ['url' => route('driver.orders.index'), 'icon' => 'truck', 'label' => 'Antar', 'active' => request()->routeIs('driver.orders.*')],
    ['url' => route('driver.cash.index'), 'icon' => 'cash', 'label' => 'Setoran', 'active' => request()->routeIs('driver.cash.*')],
];
@endphp

@section('content')
<h1 class="text-xl font-extrabold text-slate-900 mb-4">Setoran Saya</h1>

<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-5">
    <p class="text-xs font-semibold text-amber-700 uppercase">Total Belum Disetor</p>
    <p class="text-2xl font-extrabold text-amber-700">Rp {{ number_format($pendingTotal, 0, ',', '.') }}</p>
</div>

<div class="space-y-2">
    @forelse($collections as $collection)
        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center justify-between">
            <div>
                <p class="font-bold text-slate-900 text-sm">#{{ $collection->order->order_number ?? '-' }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $collection->collected_at->format('d M Y H:i') }}</p>
            </div>
            <div class="text-right">
                <p class="font-bold text-slate-800 text-sm">Rp {{ number_format($collection->amount, 0, ',', '.') }}</p>
                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $collection->is_deposited ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $collection->is_deposited ? 'Sudah Disetor' : 'Belum Disetor' }}
                </span>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-sm text-slate-400">
            Belum ada riwayat setoran.
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $collections->links() }}
</div>
@endsection
