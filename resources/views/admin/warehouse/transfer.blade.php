@extends('layouts.admin')

@section('header', 'Transfer Antar Gudang')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Form Transfer Antar Gudang</h2>

    <form action="{{ route('admin.warehouse.transfer.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Dari Gudang</label>
                <select name="from_warehouse_id" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm focus:border-blue-500 focus:outline-none">
                    <option value="">-- Pilih Gudang Asal --</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Ke Gudang</label>
                <select name="to_warehouse_id" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm focus:border-blue-500 focus:outline-none">
                    <option value="">-- Pilih Gudang Tujuan --</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
            <textarea name="notes" rows="2" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm focus:border-blue-500 focus:outline-none" placeholder="Catatan opsional..."></textarea>
        </div>

        <!-- Item List -->
        <div class="mt-8 border-t border-gray-200 pt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Daftar Barang Transfer</h3>
                <button type="button" onclick="addTransferItem()" class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-sm font-medium">
                    + Tambah Item
                </button>
            </div>
            <div id="transfer-items-container" class="space-y-4">
                <!-- Items will be appended here -->
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.warehouse.stock') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                Proses Transfer
            </button>
        </div>
    </form>
</div>

<script>
    const products = @json($products);
    let transferItemCount = 0;

    function addTransferItem() {
        const container = document.getElementById('transfer-items-container');
        let options = '<option value="">-- Pilih Produk --</option>';
        products.forEach(p => {
            options += `<option value="${p.id}">${p.name} (Stok: ${p.stock})</option>`;
        });

        const html = `
            <div class="flex gap-4 items-end bg-gray-50 p-4 rounded-md border border-gray-200" id="transfer-item-${transferItemCount}">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700">Produk</label>
                    <select name="items[${transferItemCount}][product_id]" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm">
                        ${options}
                    </select>
                </div>
                <div class="w-28">
                    <label class="block text-xs font-medium text-gray-700">Qty Transfer</label>
                    <input type="number" name="items[${transferItemCount}][quantity]" required min="1" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm" placeholder="Qty">
                </div>
                <div>
                    <button type="button" onclick="document.getElementById('transfer-item-${transferItemCount}').remove()" class="px-3 py-2 bg-red-50 text-red-600 rounded hover:bg-red-100 text-sm">Hapus</button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        transferItemCount++;
    }

    // Start with one item row
    addTransferItem();
</script>
@endsection
