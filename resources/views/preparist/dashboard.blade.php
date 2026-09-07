@extends('layouts.ops')

@section('title', 'Dashboard Preparist - DEPOSUSU')

@php
$opsRoleLabel = 'Preparist';
$opsNavItems = [
    ['url' => route('preparist.dashboard'), 'icon' => '🏠', 'label' => 'Beranda', 'active' => request()->routeIs('preparist.dashboard')],
    ['url' => route('preparist.orders.index'), 'icon' => '📦', 'label' => 'Pesanan', 'active' => request()->routeIs('preparist.orders.*')],
];
@endphp

@section('content')
<h1 class="text-xl font-extrabold text-slate-900 mb-1">Halo, {{ Auth::user()->name }} 👋</h1>
<p class="text-sm text-slate-500 mb-5">Ringkasan kerja hari ini.</p>

<div class="grid grid-cols-2 gap-3 mb-5">
    <a href="{{ route('preparist.orders.index', ['status' => 'onprocess']) }}" class="bg-white rounded-2xl border border-slate-200 p-4">
        <p class="text-2xl font-extrabold text-brand-600">{{ $stats['new_orders'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Pesanan Baru</p>
    </a>
    <a href="{{ route('preparist.orders.index', ['status' => 'onpreparation']) }}" class="bg-white rounded-2xl border border-slate-200 p-4">
        <p class="text-2xl font-extrabold text-amber-500">{{ $stats['processing'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Sedang Disiapkan</p>
    </a>
    <a href="{{ route('preparist.orders.index', ['status' => 'prepared']) }}" class="bg-white rounded-2xl border border-slate-200 p-4">
        <p class="text-2xl font-extrabold text-emerald-600">{{ $stats['waiting_driver'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Menunggu Kurir</p>
    </a>
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
        <p class="text-2xl font-extrabold text-slate-700">{{ $stats['completed_today'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Selesai Hari Ini</p>
    </div>
</div>

<a href="{{ route('preparist.orders.index') }}" class="block w-full text-center h-12 rounded-xl bg-brand-600 text-white font-bold leading-[3rem] hover:bg-brand-700 transition-colors">
    Lihat Antrian Pesanan
</a>
@endsection
