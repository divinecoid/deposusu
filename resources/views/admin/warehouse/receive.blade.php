@extends('layouts.admin')

@section('header', 'Barang Masuk')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Form Barang Masuk</h2>

    <form id="receive-form" action="{{ route('admin.warehouse.receive.store') }}" method="POST">
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
        const i = itemCount;

        const html = `
            <div class="flex gap-4 items-end bg-gray-50 p-4 rounded-md border border-gray-200" id="item-${i}">
                <div class="flex-1 relative">
                    <label class="block text-xs font-medium text-gray-700">Produk</label>
                    <input type="text" id="product-search-${i}" autocomplete="off" required placeholder="Cari nama produk atau SKU..."
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm focus:border-blue-500 focus:outline-none"
                        oninput="filterProducts(${i})" onfocus="filterProducts(${i})">
                    <input type="hidden" name="items[${i}][product_id]" id="product-id-${i}">
                    <div id="product-suggestions-${i}" class="hidden absolute z-20 mt-1 w-full max-h-56 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg"></div>
                </div>
                <div class="w-24">
                    <label class="block text-xs font-medium text-gray-700">Qty</label>
                    <input type="number" name="items[${i}][quantity]" required min="1" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm" placeholder="Qty">
                </div>
                <div class="w-40">
                    <label class="block text-xs font-medium text-gray-700">Tgl. Kedaluwarsa</label>
                    <input type="date" name="items[${i}][expiry_date]" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm">
                </div>
                <div class="w-28">
                    <label class="block text-xs font-medium text-gray-700">Lokasi Rak</label>
                    <input type="text" name="items[${i}][rack_location]" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm" placeholder="A-01">
                </div>
                <div>
                    <button type="button" onclick="document.getElementById('item-${i}').remove()" class="px-3 py-2 bg-red-50 text-red-600 rounded hover:bg-red-100 text-sm">Hapus</button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        itemCount++;
    }

    function filterProducts(i) {
        const query = document.getElementById(`product-search-${i}`).value.trim().toLowerCase();
        const box = document.getElementById(`product-suggestions-${i}`);

        const matches = query
            ? products.filter(p =>
                p.name.toLowerCase().includes(query) ||
                (p.sku && p.sku.toLowerCase().includes(query)) ||
                (p.barcode && p.barcode.toLowerCase().includes(query))
              ).slice(0, 20)
            : products.slice(0, 20);

        if (matches.length === 0) {
            box.innerHTML = '<div class="px-3 py-2 text-sm text-gray-400">Produk tidak ditemukan.</div>';
        } else {
            box.innerHTML = matches.map(p => `
                <button type="button" class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-50 last:border-0"
                    onclick="selectProduct(${i}, ${p.id}, '${p.name.replace(/'/g, "\\'")}', '${(p.sku || '-').replace(/'/g, "\\'")}')">
                    <span class="font-medium text-gray-800">${p.name}</span>
                    <span class="text-gray-400"> (${p.sku || '-'})</span>
                </button>
            `).join('');
        }
        box.classList.remove('hidden');
    }

    function selectProduct(i, id, name, sku) {
        document.getElementById(`product-search-${i}`).value = `${name} (${sku})`;
        document.getElementById(`product-id-${i}`).value = id;
        document.getElementById(`product-suggestions-${i}`).classList.add('hidden');
    }

    document.addEventListener('click', (e) => {
        document.querySelectorAll('[id^="product-suggestions-"]').forEach(box => {
            const i = box.id.replace('product-suggestions-', '');
            const input = document.getElementById(`product-search-${i}`);
            if (!box.contains(e.target) && e.target !== input) {
                box.classList.add('hidden');
            }
        });
    });

    document.getElementById('receive-form').addEventListener('submit', (e) => {
        const emptyRow = Array.from(document.querySelectorAll('[id^="product-id-"]')).find(el => !el.value);
        if (emptyRow) {
            e.preventDefault();
            alert('Pilih produk dari daftar saran untuk setiap baris (jangan hanya mengetik).');
            emptyRow.previousElementSibling?.focus();
        }
    });

    // Start with one item row
    addItem();
</script>
@endsection
