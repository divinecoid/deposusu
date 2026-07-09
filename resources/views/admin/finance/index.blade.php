@extends('layouts.admin')

@section('header', 'Finance & Accounting Dashboard')

@section('content')
<div x-data="{ openExpenseModal: false }">
    <!-- Notifications -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded text-emerald-800 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Accounting stats grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Omzet -->
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-md p-6 text-white transform hover:scale-[1.02] transition-transform duration-300">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-blue-100 uppercase tracking-wider">Total Omzet</span>
                <div class="p-2 bg-blue-400/30 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</p>
            <p class="text-xs text-blue-200 mt-2">Dari Apps & POS Kasir</p>
        </div>

        <!-- Modal Barang -->
        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-md p-6 text-white transform hover:scale-[1.02] transition-transform duration-300">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-amber-100 uppercase tracking-wider">Modal Barang (COGS)</span>
                <div class="p-2 bg-amber-400/30 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold">Rp {{ number_format($totalModal, 0, ',', '.') }}</p>
            <p class="text-xs text-amber-200 mt-2">Biaya Pokok Inventori</p>
        </div>

        <!-- Gross Profit -->
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-md p-6 text-white transform hover:scale-[1.02] transition-transform duration-300">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-emerald-100 uppercase tracking-wider">Gross Profit</span>
                <div class="p-2 bg-emerald-400/30 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold">Rp {{ number_format($grossProfit, 0, ',', '.') }}</p>
            <p class="text-xs text-emerald-200 mt-2">Omzet - Modal Barang</p>
        </div>

        <!-- Net Profit -->
        <div class="bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl shadow-md p-6 text-white transform hover:scale-[1.02] transition-transform duration-300">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-rose-100 uppercase tracking-wider">Net Profit</span>
                <div class="p-2 bg-rose-400/30 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
            <p class="text-xs text-rose-200 mt-2">Gross - Operasional - Refund</p>
        </div>

        <!-- Cash Balance -->
        <div class="bg-gradient-to-br from-slate-700 to-slate-900 rounded-2xl shadow-md p-6 text-white transform hover:scale-[1.02] transition-transform duration-300">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-slate-300 uppercase tracking-wider">Cash Balance</span>
                <div class="p-2 bg-slate-600/30 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold">Rp {{ number_format($cashBalance, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-2">Arus Kas Aktual</p>
        </div>
    </div>

    <!-- Filters & Transactions Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Riwayat Transaksi Keuangan</h3>
                <p class="text-sm text-slate-500">Pantau semua arus kas masuk, pengeluaran operasional, dan refund.</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="openExpenseModal = true" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-xl shadow transition duration-150 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Catat Biaya Operasional
                </button>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.finance.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-slate-50 rounded-xl border border-slate-100">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tipe Transaksi</label>
                <select name="type" class="w-full rounded-lg border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="all" {{ $typeFilter === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                    <option value="income" {{ $typeFilter === 'income' ? 'selected' : '' }}>Pemasukan (Income)</option>
                    <option value="expense" {{ $typeFilter === 'expense' ? 'selected' : '' }}>Pengeluaran (Expense)</option>
                    <option value="refund" {{ $typeFilter === 'refund' ? 'selected' : '' }}>Refund</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm shadow transition duration-150">
                    Filter
                </button>
                <a href="{{ route('admin.finance.index') }}" class="py-2 px-4 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium rounded-lg text-sm transition duration-150 text-center">
                    Reset
                </a>
            </div>
        </form>

        <!-- Transaction Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider rounded-l-lg">Tanggal</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">ID Ref</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wider rounded-r-lg">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $trx->transaction_date->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-700">
                                {{ $trx->reference_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($trx->type === 'income') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($trx->type === 'expense') bg-rose-50 text-rose-700 border border-rose-200
                                    @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                                    {{ ucfirst($trx->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 uppercase font-mono text-xs">
                                {{ str_replace('_', ' ', $trx->category) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $trx->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right 
                                @if($trx->type === 'income') text-emerald-600 @else text-rose-600 @endif">
                                {{ $trx->type === 'income' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                                Tidak ada data transaksi yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Add Expense Modal (Alpine.js) -->
    <div x-show="openExpenseModal" 
         class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="openExpenseModal = false"></div>

        <!-- Modal Content -->
        <div class="relative w-full max-w-lg mx-auto bg-white rounded-2xl shadow-xl z-10 border border-slate-100 transform overflow-hidden transition-all p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-800">Catat Pengeluaran Operasional</h3>
                <button @click="openExpenseModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.finance.store-expense') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kategori Biaya</label>
                    <select name="category" class="w-full rounded-lg border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="operational_rent">Sewa Tempat/Ruko</option>
                        <option value="operational_utilities">Listrik, Air & Internet</option>
                        <option value="operational_salary">Gaji & Upah Karyawan</option>
                        <option value="operational_marketing">Iklan & Pemasaran</option>
                        <option value="operational_others">Operasional Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Jumlah Pengeluaran (Rp)</label>
                    <input type="number" name="amount" min="0" placeholder="Contoh: 150000" class="w-full rounded-lg border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal Transaksi</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Keterangan / Detail Pengeluaran</label>
                    <textarea name="description" rows="3" placeholder="Tulis rincian pembelian atau biaya disini..." class="w-full rounded-lg border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="openExpenseModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-sm font-medium shadow transition">
                        Simpan Biaya
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
