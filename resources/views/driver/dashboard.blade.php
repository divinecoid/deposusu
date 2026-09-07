@extends('layouts.ops')

@section('title', 'Dashboard Driver - DEPOSUSU')

@php
$opsRoleLabel = 'Driver';
$opsNavItems = [
    ['url' => route('driver.dashboard'), 'icon' => 'home', 'label' => 'Beranda', 'active' => request()->routeIs('driver.dashboard')],
    ['url' => route('driver.orders.index'), 'icon' => 'truck', 'label' => 'Antar', 'active' => request()->routeIs('driver.orders.*')],
    ['url' => route('driver.cash.index'), 'icon' => 'cash', 'label' => 'Setoran', 'active' => request()->routeIs('driver.cash.*')],
];
@endphp

@section('content')
<h1 class="text-xl font-extrabold text-slate-900 mb-1">Halo, {{ Auth::user()->name }} 👋</h1>
<p class="text-sm text-slate-500 mb-5">Ringkasan kerja hari ini.</p>

<div class="bg-white rounded-2xl border border-slate-200 p-4 mb-5 flex items-center justify-between">
    <div>
        <p class="text-sm font-bold text-slate-800">Status Kehadiran</p>
        <p class="text-xs text-slate-500 mt-0.5">
            @if($checkedOut) Sudah check-out hari ini.
            @elseif($checkedIn) Sudah check-in, sedang bertugas.
            @else Belum check-in hari ini. @endif
        </p>
    </div>
    @if(!$checkedIn)
        <form action="{{ route('driver.check-in') }}" method="POST">
            @csrf
            <button type="submit" class="h-10 px-4 rounded-xl bg-emerald-600 text-white font-bold text-sm hover:bg-emerald-700 transition-colors">Check-in</button>
        </form>
    @elseif(!$checkedOut)
        <form action="{{ route('driver.check-out') }}" method="POST">
            @csrf
            <button type="submit" class="h-10 px-4 rounded-xl bg-rose-500 text-white font-bold text-sm hover:bg-rose-600 transition-colors">Check-out</button>
        </form>
    @endif
</div>

<div class="grid grid-cols-3 gap-3 mb-5">
    <a href="{{ route('driver.orders.index', ['status' => 'prepared']) }}" class="bg-white rounded-2xl border border-slate-200 p-4">
        <p class="text-2xl font-extrabold text-brand-600">{{ $stats['pending_tasks'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Siap Diambil</p>
    </a>
    <a href="{{ route('driver.orders.index', ['status' => 'ondelivery']) }}" class="bg-white rounded-2xl border border-slate-200 p-4">
        <p class="text-2xl font-extrabold text-amber-500">{{ $stats['active_deliveries'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Dalam Antar</p>
    </a>
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
        <p class="text-2xl font-extrabold text-emerald-600">{{ $stats['completed_today'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Selesai Hari Ini</p>
    </div>
</div>

<a href="{{ route('driver.cash.index') }}" class="block bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-5">
    <p class="text-xs font-semibold text-amber-700 uppercase">Belum Disetor</p>
    <p class="text-xl font-extrabold text-amber-700">Rp {{ number_format($pendingCash, 0, ',', '.') }}</p>
</a>

<a href="{{ route('driver.orders.index') }}" class="block w-full text-center h-12 rounded-xl bg-brand-600 text-white font-bold leading-[3rem] hover:bg-brand-700 transition-colors">
    Lihat Pesanan
</a>
@endsection
