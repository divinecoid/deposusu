@extends('layouts.admin')

@section('header', 'Riwayat Pergerakan Stok')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Riwayat Pergerakan Stok</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.warehouse.stock') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200 text-sm font-medium">
                ← Kembali ke Stok
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="flex flex-wrap gap-4 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <div>
            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Tipe</label>
            <select name="type" class="border border-gray-300 rounded-md py-1.5 px-3 text-sm" onchange="this.form.submit()">
                <option value="">Semua</option>
                <option value="IN" {{ request('type') == 'IN' ? 'selected' : '' }}>Barang Masuk</option>
                <option value="OUT" {{ request('type') == 'OUT' ? 'selected' : '' }}>Barang Keluar</option>
                <option value="TRANSFER" {{ request('type') == 'TRANSFER' ? 'selected' : '' }}>Transfer</option>
                <option value="OPNAME" {{ request('type') == 'OPNAME' ? 'selected' : '' }}>Stock Opname</option>
                <option value="RETURN" {{ request('type') == 'RETURN' ? 'selected' : '' }}>Retur</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Gudang</label>
            <select name="warehouse_id" class="border border-gray-300 rounded-md py-1.5 px-3 text-sm" onchange="this.form.submit()">
                <option value="">Semua Gudang</option>
                @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gudang</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sebelum → Sesudah</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referensi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($movements as $mv)
                    <tr>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $mv->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-bold rounded-full {{ $mv->type_badge }}">{{ $mv->type_label }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $mv->product->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $mv->warehouse->name ?? '-' }}
                            @if($mv->to_warehouse_id)
                                <span class="text-gray-400">→</span> {{ $mv->toWarehouse->name ?? '-' }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-center font-bold {{ $mv->type == 'IN' ? 'text-green-600' : ($mv->type == 'OUT' ? 'text-red-600' : 'text-blue-600') }}">
                            {{ $mv->type == 'IN' ? '+' : ($mv->type == 'OUT' ? '-' : '±') }}{{ $mv->quantity }}
                        </td>
                        <td class="px-4 py-3 text-xs text-center text-gray-500">
                            {{ $mv->stock_before }} → {{ $mv->stock_after }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $mv->reference ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $mv->user->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400 italic">Belum ada riwayat pergerakan stok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $movements->withQueryString()->links() }}
    </div>
</div>
@endsection
