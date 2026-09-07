@extends('layouts.admin')

@section('header', 'Stok Per Gudang')

@section('content')
<div class="bg-white rounded-lg shadow p-6" x-data="{ openBatch: null }">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Stok Per Gudang</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.warehouse.receive') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm font-medium">
                + Barang Masuk
            </a>
            <a href="{{ route('admin.warehouse.transfer') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium">
                ↔ Transfer Gudang
            </a>
            <a href="{{ route('admin.warehouse.movements') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm font-medium">
                📋 Riwayat
            </a>
        </div>
    </div>

    <!-- Warehouse Tabs -->
    <div class="flex gap-2 mb-6 border-b border-gray-200 overflow-x-auto">
        @foreach($warehouses as $wh)
            <a href="{{ route('admin.warehouse.stock', ['warehouse_id' => $wh->id]) }}"
                class="px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 -mb-px transition
                    {{ $selectedWarehouse && $selectedWarehouse->id == $wh->id
                        ? 'border-blue-600 text-blue-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                {{ $wh->name }}
            </a>
        @endforeach
    </div>

    @if($selectedWarehouse)
        <p class="text-sm text-gray-500 mb-4">📍 {{ $selectedWarehouse->address ?? 'Alamat belum diisi' }}</p>

        @if($stocks->isEmpty())
            <div class="text-center py-12 text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <p>Gudang ini belum memiliki stok.</p>
                <a href="{{ route('admin.warehouse.receive') }}" class="mt-2 inline-block text-blue-600 hover:underline text-sm">+ Tambah Barang Masuk</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-8"></th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok Gudang</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Min. Stok</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi Rak</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($stocks as $stock)
                            @php $batches = $batchesByProduct->get($stock->product_id, collect()); @endphp
                            <tr @click="openBatch = openBatch === {{ $stock->id }} ? null : {{ $stock->id }}" class="cursor-pointer hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-400">
                                    @if($batches->isNotEmpty())
                                        <svg class="w-4 h-4 transition-transform" :class="openBatch === {{ $stock->id }} ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $stock->product->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $stock->product->sku ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-center font-bold">{{ $stock->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-500">{{ $stock->min_stock }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $stock->rack_location ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($stock->quantity <= 0)
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800">Habis</span>
                                    @elseif($stock->quantity <= $stock->min_stock)
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800">Menipis</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800">Aman</span>
                                    @endif
                                </td>
                            </tr>
                            @if($batches->isNotEmpty())
                                <tr x-show="openBatch === {{ $stock->id }}" x-cloak style="display: none;">
                                    <td></td>
                                    <td colspan="6" class="px-4 py-3 bg-slate-50">
                                        <p class="text-xs font-bold text-slate-500 uppercase mb-2">Rincian per Tanggal Kedaluwarsa</p>
                                        <table class="w-full text-xs">
                                            @foreach($batches as $batch)
                                                @php
                                                    $status = $batch->expiryStatus();
                                                    $cls = ['expired' => 'text-rose-600', 'near_expiry' => 'text-amber-600', 'safe' => 'text-slate-600'][$status];
                                                @endphp
                                                <tr class="border-b border-slate-100 last:border-0">
                                                    <td class="py-1.5 {{ $cls }} font-semibold">{{ $batch->expiry_date->format('d M Y') }}</td>
                                                    <td class="py-1.5 text-slate-500">{{ $batch->rack_location ?? '-' }}</td>
                                                    <td class="py-1.5 text-right font-bold text-slate-700">{{ $batch->quantity }} pcs</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @else
        <p class="text-center text-gray-500 py-8">Belum ada gudang terdaftar.</p>
    @endif
</div>
@endsection
