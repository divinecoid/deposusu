@extends('layouts.admin')

@section('header', 'Tambah Order Manual')

@section('content')
<div class="max-w-6xl mx-auto" x-data="orderForm()">
    <!-- Back button & Title -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1.5 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Order
            </a>
            <h1 class="text-3xl font-extrabold text-gray-900 mt-2">Buat Order Baru</h1>
        </div>
    </div>

    <!-- Error/Alert banner -->
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm animate-fade-in-up">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.orders.store') }}" method="POST" @submit="handleSubmit($event)">
        @csrf

        <!-- Dynamic hidden inputs for products list -->
        <template x-for="(item, index) in selectedItems" :key="item.product_id">
            <div>
                <input type="hidden" :name="'products[' + index + '][id]'" :value="item.product_id">
                <input type="hidden" :name="'products[' + index + '][quantity]'" :value="item.quantity">
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Order details & items selector -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Details Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Detail Pelanggan
                    </h3>

                    <!-- Customer Type Toggle -->
                    <div class="flex bg-gray-100 rounded-xl p-1 mb-6 max-w-sm">
                        <button type="button" @click="customerType = 'existing'" 
                            class="flex-1 py-2 text-sm font-semibold rounded-lg transition-all duration-200"
                            :class="customerType === 'existing' ? 'bg-white text-blue-600 shadow' : 'text-gray-500 hover:text-gray-700'">
                            Pelanggan Terdaftar
                        </button>
                        <button type="button" @click="customerType = 'new'" 
                            class="flex-1 py-2 text-sm font-semibold rounded-lg transition-all duration-200"
                            :class="customerType === 'new' ? 'bg-white text-blue-600 shadow' : 'text-gray-500 hover:text-gray-700'">
                            Walk-in / Guest Baru
                        </button>
                    </div>

                    <!-- Existing Customer Select -->
                    <div x-show="customerType === 'existing'" class="space-y-2">
                        <label for="customer_id" class="block text-sm font-semibold text-gray-700">Pilih Pelanggan</label>
                        <select name="customer_id" id="customer_id" x-model="customerId"
                            class="block w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 hover:bg-gray-100 transition duration-150">
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- New Customer Input -->
                    <div x-show="customerType === 'new'" class="space-y-2" x-cloak>
                        <label for="customer_name" class="block text-sm font-semibold text-gray-700">Nama Pelanggan Baru</label>
                        <input type="text" name="customer_name" id="customer_name" x-model="customerName" placeholder="Masukkan nama lengkap pelanggan"
                            class="block w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 hover:bg-white transition duration-150">
                    </div>
                </div>

                <!-- Products Selection Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Pilih Produk & Tambah ke Order
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div class="md:col-span-2 space-y-2">
                            <label for="selected_product_id" class="block text-sm font-semibold text-gray-700">Pilih Produk</label>
                            <select id="selected_product_id" x-model="selectedProductId" @change="handleProductChange()"
                                class="block w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 hover:bg-gray-100 transition duration-150">
                                <option value="">-- Pilih Produk --</option>
                                <template x-for="p in productsList" :key="p.id">
                                    <option :value="p.id" x-text="p.name + ' (Stok: ' + p.stock + ')'"></option>
                                </template>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="selected_qty" class="block text-sm font-semibold text-gray-700">Jumlah (Qty)</label>
                            <div class="flex gap-2">
                                <input type="number" id="selected_qty" x-model="selectedQty" min="1" :max="selectedMaxStock"
                                    class="block w-24 border border-gray-200 rounded-xl shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center bg-gray-50">
                                <button type="button" @click="addItem()"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl transition duration-150 shadow-md shadow-blue-500/10 flex items-center justify-center gap-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Product dynamic info -->
                    <div x-show="selectedProduct" class="mt-4 p-3 bg-blue-50/50 border border-blue-100 rounded-xl text-sm flex justify-between items-center" x-cloak>
                        <div>
                            <span class="text-blue-800 font-semibold">Harga Asli:</span>
                            <span class="text-gray-700" x-text="formatRupiah(selectedProduct.price)"></span>
                            <template x-if="selectedProduct.active_discount">
                                <span class="ml-2 text-red-500 font-bold" x-text="'(Diskon: ' + formatRupiah(selectedProduct.price - selectedProduct.discounted_price) + ')'"></span>
                            </template>
                        </div>
                        <div>
                            <span class="text-blue-800 font-semibold">Harga Final:</span>
                            <span class="text-blue-600 font-bold" x-text="formatRupiah(selectedProduct.discounted_price)"></span>
                        </div>
                    </div>
                </div>

                <!-- Selected Items List Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900">Daftar Produk Ditambahkan</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50/30">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Produk</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga Unit</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Diskon</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Subtotal</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <template x-for="(item, index) in selectedItems" :key="item.product_id">
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900" x-text="item.name"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600" x-text="formatRupiah(item.original_price)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button" @click="decreaseQty(item)" class="p-1 hover:bg-gray-100 rounded text-gray-500 hover:text-gray-900 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                                </button>
                                                <span class="w-8 font-bold text-gray-900 text-center" x-text="item.quantity"></span>
                                                <button type="button" @click="increaseQty(item)" class="p-1 hover:bg-gray-100 rounded text-gray-500 hover:text-gray-900 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-red-500 font-medium" x-text="item.discount_amount > 0 ? '-' + formatRupiah(item.discount_amount * item.quantity) : '-'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900" x-text="formatRupiah(item.subtotal)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700 p-1.5 hover:bg-red-50 rounded-lg transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="selectedItems.length === 0">
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm italic">Belum ada produk yang ditambahkan ke pesanan ini.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side: Order summary & Submit -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-3">Ringkasan Pesanan</h3>

                    <div class="space-y-4 text-sm mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Jumlah Produk (Qty)</span>
                            <span class="font-bold text-gray-900" x-text="totalItems"></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Total Diskon</span>
                            <span class="font-bold text-red-500" x-text="totalDiscount > 0 ? '-' + formatRupiah(totalDiscount) : '-'"></span>
                        </div>
                        <div class="border-t border-gray-100 pt-4 flex justify-between items-end">
                            <span class="text-base font-bold text-gray-900">Total Akhir</span>
                            <span class="text-2xl font-extrabold text-blue-600" x-text="formatRupiah(grandTotal)"></span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition duration-150 shadow-lg shadow-blue-500/20 text-center flex items-center justify-center gap-2 group"
                        :disabled="selectedItems.length === 0">
                        <svg class="w-5 h-5 text-blue-200 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Buat Order</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function orderForm() {
    return {
        customerType: 'existing',
        customerId: '',
        customerName: '',
        productsList: @json($products),
        selectedProductId: '',
        selectedQty: 1,
        selectedItems: [],

        get selectedProduct() {
            if (!this.selectedProductId) return null;
            return this.productsList.find(p => p.id == this.selectedProductId) || null;
        },

        get selectedMaxStock() {
            return this.selectedProduct ? this.selectedProduct.stock : 1;
        },

        handleProductChange() {
            this.selectedQty = 1;
        },

        addItem() {
            if (!this.selectedProductId) {
                alert('Silakan pilih produk terlebih dahulu.');
                return;
            }

            const prod = this.selectedProduct;
            const qty = parseInt(this.selectedQty);

            if (isNaN(qty) || qty < 1) {
                alert('Jumlah minimal adalah 1.');
                return;
            }

            const existingItem = this.selectedItems.find(item => item.product_id == prod.id);
            if (existingItem) {
                const newQty = existingItem.quantity + qty;
                if (newQty > prod.stock) {
                    alert(`Stok tidak mencukupi untuk ${prod.name}. Stok tersedia: ${prod.stock}`);
                    return;
                }
                existingItem.quantity = newQty;
                existingItem.subtotal = existingItem.price * existingItem.quantity;
            } else {
                if (qty > prod.stock) {
                    alert(`Stok tidak mencukupi untuk ${prod.name}. Stok tersedia: ${prod.stock}`);
                    return;
                }

                // Check active discount
                let finalPrice = parseFloat(prod.discounted_price || prod.price);
                let discountValue = parseFloat(prod.price) - finalPrice;

                const item = {
                    product_id: prod.id,
                    name: prod.name,
                    original_price: parseFloat(prod.price),
                    price: finalPrice,
                    quantity: qty,
                    discount_amount: discountValue,
                    subtotal: finalPrice * qty,
                    stock: prod.stock
                };

                this.selectedItems.push(item);
            }

            // reset selection inputs
            this.selectedProductId = '';
            this.selectedQty = 1;
        },

        removeItem(index) {
            this.selectedItems.splice(index, 1);
        },

        increaseQty(item) {
            if (item.quantity + 1 > item.stock) {
                alert(`Stok tidak mencukupi. Maksimal: ${item.stock}`);
                return;
            }
            item.quantity++;
            item.subtotal = item.price * item.quantity;
        },

        decreaseQty(item) {
            if (item.quantity - 1 < 1) {
                return;
            }
            item.quantity--;
            item.subtotal = item.price * item.quantity;
        },

        get totalItems() {
            return this.selectedItems.reduce((sum, item) => sum + item.quantity, 0);
        },

        get totalDiscount() {
            return this.selectedItems.reduce((sum, item) => sum + (item.discount_amount * item.quantity), 0);
        },

        get grandTotal() {
            return this.selectedItems.reduce((sum, item) => sum + item.subtotal, 0);
        },

        formatRupiah(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(value);
        },

        handleSubmit(event) {
            if (this.customerType === 'existing' && !this.customerId) {
                alert('Silakan pilih pelanggan terdaftar terlebih dahulu.');
                event.preventDefault();
                return;
            }

            if (this.customerType === 'new' && !this.customerName.trim()) {
                alert('Silakan masukkan nama pelanggan baru.');
                event.preventDefault();
                return;
            }

            if (this.selectedItems.length === 0) {
                alert('Silakan tambahkan minimal satu produk ke daftar belanja.');
                event.preventDefault();
                return;
            }
        }
    }
}
</script>
@endsection
