@extends('layouts.admin')

@section('header', 'Business Reports & Analytics')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Title and Top Buttons --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </span>
                Analisa & Laporan
            </h1>
            <p class="text-slate-500 text-sm mt-1">Laporan finansial, penjualan, stock per warehouse, customer history, dan performa operasional</p>
        </div>
        <div class="flex items-center gap-2 print:hidden">
            <a href="{{ route('admin.reports.export', ['tab' => $tab, 'start_date' => $startDate, 'end_date' => $endDate, 'warehouse_id' => $selectedWarehouseId]) }}" 
               class="bg-indigo-650 hover:bg-indigo-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export PDF
            </a>
            <button onclick="window.print()" 
                    class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="flex border-b border-slate-200 bg-white p-1 rounded-2xl shadow-sm gap-1 print:hidden">
        @foreach([
            'finance' => '📈 Financial Report',
            'sales' => '🛍️ Sales Report',
            'stock' => '📦 Stock Per Warehouse',
            'customer' => '👥 Customer Report',
            'operational' => '⚙️ Operational Report'
        ] as $key => $title)
        <a href="{{ route('admin.reports.index', ['tab' => $key, 'start_date' => $startDate, 'end_date' => $endDate, 'warehouse_id' => $selectedWarehouseId]) }}"
           class="flex-1 text-center py-2.5 rounded-xl text-sm font-bold transition {{ $tab === $key ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">
            {{ $title }}
        </a>
        @endforeach
    </div>

    {{-- Date & Location Filter --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 print:hidden">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap gap-4 items-end">
            <input type="hidden" name="tab" value="{{ $tab }}">
            
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="border border-slate-200 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="border border-slate-200 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Warehouse / Gudang</label>
                <select name="warehouse_id" class="border border-slate-200 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                    <option value="">Semua Lokasi / Gudang</option>
                    @foreach($warehouses as $w)
                    <option value="{{ $w->id }}" {{ $selectedWarehouseId == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition shadow-sm">Terapkan Filter</button>
            <a href="{{ route('admin.reports.index', ['tab' => $tab]) }}" class="text-slate-500 border border-slate-200 hover:bg-slate-50 text-sm font-semibold px-5 py-2.5 rounded-xl transition">Reset</a>
        </form>
    </div>

    {{-- ─── 1. FINANCIAL REPORT TAB ─── --}}
    @if($tab === 'finance')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl p-6 shadow-md">
            <span class="text-xs font-semibold uppercase tracking-wider opacity-85">Total Pendapatan (Omzet)</span>
            <div class="text-3xl font-extrabold mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="text-xs mt-3 opacity-90">Total omzet dari App, POS, dan WA Manual</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pengeluaran</span>
                <div class="text-2xl font-extrabold text-slate-800 mt-2">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </div>
            <span class="text-xs text-slate-500 mt-3">Operasional, Gaji, Transport, Marketing, dll.</span>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Estimasi Profit Bersih</span>
                <div class="text-2xl font-extrabold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-2">Rp {{ number_format($netProfit, 0, ',', '.') }}</div>
            </div>
            <span class="text-xs text-slate-500 mt-3">Omzet - HPP (Modal) - Pengeluaran</span>
        </div>
    </div>

    {{-- Detailed P&L and AR/AP --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Profit & Loss Statement --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Ringkasan Laba Rugi</h3>
            <div class="space-y-3.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-650">Total Pendapatan</span>
                    <span class="font-bold text-slate-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-650">Harga Pokok Penjualan (HPP / Modal Barang)</span>
                    <span class="font-semibold text-rose-600">- Rp {{ number_format($totalModal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-100 pt-2 font-bold">
                    <span class="text-slate-800">Gross Profit (Profit Kotor)</span>
                    <span class="text-emerald-600">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-650">Total Biaya Operasional</span>
                    <span class="font-semibold text-rose-650">- Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-2.5 font-extrabold text-base">
                    <span class="text-slate-800">Net Profit</span>
                    <span class="{{ $netProfit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Rp {{ number_format($netProfit, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Accounts Receivable / Piutang Customer --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold text-slate-800">Piutang Customer (Unpaid Corporate)</h3>
                <span class="text-xs font-bold text-rose-600">Total: Rp {{ number_format($totalPiutang, 0, ',', '.') }}</span>
            </div>
            <div class="divide-y divide-slate-55 overflow-y-auto max-h-60 flex-1">
                @forelse($piutangList as $order)
                <div class="px-6 py-3 flex justify-between items-center text-sm">
                    <div>
                        <div class="font-bold text-slate-800">{{ $order->order_number }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $order->customer_name }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-slate-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded-full uppercase">UNPAID</span>
                    </div>
                </div>
                @empty
                <p class="text-slate-400 text-sm text-center py-8">Tidak ada piutang customer aktif</p>
                @endforelse
            </div>
        </div>

        {{-- Accounts Payable / Hutang Supplier --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold text-slate-800">Hutang ke Supplier</h3>
                <span class="text-xs font-bold text-rose-600">Total Sisa Hutang: Rp {{ number_format($totalHutang, 0, ',', '.') }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">PO Number</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Supplier</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Total Tagihan</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Telah Dibayar</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Sisa Hutang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @forelse($hutangList as $po)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-3.5 font-bold text-slate-800">{{ $po->po_number }}</td>
                            <td class="px-6 py-3.5 text-slate-700">{{ $po->supplier->name }}</td>
                            <td class="px-6 py-3.5 text-right text-slate-800">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-3.5 text-right text-emerald-600">Rp {{ number_format($po->paid_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-3.5 text-right font-bold text-rose-600">Rp {{ number_format($po->total_amount - $po->paid_amount, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">Tidak ada hutang supplier aktif</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ─── 2. SALES REPORT TAB ─── --}}
    @if($tab === 'sales')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Penjualan per Channel</h3>
            <div class="space-y-4">
                @forelse($salesByChannel as $src)
                <div class="flex items-center justify-between text-sm">
                    <span class="font-semibold text-slate-700 capitalize">
                        {{ $src->source === 'admin' ? 'WhatsApp Order & POS (Manual/Toko)' : ($src->source === 'kasir' ? 'POS Toko Fisik' : 'Customer App') }}
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

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 overflow-hidden">
            <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Top Products (Fast-Moving)</h3>
            <div class="divide-y divide-slate-50">
                @forelse($topProducts as $tp)
                <div class="py-3 flex justify-between items-center text-sm">
                    <div>
                        <div class="font-bold text-slate-850">{{ $tp->name }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ number_format($tp->qty_sold, 0) }} pcs terjual</div>
                    </div>
                    <div class="font-bold text-slate-800">Rp {{ number_format($tp->total_rev, 0, ',', '.') }}</div>
                </div>
                @empty
                <p class="text-slate-400 text-sm text-center py-4">Belum ada data produk terjual</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    {{-- ─── 3. STOCK REPORT TAB ─── --}}
    @if($tab === 'stock')
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800">Stok Aktual per Warehouse</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Gudang / Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Rak Lokasi</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Stok Aktual</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Batas Minimum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($stocks as $stock)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-3.5 font-bold text-slate-800">{{ $stock->product->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-3.5 text-slate-650">{{ $stock->warehouse->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-3.5 text-slate-500">{{ $stock->rack_location ?: '-' }}</td>
                        <td class="px-6 py-3.5 text-right font-bold {{ $stock->quantity <= $stock->min_stock ? 'text-rose-600' : 'text-slate-800' }}">{{ $stock->quantity }} pcs</td>
                        <td class="px-6 py-3.5 text-right text-slate-500">{{ $stock->min_stock }} pcs</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">Tidak ada data stok per gudang</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ─── 4. CUSTOMER REPORT TAB ─── --}}
    @if($tab === 'customer')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Customer Terdaftar</span>
            <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $totalCustomersCount }} orang</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Customer Baru (Periode Ini)</span>
            <div class="text-3xl font-extrabold text-indigo-600 mt-2">{{ $newCustomersCount }} orang</div>
        </div>
        <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-bold text-slate-800">Top Customer (Repeat Order / Pembelanjaan Terbesar)</h3></div>
            <div class="divide-y divide-slate-50">
                @forelse($topCustomers as $tc)
                <div class="px-6 py-4 flex justify-between items-center text-sm">
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
                <p class="text-slate-400 text-sm text-center py-6">Belum ada data customer belanja</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    {{-- ─── 5. OPERATIONAL REPORT TAB ─── --}}
    @if($tab === 'operational')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Order Masuk</span>
            <div class="text-2xl font-extrabold text-slate-800 mt-2">{{ $totalOrders }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Selesai Dikirim</span>
            <div class="text-2xl font-extrabold text-emerald-600 mt-2">{{ $completedOrders }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Sedang Diproses/Kirim</span>
            <div class="text-2xl font-extrabold text-amber-600 mt-2">{{ $processingOrders }}</div>
        </div>

        {{-- Driver courier performance --}}
        <div class="md:col-span-3 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50"><h3 class="font-bold text-slate-800">Performa Pengiriman Kurir</h3></div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Nama Driver / Kurir</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Total Pengantaran</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Pengantaran Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Persentase Sukses</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($deliveries as $d)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-3.5 font-bold text-slate-800">{{ $d->driver->name ?? 'Unknown Driver' }}</td>
                            <td class="px-6 py-3.5 text-center text-slate-650">{{ $d->total_deliveries }}</td>
                            <td class="px-6 py-3.5 text-center text-emerald-600 font-semibold">{{ $d->completed_deliveries }}</td>
                            <td class="px-6 py-3.5 text-center font-bold text-indigo-600">
                                {{ $d->total_deliveries > 0 ? round(($d->completed_deliveries / $d->total_deliveries) * 100) : 0 }}%
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">Tidak ada data performa pengiriman kurir</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
