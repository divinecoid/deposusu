@extends('layouts.admin')

@section('header', 'Kasir POS Terminal')

@section('content')
<div x-data="posApp()" class="h-[calc(100vh-8rem)] flex flex-col lg:flex-row gap-6">

    <!-- Left Column: Products Selection & Barcode Scanner -->
    <div class="flex-1 flex flex-col bg-white rounded-2xl shadow-sm border border-slate-100 p-6 overflow-hidden h-full">
        <!-- Barcode Scan Simulator & Search Bar -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="relative">
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Cari Produk</label>
                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Cari nama produk atau SKU..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Scan Barcode Simulator</label>
                <div class="relative">
                    <input type="text" x-model="barcodeQuery" @keydown.enter.prevent="scanBarcode()" placeholder="Ketik barcode/SKU & tekan Enter (ex: SKU-001)" class="w-full pl-10 pr-12 py-2.5 rounded-xl border-slate-200 text-sm font-mono focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </div>
                    <button @click="scanBarcode()" class="absolute inset-y-1.5 right-1.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center">
                        Scan
                    </button>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto pr-1">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div class="border border-slate-100 hover:border-indigo-100 rounded-xl p-4 bg-slate-50/50 hover:bg-indigo-50/10 cursor-pointer transition flex flex-col justify-between"
                         @click="addToCart(product)"
                         :class="{ 'opacity-50 pointer-events-none': product.stock <= 0 }">
                        <div>
                            <!-- Product Image or Placeholder -->
                            <div class="w-full h-24 bg-slate-100 rounded-lg mb-3 flex items-center justify-center overflow-hidden">
                                <template x-if="product.image">
                                    <img :src="product.image" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!product.image">
                                    <span class="text-xs text-slate-400">No Image</span>
                                </template>
                            </div>
                            <span class="text-xs font-mono text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded" x-text="product.sku"></span>
                            <h4 class="font-bold text-slate-800 text-sm mt-1.5 line-clamp-2" x-text="product.name"></h4>
                        </div>
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                                <span>Stok: <strong x-text="product.stock"></strong></span>
                                <template x-if="product.active_discount">
                                    <span class="text-rose-600 font-bold bg-rose-50 px-1.5 rounded" x-text="formatPromo(product.active_discount)"></span>
                                </template>
                            </div>
                            <div class="flex items-baseline gap-1.5">
                                <span class="font-extrabold text-indigo-600 text-sm" x-text="formatRupiah(product.discounted_price || product.price)"></span>
                                <template x-if="product.active_discount">
                                    <span class="text-[10px] text-slate-400 line-through" x-text="formatRupiah(product.price)"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Middle Column: POS Cart & Payments -->
    <div class="w-full lg:w-96 flex flex-col bg-white rounded-2xl shadow-sm border border-slate-100 p-6 overflow-hidden h-full">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center justify-between">
            <span>Keranjang Belanja</span>
            <span class="text-xs font-semibold px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-full" x-text="cart.length + ' item'"></span>
        </h3>

        <!-- Customer Selector -->
        <div class="mb-4">
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Pilih Customer</label>
            <select x-model="selectedCustomerId" @change="updateCustomerDetails()" class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">-- Walk-in / Guest Customer --</option>
                <template x-for="c in customers" :key="c.id">
                    <option :value="c.id" x-text="c.name + ' (' + c.membership + ')'"></option>
                </template>
            </select>
            <template x-if="!selectedCustomerId">
                <input type="text" x-model="guestCustomerName" placeholder="Nama Guest Customer..." class="mt-2 w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </template>
            <template x-if="selectedCustomerId && activeCustomer">
                <div class="mt-2 p-2 bg-indigo-50 rounded-lg text-xs flex items-center justify-between">
                    <span class="text-slate-600">Member Level: <strong class="text-indigo-600" x-text="activeCustomer.membership"></strong></span>
                    <span class="text-slate-600">Telp: <span x-text="activeCustomer.phone"></span></span>
                </div>
            </template>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto mb-4 border-y border-slate-100 py-3">
            <template x-if="cart.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-slate-400">
                    <svg class="w-12 h-12 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span class="text-xs">Keranjang masih kosong</span>
                </div>
            </template>
            <div class="space-y-3">
                <template x-for="(item, index) in cart" :key="item.product_id">
                    <div class="flex items-center justify-between gap-3 text-sm bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 truncate" x-text="item.name"></p>
                            <p class="text-xs text-indigo-600 font-bold" x-text="formatRupiah(item.price)"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="decreaseQty(index)" class="p-1 rounded bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold">-</button>
                            <span class="w-6 text-center font-bold text-slate-800 text-xs" x-text="item.quantity"></span>
                            <button @click="increaseQty(index)" class="p-1 rounded bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold">+</button>
                        </div>
                        <button @click="removeFromCart(index)" class="text-rose-500 hover:text-rose-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Checkout Info -->
        <div class="space-y-2.5 text-xs text-slate-600 mb-4">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span class="font-bold text-slate-800" x-text="formatRupiah(cartSubtotal())"></span>
            </div>
            <div class="flex items-center justify-between gap-4">
                <span>Promo / Diskon Toko</span>
                <div class="flex items-center gap-1.5 w-32">
                    <span class="text-slate-400">Rp</span>
                    <input type="number" x-model.number="shopDiscount" class="w-full p-1 rounded border-slate-200 text-right text-xs font-bold" min="0">
                </div>
            </div>
            <div class="flex justify-between text-sm border-t border-dashed border-slate-200 pt-2.5">
                <span class="font-extrabold text-slate-800">Total Akhir</span>
                <span class="font-black text-indigo-600 text-base" x-text="formatRupiah(cartTotal())"></span>
            </div>
        </div>

        <!-- Payment Selection -->
        <div class="mb-4">
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1.5">Metode Pembayaran</label>
            <div class="grid grid-cols-2 gap-2">
                <template x-for="method in ['CASH', 'QRIS', 'TRANSFER', 'DEBIT']">
                    <button type="button" 
                            @click="paymentMethod = method" 
                            class="py-2 rounded-xl text-xs font-bold text-center border transition"
                            :class="paymentMethod === method ? 'bg-indigo-600 border-indigo-600 text-white shadow' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200'"
                            x-text="method">
                    </button>
                </template>
            </div>
        </div>

        <!-- Checkout Button -->
        <button type="button" 
                @click="processCheckout()" 
                class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2"
                :disabled="cart.length === 0 || loading"
                :class="{ 'opacity-50 cursor-not-allowed': cart.length === 0 || loading }">
            <span x-show="loading" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
            <span x-text="loading ? 'Memproses...' : 'Checkout & Cetak Invoice'"></span>
        </button>
    </div>

    <!-- Right Column: Print Invoice simulator -->
    <div class="w-full lg:w-80 flex flex-col bg-white rounded-2xl shadow-sm border border-slate-100 p-6 overflow-hidden h-full">
        <h3 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider text-slate-500">Struk / Invoice Generator</h3>

        <div class="flex-1 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-xl p-4 bg-slate-50/50 overflow-hidden">
            <template x-if="!lastInvoice">
                <div class="text-center text-slate-400 text-xs">
                    <svg class="w-12 h-12 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Belum ada transaksi POS yang diselesaikan.
                </div>
            </template>
            
            <template x-if="lastInvoice">
                <div class="w-full h-full flex flex-col justify-between">
                    <!-- Virtual Thermal Receipt -->
                    <div class="bg-white p-4 shadow-sm border border-slate-100 rounded-lg text-xs font-mono space-y-3 overflow-y-auto max-h-[300px]">
                        <div class="text-center border-b border-dashed border-slate-200 pb-2">
                            <h4 class="font-bold text-sm">DEPOSUSU</h4>
                            <p class="text-[10px] text-slate-500">Gudang & POS Terminal Utama</p>
                            <p class="text-[10px] text-slate-500">Jakarta Selatan</p>
                        </div>
                        <div class="space-y-1 text-[10px]">
                            <p>No: <span x-text="lastInvoice.order_number"></span></p>
                            <p>Cust: <span x-text="lastInvoice.customer_name"></span></p>
                            <p>Date: <span x-text="lastInvoice.date"></span></p>
                            <p>Bayar: <span x-text="lastInvoice.payment_method"></span></p>
                        </div>
                        <div class="border-t border-b border-dashed border-slate-200 py-2 space-y-1.5">
                            <template x-for="item in lastInvoice.items">
                                <div class="flex justify-between text-[10px]">
                                    <div class="max-w-[70%]">
                                        <span x-text="item.name"></span>
                                        <br>
                                        <span class="text-slate-400" x-text="item.qty + 'x ' + formatRupiah(item.price)"></span>
                                    </div>
                                    <span x-text="formatRupiah(item.subtotal)"></span>
                                </div>
                            </template>
                        </div>
                        <div class="space-y-1 text-[10px] text-right">
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span x-text="formatRupiah(lastInvoice.subtotal)"></span>
                            </div>
                            <template x-if="lastInvoice.discount > 0">
                                <div class="flex justify-between text-rose-600">
                                    <span>Promo:</span>
                                    <span x-text="'-' + formatRupiah(lastInvoice.discount)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between font-bold text-slate-800 border-t border-slate-100 pt-1">
                                <span>TOTAL:</span>
                                <span x-text="formatRupiah(lastInvoice.total)"></span>
                            </div>
                        </div>
                        <div class="text-center pt-2 border-t border-dashed border-slate-200 text-[9px] text-slate-400">
                            Terima kasih atas kunjungan Anda!
                        </div>
                    </div>

                    <!-- Print Action Button -->
                    <button @click="printReceipt()" class="w-full mt-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Struk Thermal
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function posApp() {
        return {
            products: @json($products),
            customers: @json($customers),
            searchQuery: '',
            barcodeQuery: '',
            selectedCustomerId: '',
            guestCustomerName: '',
            activeCustomer: null,
            paymentMethod: 'CASH',
            shopDiscount: 0,
            cart: [],
            lastInvoice: null,
            loading: false,

            init() {
                // Prepopulate guestCustomerName if customer is not selected
                this.guestCustomerName = 'Guest Walk-in';
            },

            updateCustomerDetails() {
                if (this.selectedCustomerId) {
                    this.activeCustomer = this.customers.find(c => c.id == this.selectedCustomerId) || null;
                } else {
                    this.activeCustomer = null;
                    this.guestCustomerName = 'Guest Walk-in';
                }
            },

            get filteredProducts() {
                if (!this.searchQuery) return this.products;
                let query = this.searchQuery.toLowerCase();
                return this.products.filter(p => 
                    p.name.toLowerCase().includes(query) || 
                    (p.sku && p.sku.toLowerCase().includes(query)) ||
                    (p.barcode && p.barcode.toLowerCase().includes(query))
                );
            },

            scanBarcode() {
                if (!this.barcodeQuery) return;
                let barcode = this.barcodeQuery.trim();
                let product = this.products.find(p => p.sku === barcode || p.barcode === barcode);
                if (product) {
                    if (product.stock <= 0) {
                        alert(`Produk ${product.name} kosong.`);
                    } else {
                        this.addToCart(product);
                    }
                } else {
                    alert(`Produk dengan SKU / Barcode "${barcode}" tidak ditemukan.`);
                }
                this.barcodeQuery = '';
            },

            addToCart(product) {
                if (product.stock <= 0) return;
                
                let cartItemIndex = this.cart.findIndex(item => item.product_id === product.id);
                if (cartItemIndex > -1) {
                    if (this.cart[cartItemIndex].quantity < product.stock) {
                        this.cart[cartItemIndex].quantity++;
                    } else {
                        alert('Tidak bisa menambah lebih banyak. Stok terbatas.');
                    }
                } else {
                    this.cart.push({
                        product_id: product.id,
                        name: product.name,
                        price: parseFloat(product.discounted_price || product.price),
                        quantity: 1,
                        stock: product.stock
                    });
                }
            },

            removeFromCart(index) {
                this.cart.splice(index, 1);
            },

            increaseQty(index) {
                let item = this.cart[index];
                if (item.quantity < item.stock) {
                    item.quantity++;
                }
            },

            decreaseQty(index) {
                let item = this.cart[index];
                if (item.quantity > 1) {
                    item.quantity--;
                } else {
                    this.removeFromCart(index);
                }
            },

            cartSubtotal() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            },

            cartTotal() {
                return Math.max(0, this.cartSubtotal() - this.shopDiscount);
            },

            formatRupiah(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            },

            formatPromo(discount) {
                if (discount.discount_type === 'PERCENTAGE') {
                    return `Disc ${discount.discount_value}%`;
                }
                return `Potongan ${this.formatRupiah(discount.discount_value)}`;
            },

            printReceipt() {
                // Open real browser print behavior on thermal layout
                let printWindow = window.open('', '_blank', 'width=350,height=600');
                printWindow.document.write(`
                    <html>
                    <head>
                        <title>Cetak Invoice - DEPOSUSU</title>
                        <style>
                            body { font-family: monospace; font-size: 10px; width: 80mm; margin: 0; padding: 10px; }
                            .center { text-align: center; }
                            .line { border-bottom: 1px dashed black; margin: 8px 0; }
                            .flex-between { display: flex; justify-content: space-between; }
                        </style>
                    </head>
                    <body>
                        <div class="center">
                            <h3>DEPOSUSU CO</h3>
                            <p>POS & GUDANG UTAMA</p>
                            <p>Jakarta Selatan</p>
                        </div>
                        <div class="line"></div>
                        <p>No: ${this.lastInvoice.order_number}</p>
                        <p>Cust: ${this.lastInvoice.customer_name}</p>
                        <p>Date: ${this.lastInvoice.date}</p>
                        <p>Payment: ${this.lastInvoice.payment_method}</p>
                        <div class="line"></div>
                        ${this.lastInvoice.items.map(item => `
                            <div class="flex-between">
                                <div>
                                    <span>${item.name}</span><br>
                                    <span style="color: gray;">${item.qty} x ${this.formatRupiah(item.price)}</span>
                                </div>
                                <span>${this.formatRupiah(item.subtotal)}</span>
                            </div>
                            <br>
                        `).join('')}
                        <div class="line"></div>
                        <div class="flex-between">
                            <span>Subtotal:</span>
                            <span>${this.formatRupiah(this.lastInvoice.subtotal)}</span>
                        </div>
                        ${this.lastInvoice.discount > 0 ? `
                            <div class="flex-between" style="color: red;">
                                <span>Promo:</span>
                                <span>-${this.formatRupiah(this.lastInvoice.discount)}</span>
                            </div>
                        ` : ''}
                        <div class="flex-between" style="font-weight: bold;">
                            <span>TOTAL:</span>
                            <span>${this.formatRupiah(this.lastInvoice.total)}</span>
                        </div>
                        <div class="line"></div>
                        <div class="center">
                            <p>Terima kasih atas belanja Anda!</p>
                        </div>
                        <script>
                            window.onload = function() { window.print(); window.close(); }
                        <\/script>
                    </body>
                    </html>
                `);
                printWindow.document.close();
            },

            async processCheckout() {
                if (this.cart.length === 0) return;
                this.loading = true;

                let data = {
                    customer_id: this.selectedCustomerId || null,
                    customer_name: this.selectedCustomerId ? null : this.guestCustomerName,
                    items: this.cart.map(item => {
                        return {
                            product_id: item.product_id,
                            quantity: item.quantity
                        };
                    }),
                    payment_method: this.paymentMethod,
                    discount_amount: this.shopDiscount
                };

                try {
                    let response = await fetch("{{ route('admin.kasir.checkout') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    });

                    let result = await response.json();

                    if (result.success) {
                        this.lastInvoice = result.invoice;
                        // Decrement local products stock
                        this.cart.forEach(item => {
                            let p = this.products.find(prod => prod.id === item.product_id);
                            if (p) p.stock -= item.quantity;
                        });
                        this.cart = [];
                        this.shopDiscount = 0;
                        this.selectedCustomerId = '';
                        this.updateCustomerDetails();
                        alert('Checkout POS berhasil diselesaikan!');
                    } else {
                        alert('Gagal checkout: ' + result.message);
                    }
                } catch (error) {
                    console.error(error);
                    alert('Gagal melakukan checkout POS. Coba lagi.');
                } finally {
                    this.loading = false;
                }
            }
        };
    }
</script>
@endsection
