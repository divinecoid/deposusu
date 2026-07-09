@extends('layouts.admin')

@section('header', 'Dashboard Overview')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Period Filter --}}
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-extrabold text-slate-800">Selamat datang di Deposusu 👋</h1>
        <div class="flex bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            @foreach(['today' => 'Hari Ini', 'week' => 'Minggu Ini', 'month' => 'Bulan Ini'] as $key => $label)
            <a href="{{ route('admin.dashboard', ['period' => $key]) }}"
                class="px-4 py-2 text-sm font-semibold transition {{ $period === $key ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- ─── KPI CARDS ROW 1 ──────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Revenue --}}
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 text-white rounded-2xl p-5 shadow-lg shadow-blue-600/20">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wide opacity-80">Total Revenue</span>
                <span class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="flex gap-3 mt-2 text-xs opacity-80">
                <span>🌐 Online: Rp {{ number_format($revenueOnline, 0, ',', '.') }}</span>
                <span>🏪 Toko: Rp {{ number_format($revenueOffline, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Orders --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Order</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold text-slate-800">{{ $totalOrders }}</div>
            @if($pendingOrders > 0)
            <div class="mt-2 flex items-center gap-1.5 text-xs text-amber-600 font-medium">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                {{ $pendingOrders }} belum diproses
            </div>
            @endif
        </div>

        {{-- Pending Payments --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Menunggu Bayar</span>
                <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold text-slate-800">{{ $pendingPayments }}</div>
            <div class="mt-2 text-xs text-slate-500">
                {{ $unpaidInvoices }} invoice unpaid · Rp {{ number_format($pendingPaymentAmount, 0, ',', '.') }}
            </div>
        </div>

        {{-- Stock Alert --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Produk & Stok</span>
                <span class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold text-slate-800">{{ $totalProducts }}</div>
            <div class="mt-2 flex gap-3 text-xs">
                @if($lowStockCount > 0)<span class="text-amber-600 font-medium">⚠️ {{ $lowStockCount }} stok tipis</span>@endif
                @if($outOfStock > 0)<span class="text-red-600 font-medium">🔴 {{ $outOfStock }} habis</span>@endif
                @if($lowStockCount == 0 && $outOfStock == 0)<span class="text-emerald-600 font-medium">✅ Stok aman</span>@endif
            </div>
        </div>
    </div>

    {{-- ─── KPI CARDS ROW 2 ──────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-4">
        <a href="{{ route('admin.master.customers.index') }}" class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition flex items-center gap-4">
            <span class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
            <div>
                <div class="text-xl font-extrabold text-slate-800">{{ $totalCustomers }}</div>
                <div class="text-xs text-slate-500 font-medium">Total Customer</div>
            </div>
        </a>
        <a href="{{ route('admin.suppliers.index') }}" class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition flex items-center gap-4">
            <span class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></span>
            <div>
                <div class="text-xl font-extrabold text-slate-800">{{ $totalSuppliers }}</div>
                <div class="text-xs text-slate-500 font-medium">Total Supplier</div>
            </div>
        </a>
        <a href="{{ route('admin.invoices.index') }}" class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition flex items-center gap-4">
            <span class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></span>
            <div>
                <div class="text-xl font-extrabold text-slate-800">{{ $unpaidInvoices }}</div>
                <div class="text-xs text-slate-500 font-medium">Invoice Unpaid</div>
            </div>
        </a>
    </div>

    {{-- ─── REVENUE CHART + LOW STOCK ────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Revenue Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <h3 class="font-bold text-slate-800 mb-4">Revenue 7 Hari Terakhir</h3>
            <div class="h-48 flex items-end gap-2">
                @php $maxRev = $chartDays->max('revenue') ?: 1; @endphp
                @foreach($chartDays as $day)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-xs font-semibold text-slate-600">Rp {{ number_format($day['revenue'] / 1000, 0) }}k</span>
                    <div class="w-full bg-blue-500 rounded-t-lg transition-all" style="height: {{ max(4, ($day['revenue'] / $maxRev) * 100) }}%"></div>
                    <span class="text-xs text-slate-400">{{ $day['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Low Stock --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Stok Menipis
            </h3>
            <div class="space-y-3">
                @forelse($lowStockProducts as $prod)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-700 font-medium truncate flex-1 mr-3">{{ $prod->name }}</span>
                    <span class="text-sm font-bold {{ $prod->stock <= 3 ? 'text-red-600' : 'text-amber-600' }}">{{ $prod->stock }}</span>
                </div>
                @empty
                <p class="text-sm text-slate-400">Semua stok aman ✅</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ─── TOP PRODUCTS + RECENT ORDERS ─────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Top Products --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">🏆 Produk Terlaris</h3>
            </div>
            <div class="divide-y divide-slate-50">
                @foreach($topProducts as $i => $tp)
                <div class="px-6 py-3 flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full {{ $i === 0 ? 'bg-amber-400 text-white' : 'bg-slate-100 text-slate-500' }} text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-slate-800 truncate">{{ $tp->name }}</div>
                        <div class="text-xs text-slate-400">{{ number_format($tp->total_qty, 0) }} terjual</div>
                    </div>
                    <span class="text-sm font-bold text-slate-700">Rp {{ number_format($tp->total_revenue, 0, ',', '.') }}</span>
                </div>
                @endforeach
                @if($topProducts->isEmpty())
                <div class="px-6 py-6 text-center text-sm text-slate-400">Belum ada data penjualan</div>
                @endif
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">Order Terbaru</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-blue-600 font-semibold hover:text-blue-800 transition">Lihat Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Order</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Customer</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Total</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-3 text-sm">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="font-mono font-bold text-blue-600 hover:text-blue-800">{{ $order->order_number }}</a>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $order->source === 'admin' ? '📋 Manual' : '📱 App' }}</div>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-700">{{ $order->customer_name }}</td>
                            <td class="px-5 py-3">
                                @php
                                    $sc = ['pending'=>'bg-amber-100 text-amber-700','onprocess'=>'bg-blue-100 text-blue-700','done'=>'bg-emerald-100 text-emerald-700','cancelled'=>'bg-slate-100 text-slate-500'];
                                    $sv = $order->status instanceof \App\Enums\OrderStatusEnum ? $order->status->value : $order->status;
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $sc[strtolower($sv)] ?? 'bg-violet-100 text-violet-700' }}">{{ ucfirst($sv) }}</span>
                            </td>
                            <td class="px-5 py-3 text-sm text-right font-bold text-slate-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-xs text-right text-slate-400">{{ $order->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada order</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection