@extends('layouts.ops')

@section('title', 'Antrian Pesanan - DEPOSUSU')

@php
$opsRoleLabel = 'Preparist';
$opsNavItems = [
    ['url' => route('preparist.dashboard'), 'icon' => 'home', 'label' => 'Beranda', 'active' => request()->routeIs('preparist.dashboard')],
    ['url' => route('preparist.orders.index'), 'icon' => 'archive', 'label' => 'Pesanan', 'active' => request()->routeIs('preparist.orders.*')],
];
@endphp

@section('content')
<h1 class="text-xl font-extrabold text-slate-900 mb-4">Antrian Pesanan</h1>

<div class="flex gap-2 mb-4 overflow-x-auto pb-1">
    @foreach(['onprocess' => 'Baru', 'onpreparation' => 'Disiapkan', 'prepared' => 'Menunggu Kurir', 'history' => 'Riwayat'] as $key => $label)
        <a href="{{ route('preparist.orders.index', ['status' => $key]) }}"
            class="shrink-0 px-4 py-2 rounded-xl text-sm font-semibold {{ $status === $key ? 'bg-brand-600 text-white' : 'bg-white text-slate-600 border border-slate-200' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

<div class="space-y-3">
    @forelse($orders as $order)
        <a href="{{ route('preparist.orders.show', $order->id) }}" class="block bg-white rounded-2xl border border-slate-200 p-4">
            <div class="flex items-center justify-between mb-1.5">
                <p class="font-bold text-slate-900 text-sm">#{{ $order->order_number }}</p>
                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ $order->status->label() }}</span>
            </div>
            <p class="text-xs text-slate-500">{{ $order->customer_name }} &middot; {{ $order->items->count() }} item</p>
            <p class="text-[11px] text-slate-400 mt-1">{{ $order->created_at->format('d M Y H:i') }}</p>
        </a>
    @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-sm text-slate-400">
            Tidak ada pesanan di kategori ini.
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection
