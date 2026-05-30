@extends('layouts.admin')

@section('header', 'Barang Masuk')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Form Barang Masuk</h2>

    <form action="{{ route('admin.warehouse.receive.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Gudang Tujuan</label>
                <select name="warehouse_id" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm focus:border-blue-500 focus:outline-none">
                    <option value="">-- Pilih Gudang --</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Referensi / No. PO</label>
                <input type="text" name="reference" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm focus:border-blue-500 focus:outline-none" placeholder="Contoh: PO-2026-001">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
            <textarea name="notes" rows="2" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm focus:border-blue-500 focus:outline-none" placeholder="Catatan opsional..."></textarea>
        </div>

        <!-- Item List -->
        <div class="mt-8 border-t border-gray-200 pt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Daftar Barang</h3>
                <button type="button" onclick="addItem()" class="px-3 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200 text-sm font-medium">
                    + Tambah Item
                </button>
            </div>
            <div id="items-container" class="space-y-4">
                <!-- Items will be appended here -->
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.warehouse.stock') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                Simpan Barang Masuk
            </button>
        </div>
    </form>
</div>

<script>
    const products = @json($products);
    let itemCount = 0;

    function addItem() {
        const container = document.getElementById('items-container');
        let options = '<option value="">-- Pilih Produk --</option>';
        products.forEach(p => {
            options += `<option value="${p.id}">${p.name} (${p.sku || '-'})</option>`;
        });

        const html = `
            <div class="flex gap-4 items-end bg-gray-50 p-4 rounded-md border border-gray-200" id="item-${itemCount}">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700">Produk</label>
                    <select name="items[${itemCount}][product_id]" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm">
                        ${options}
                    </select>
                </div>
                <div class="w-28">
                    <label class="block text-xs font-medium text-gray-700">Qty</label>
                    <input type="number" name="items[${itemCount}][quantity]" required min="1" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm" placeholder="Qty">
                </div>
                <div class="w-28">
                    <label class="block text-xs font-medium text-gray-700">Lokasi Rak</label>
                    <input type="text" name="items[${itemCount}][rack_location]" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm" placeholder="A-01">
                </div>
                <div>
                    <button type="button" onclick="document.getElementById('item-${itemCount}').remove()" class="px-3 py-2 bg-red-50 text-red-600 rounded hover:bg-red-100 text-sm">Hapus</button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        itemCount++;
    }

    // Start with one item row
    addItem();
</script>
@endsection
