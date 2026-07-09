@extends('layouts.admin')

@section('header', 'Business Reports')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </span>
                Analisa & Laporan
            </h1>
            <p class="text-slate-500 text-sm mt-1">Laporan penjualan, finansial, produk, dan performa customer</p>
        </div>
    </div>

    {{-- Date Filter --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="border border-slate-200 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="border border-slate-200 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition shadow-sm">Terapkan Filter</button>
            <a href="{{ route('admin.reports.index') }}" class="text-slate-500 border border-slate-200 hover:bg-slate-50 text-sm font-semibold px-5 py-2 rounded-xl transition">Reset</a>
        </form>
    </div>

    {{-- ─── SECTION 1: SALES & FINANCE OVERVIEW ──────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Revenue card --}}
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-2xl p-6 shadow-lg shadow-indigo-600/20">
            <span class="text-xs font-semibold uppercase tracking-wider opacity-85">Total Omzet Penjualan</span>
            <div class="text-3xl font-extrabold mt-2">Rp {{ number_format($totalSalesAmount, 0, ',', '.') }}</div>
            <div class="text-xs mt-3 opacity-90 flex items-center gap-1.5">
                <span>📦 {{ $totalSalesCount }} orders done/delivered</span>
            </div>
        </div>

        {{-- Expenses --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pengeluaran</span>
                <div class="text-2xl font-extrabold text-slate-800 mt-2">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </div>
            <span class="text-xs text-slate-500 mt-3">Diinput dari modul Finance</span>
        </div>

        {{-- Profit estimate --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Estimasi Profit Kasar</span>
                <div class="text-2xl font-extrabold text-emerald-600 mt-2">Rp {{ number_format($estimatedProfit, 0, ',', '.') }}</div>
            </div>
            <span class="text-xs text-slate-500 mt-3">Omzet Penjualan - Pengeluaran</span>
        </div>
    </div>

    {{-- Channel & Customer Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Channel breakdown --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Penjualan per Channel</h3>
            <div class="space-y-3">
                @forelse($salesBySource as $src)
                <div class="flex items-center justify-between text-sm">
                    <span class="font-semibold text-slate-700 capitalize">
                        {{ $src->source === 'admin' ? '🏪 POS & WA (Manual)' : '📱 Customer App' }}
                    </span>
                    <div class="flex gap-4">
                        <span class="text-slate-500">{{ $src->count }} orders</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($src->total, 0, ',', '.') }}</span>
                    </div>
                </div>
                @empty
                <p class="text-slate-400 text-sm text-center py-4">Belum ada data penjualan per channel</p>
                @endforelse
            </div>
        </div>

        {{-- Top Customer --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-bold text-slate-800">Top Customer (Repeat Order / Pembelanjaan Terbesar)</h3></div>
            <div class="divide-y divide-slate-50">
                @forelse($topCustomers as $tc)
                <div class="px-6 py-3 flex justify-between items-center text-sm">
                    <div>
                        <div class="font-bold text-slate-800">{{ $tc->customer_name }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $tc->customer_phone ?: '-' }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-slate-800">Rp {{ number_format($tc->total_spend, 0, ',', '.') }}</div>
                        <div class="text-xs text-slate-500">{{ $tc->total_orders }} orders</div>
                    </div>
                </div>
                @empty
                <p class="text-slate-400 text-sm text-center py-4">Belum ada data customer belanja</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ─── SECTION 2: PRODUCTS MOVING REPORT ────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Fast Moving --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-emerald-50/50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">🚀 Fast-Moving Products (Terlaris)</h3>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($topProducts as $tp)
                <div class="px-6 py-3.5 flex justify-between items-center text-sm">
                    <div class="min-w-0 flex-1 mr-3">
                        <div class="font-bold text-slate-800 truncate">{{ $tp->name }}</div>
                        <div class="text-xs text-slate-500">Stok saat ini: {{ $tp->stock }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-emerald-600">{{ number_format($tp->qty_sold, 0) }} terjual</div>
                        <div class="text-xs text-slate-400">Subtotal: Rp {{ number_format($tp->total_rev, 0, ',', '.') }}</div>
                    </div>
                </div>
                @empty
                <p class="text-slate-400 text-sm text-center py-6">Belum ada data produk terjual</p>
                @endforelse
            </div>
        </div>

        {{-- Slow Moving --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-rose-50/50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">🐢 Slow-Moving Products (Lambat Terjual / Menumpuk)</h3>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($slowProducts as $sp)
                <div class="px-6 py-3.5 flex justify-between items-center text-sm">
                    <div class="min-w-0 flex-1 mr-3">
                        <div class="font-bold text-slate-800 truncate">{{ $sp->name }}</div>
                        <div class="text-xs text-slate-500">Stok menumpuk: <span class="font-bold text-rose-600">{{ $sp->stock }}</span></div>
                    </div>
                    <div class="text-right text-xs">
                        <div class="font-bold text-slate-650">{{ number_format($sp->qty_sold, 0) }} terjual</div>
                    </div>
                </div>
                @empty
                <p class="text-slate-400 text-sm text-center py-6">Semua produk terjual rata</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
