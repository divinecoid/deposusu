@extends('layouts.admin')

@section('header', 'Buat Purchase Order')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="poForm()" x-init="init()">
    <a href="{{ route('admin.suppliers.po.index') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali ke PO
    </a>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.suppliers.po.store') }}" method="POST" @submit="handleSubmit($event)">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left column (Main details + PO items) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Details --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Informasi Purchase Order</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Supplier <span class="text-red-500">*</span></label>
                            <select name="supplier_id" required class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50 focus:ring-2 focus:ring-orange-500">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ request()->query('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Tanggal Order <span class="text-red-500">*</span></label>
                            <input type="date" name="order_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Estimasi Tiba</label>
                            <input type="date" name="expected_date" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Jatuh Tempo Pembayaran</label>
                            <input type="date" name="payment_due_date" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">
                        </div>
                    </div>
                </div>

                {{-- Products Selection --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-bold text-slate-800">Daftar Item Barang</h3>
                        <button type="button" @click="addRow()" class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xs transition">+ Tambah Baris</button>
                    </div>

                    {{-- Dynamic rows --}}
                    <div class="space-y-3">
                        <template x-for="(row, index) in rows" :key="index">
                            <div class="grid grid-cols-12 gap-2 items-center bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                                {{-- Product select or custom name --}}
                                <div class="col-span-4">
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Pilih Produk</label>
                                    <select x-model="row.product_id" @change="productSelected(row)" class="w-full border border-slate-200 rounded-lg p-1.5 text-xs bg-white">
                                        <option value="">-- Ketik Nama Manual --</option>
                                        <template x-for="p in products" :key="p.id">
                                            <option :value="p.id" x-text="p.name"></option>
                                        </template>
                                    </select>
                                    <input type="text" x-model="row.name" placeholder="Nama item manual..." x-show="!row.product_id" required class="w-full border border-slate-200 rounded-lg p-1.5 text-xs bg-white mt-1">
                                </div>
                                {{-- Unit --}}
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Unit</label>
                                    <input type="text" x-model="row.unit" required placeholder="pcs/karton" class="w-full border border-slate-200 rounded-lg p-1.5 text-xs bg-white">
                                </div>
                                {{-- Qty --}}
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Qty</label>
                                    <input type="number" x-model="row.qty" required min="0.01" step="any" @input="updateSubtotal(row)" class="w-full border border-slate-200 rounded-lg p-1.5 text-xs bg-white">
                                </div>
                                {{-- Price --}}
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Harga Beli</label>
                                    <input type="number" x-model="row.price" required min="0" @input="updateSubtotal(row)" class="w-full border border-slate-200 rounded-lg p-1.5 text-xs bg-white">
                                </div>
                                {{-- Subtotal --}}
                                <div class="col-span-2 text-right">
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Subtotal</label>
                                    <span class="text-xs font-bold text-slate-800" x-text="'Rp ' + formatNumber(row.subtotal)">Rp 0</span>
                                </div>
                                {{-- Remove action --}}
                                <div class="col-span-12 flex justify-end mt-1">
                                    <button type="button" @click="removeRow(index)" class="text-red-500 hover:text-red-700 text-xs font-semibold">Hapus Baris</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Right Column (Summary & Notes) --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Ringkasan PO</h3>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal:</span>
                            <span class="font-bold text-slate-800" x-text="'Rp ' + formatNumber(grandTotal)">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Total Item:</span>
                            <span class="font-bold text-slate-800" x-text="rows.length">0</span>
                        </div>
                    </div>

                    <div class="bg-slate-900 text-white rounded-xl p-4 text-center">
                        <div class="text-xs opacity-75 mb-1">Total Estimasi Pembayaran</div>
                        <div class="text-xl font-extrabold" x-text="'Rp ' + formatNumber(grandTotal)">Rp 0</div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Catatan PO</label>
                        <textarea name="notes" rows="3" placeholder="Instruksi tambahan ke supplier..." class="w-full border border-slate-200 rounded-xl py-2 px-3 text-xs bg-slate-50"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition shadow-lg shadow-orange-500/20">Buat Purchase Order</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function poForm() {
    return {
        products: @json($products),
        rows: [],
        init() {
            this.addRow(); // start with one empty row
        },
        addRow() {
            this.rows.push({
                product_id: '',
                name: '',
                unit: 'pcs',
                qty: 1,
                price: 0,
                subtotal: 0
            });
        },
        removeRow(index) {
            if (this.rows.length > 1) {
                this.rows.splice(index, 1);
            } else {
                alert('Minimal harus ada 1 item.');
            }
        },
        productSelected(row) {
            if (row.product_id) {
                const prod = this.products.find(p => p.id == row.product_id);
                if (prod) {
                    row.name = prod.name;
                    // defaults
                    row.price = prod.purchase_price || 0;
                    row.unit = 'pcs';
                    this.updateSubtotal(row);
                }
            } else {
                row.name = '';
                row.price = 0;
                row.subtotal = 0;
            }
        },
        updateSubtotal(row) {
            const qty = parseFloat(row.qty) || 0;
            const price = parseFloat(row.price) || 0;
            row.subtotal = qty * price;
        },
        get grandTotal() {
            return this.rows.reduce((s, r) => s + r.subtotal, 0);
        },
        formatNumber(v) {
            return new Intl.NumberFormat('id-ID').format(v || 0);
        },
        handleSubmit(event) {
            // Validation
            if (this.rows.some(r => !r.name || r.qty <= 0 || r.price < 0)) {
                alert('Harap lengkapi seluruh baris item, pastikan qty > 0.');
                event.preventDefault();
                return;
            }
            // inject hidden inputs for items
            const form = event.target;
            this.rows.forEach((r, i) => {
                const prefix = `items[${i}]`;
                this.createHiddenInput(form, `${prefix}[product_id]`, r.product_id);
                this.createHiddenInput(form, `${prefix}[name]`, r.name);
                this.createHiddenInput(form, `${prefix}[unit]`, r.unit);
                this.createHiddenInput(form, `${prefix}[qty]`, r.qty);
                this.createHiddenInput(form, `${prefix}[price]`, r.price);
            });
        },
        createHiddenInput(form, name, value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value || '';
            form.appendChild(input);
        }
    };
}
</script>
@endsection
