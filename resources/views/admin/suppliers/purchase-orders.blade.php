@extends('layouts.admin')

@section('header', 'Purchase Orders')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </span>
                Purchase Orders (PO)
            </h1>
            <p class="text-slate-500 text-sm mt-1">Daftar PO pemesanan barang ke supplier</p>
        </div>
        <a href="{{ route('admin.suppliers.po.create') }}" class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-xs transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat PO Baru
        </a>
    </div>

    {{-- Tabs status --}}
    <div class="flex flex-wrap gap-2">
        @foreach([
            ['k' => 'all', 'l' => 'Semua PO', 'n' => $counts['all']],
            ['k' => 'draft', 'l' => 'Draft', 'n' => $counts['draft']],
            ['k' => 'ordered', 'l' => 'Ordered', 'n' => $counts['ordered']],
            ['k' => 'received', 'l' => 'Received', 'n' => $counts['received']],
        ] as $tab)
        <a href="{{ route('admin.suppliers.po.index', ['status' => $tab['k']]) }}"
            class="px-4 py-1.5 rounded-full text-sm font-semibold border transition
            {{ $status === $tab['k'] ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-slate-600 border-slate-200 hover:border-orange-300 hover:text-orange-600' }}">
            {{ $tab['l'] }} <span class="ml-1 text-xs opacity-75">({{ $tab['n'] }})</span>
        </a>
        @endforeach
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    {{-- Table of PO --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">PO Number</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Supplier</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Order Date</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Expected Date</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Total</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($orders as $po)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="font-mono text-sm font-bold text-blue-700">{{ $po->po_number }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">Created by: {{ $po->createdBy->name ?? 'Admin' }}</div>
                        </td>
                        <td class="px-5 py-4 text-sm font-semibold text-slate-800">{{ $po->supplier->name ?? 'N/A' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $po->order_date->format('d M Y') }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $po->expected_date ? $po->expected_date->format('d M Y') : '-' }}</td>
                        <td class="px-5 py-4 text-sm text-right font-bold text-slate-800">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $po->status_color }}-100 text-{{ $po->status_color }}-700 capitalize">
                                {{ $po->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="{{ route('admin.suppliers.po.show', $po->id) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-16 text-center text-slate-400 text-sm">Belum ada Purchase Order</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $orders->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection
