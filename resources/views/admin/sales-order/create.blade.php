@extends('layouts.admin')

@section('header', 'Sales Order')

@section('content')
<div class="max-w-7xl mx-auto" x-data="salesOrderForm()" x-init="init()">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                <a href="{{ route('admin.sales-order.index') }}" class="hover:text-blue-600 transition">Sales Order</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-700 font-medium">Buat Baru</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-blue-600 text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                Buat Sales Order
            </h1>
            <p class="text-slate-500 text-sm mt-1">Order manual dari WhatsApp / kontak langsung</p>
        </div>
        <a href="{{ route('admin.sales-order.index') }}" class="flex items-center gap-2 text-sm text-slate-600 hover:text-blue-600 bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
            Daftar Sales Order
        </a>
    </div>

    {{-- Error Banner --}}
    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

    <form action="{{ route('admin.sales-order.store') }}" method="POST" @submit="handleSubmit($event)">
        @csrf

        {{-- Hidden product inputs --}}
        <template x-for="(item, index) in selectedItems" :key="item.product_id">
            <div>
                <input type="hidden" :name="'products[' + index + '][id]'" :value="item.product_id">
                <input type="hidden" :name="'products[' + index + '][quantity]'" :value="item.quantity">
            </div>
        </template>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- ============================================================ --}}
            {{-- LEFT COLUMN (2/3): Customer + Products + Delivery + Payment --}}
            {{-- ============================================================ --}}
            <div class="xl:col-span-2 space-y-5">

                {{-- ─── 1. CUSTOMER ─────────────────────────────────────── --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">1</span>
                        <h2 class="font-bold text-slate-800">Customer</h2>
                    </div>
                    <div class="p-6 space-y-5">

                        {{-- Toggle: Terdaftar vs Walk-in --}}
                        <div class="flex bg-slate-100 rounded-xl p-1 max-w-sm">
                            <button type="button" @click="customerType = 'registered'"
                                class="flex-1 py-2 text-sm font-semibold rounded-lg transition-all"
                                :class="customerType === 'registered' ? 'bg-white text-blue-600 shadow' : 'text-slate-500 hover:text-slate-700'">
                                Pelanggan Terdaftar
                            </button>
                            <button type="button" @click="customerType = 'walkin'"
                                class="flex-1 py-2 text-sm font-semibold rounded-lg transition-all"
                                :class="customerType === 'walkin' ? 'bg-white text-blue-600 shadow' : 'text-slate-500 hover:text-slate-700'">
                                Walk-in / Guest
                            </button>
                        </div>

                        {{-- Registered Customer Search --}}
                        <div x-show="customerType === 'registered'" class="space-y-4">
                            <div class="relative">
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Cari nama / nomor WhatsApp</label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <input type="text" x-model="customerSearch" @input.debounce.400ms="searchCustomers()"
                                        placeholder="Ketik nama atau nomor HP..."
                                        class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 hover:bg-white transition">
                                    <div x-show="searchLoading" class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <svg class="w-4 h-4 text-blue-400 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    </div>
                                </div>

                                {{-- Dropdown results --}}
                                <div x-show="searchResults.length > 0" x-cloak class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                                    <template x-for="result in searchResults" :key="result.id">
                                        <button type="button" @click="selectCustomer(result)"
                                            class="w-full flex items-center gap-3 px-4 py-3 hover:bg-blue-50 transition text-left border-b border-slate-50 last:border-0">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center flex-shrink-0" x-text="result.name.substring(0,2).toUpperCase()"></div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-semibold text-slate-800 text-sm truncate" x-text="result.name"></div>
                                                <div class="text-xs text-slate-500" x-text="result.phone || result.email"></div>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <input type="hidden" name="customer_id" :value="selectedCustomer ? selectedCustomer.id : ''">

                            {{-- Selected customer card --}}
                            <div x-show="selectedCustomer" x-cloak class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-sm flex-shrink-0"
                                    x-text="selectedCustomer ? selectedCustomer.name.substring(0,2).toUpperCase() : ''"></div>
                                <div class="flex-1 space-y-1">
                                    <div class="font-bold text-slate-800" x-text="selectedCustomer ? selectedCustomer.name : ''"></div>
                                    <div class="text-xs text-slate-600 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span x-text="selectedCustomer ? (selectedCustomer.phone || '-') : ''"></span>
                                    </div>
                                    <div class="text-xs text-slate-600 flex items-start gap-1">
                                        <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span x-text="selectedCustomer ? (selectedCustomer.address || 'Alamat belum diisi') : ''"></span>
                                    </div>
                                </div>
                                <button type="button" @click="clearCustomer()" class="text-slate-400 hover:text-red-500 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Walk-in / Guest Fields --}}
                        <div x-show="customerType === 'walkin'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Nama Pelanggan <span class="text-red-500">*</span></label>
                                <input type="text" name="customer_name" placeholder="Masukkan nama lengkap"
                                    class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 hover:bg-white transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">No. WhatsApp / HP</label>
                                <input type="text" name="customer_phone" placeholder="08xxx"
                                    class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 hover:bg-white transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Catatan Alamat</label>
                                <input type="text" name="customer_address" placeholder="Alamat pengiriman"
                                    class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 hover:bg-white transition">
                            </div>
                        </div>

                        {{-- Address override for registered customer --}}
                        <div x-show="customerType === 'registered' && selectedCustomer" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Override No. HP (opsional)</label>
                                <input type="text" name="customer_phone" x-model="overridePhone" placeholder="Biarkan kosong untuk pakai HP terdaftar"
                                    class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 hover:bg-white transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Override Alamat (opsional)</label>
                                <input type="text" name="customer_address" x-model="overrideAddress" placeholder="Biarkan kosong untuk pakai alamat terdaftar"
                                    class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 hover:bg-white transition">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ─── 2. PRODUK ────────────────────────────────────────── --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs">2</span>
                        <h2 class="font-bold text-slate-800">Pilih Produk</h2>
                    </div>
                    <div class="p-6 space-y-4">

                        {{-- Product search & add --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Cari Produk</label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <input type="text" x-model="productSearch" placeholder="Nama produk..."
                                        class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50 hover:bg-white transition">
                                </div>
                                <select x-model="selectedProductId" @change="handleProductChange()" class="mt-2 w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50 hover:bg-white transition">
                                    <option value="">-- Pilih Produk --</option>
                                    <template x-for="p in filteredProducts" :key="p.id">
                                        <option :value="p.id" x-text="p.name + ' · Stok: ' + p.stock + ' · Rp ' + formatNumber(p.discounted_price)"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Qty</label>
                                <div class="flex gap-2">
                                    <input type="number" x-model="selectedQty" min="1" :max="selectedMaxStock"
                                        class="w-20 border border-slate-200 rounded-xl py-2.5 px-3 text-sm text-center focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50">
                                    <button type="button" @click="addItem()"
                                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Tambah
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Selected product info chip --}}
                        <div x-show="selectedProduct" x-cloak class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-100 rounded-xl text-sm">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <div class="flex flex-wrap gap-x-4 gap-y-1">
                                <span class="text-slate-600">Harga: <strong class="text-slate-800" x-text="selectedProduct ? 'Rp ' + formatNumber(selectedProduct.price) : ''"></strong></span>
                                <template x-if="selectedProduct && selectedProduct.active_discount">
                                    <span class="text-red-600">Diskon: <strong x-text="selectedProduct ? 'Rp ' + formatNumber(selectedProduct.price - selectedProduct.discounted_price) : ''"></strong></span>
                                </template>
                                <span class="text-emerald-700 font-bold">Final: <strong x-text="selectedProduct ? 'Rp ' + formatNumber(selectedProduct.discounted_price) : ''"></strong></span>
                            </div>
                        </div>

                        {{-- Items Table --}}
                        <div class="border border-slate-100 rounded-xl overflow-hidden">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wide">Produk</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wide">Harga</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wide w-36">Qty</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wide">Subtotal</th>
                                        <th class="px-4 py-3 w-12"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 bg-white">
                                    <template x-for="(item, index) in selectedItems" :key="item.product_id">
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <td class="px-4 py-3 text-sm font-semibold text-slate-800" x-text="item.name"></td>
                                            <td class="px-4 py-3 text-sm text-right text-slate-600" x-text="'Rp ' + formatNumber(item.price)"></td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button type="button" @click="decreaseQty(item)" class="w-6 h-6 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                                                    </button>
                                                    <span class="w-8 text-center font-bold text-slate-800 text-sm" x-text="item.quantity"></span>
                                                    <button type="button" @click="increaseQty(item)" class="w-6 h-6 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm font-bold text-right text-slate-800" x-text="'Rp ' + formatNumber(item.subtotal)"></td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" @click="removeItem(index)" class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 hover:text-red-700 flex items-center justify-center transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="selectedItems.length === 0">
                                        <tr>
                                            <td colspan="5" class="px-4 py-10 text-center text-slate-400 text-sm">
                                                <svg class="w-10 h-10 mx-auto mb-2 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                Belum ada produk ditambahkan
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ─── 3. DELIVERY ──────────────────────────────────────── --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center font-bold text-xs">3</span>
                        <h2 class="font-bold text-slate-800">Jadwal Pengiriman</h2>
                        <span class="text-xs text-slate-400 ml-auto">Opsional</span>
                    </div>
                    <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Tanggal Kirim</label>
                            <input type="date" name="delivery_date" min="{{ date('Y-m-d') }}"
                                class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 bg-slate-50 hover:bg-white transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Sesi Pengiriman</label>
                            <select name="delivery_slot" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 bg-slate-50 hover:bg-white transition">
                                <option value="">Auto / Tidak ditentukan</option>
                                <option value="pagi">🌅 Pagi (07.00 – 12.00)</option>
                                <option value="siang">☀️ Siang (12.00 – 17.00)</option>
                                <option value="sore">🌇 Sore (17.00 – 21.00)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Kurir</label>
                            <select name="driver_id" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 bg-slate-50 hover:bg-white transition">
                                <option value="">🔄 Auto Assign</option>
                                @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ─── 4. PAYMENT ───────────────────────────────────────── --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-xs">4</span>
                        <h2 class="font-bold text-slate-800">Metode Pembayaran</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                            @foreach([
                                ['value' => 'transfer', 'label' => 'Transfer', 'icon' => '🏦'],
                                ['value' => 'cash',     'label' => 'Cash',     'icon' => '💵'],
                                ['value' => 'cod',      'label' => 'COD',      'icon' => '🚚'],
                                ['value' => 'wallet',   'label' => 'Wallet',   'icon' => '👜'],
                                ['value' => 'piutang',  'label' => 'Piutang (B2B)', 'icon' => '📋'],
                            ] as $pm)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="payment_method" value="{{ $pm['value'] }}"
                                    class="sr-only peer" {{ $loop->first ? 'checked' : '' }}>
                                <div class="flex flex-col items-center gap-1.5 p-3 border-2 border-slate-200 rounded-xl text-center text-sm font-semibold text-slate-600 transition peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700 hover:border-amber-300">
                                    <span class="text-xl">{{ $pm['icon'] }}</span>
                                    <span class="text-xs leading-tight">{{ $pm['label'] }}</span>
                                </div>
                                <div class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-amber-500 peer-checked:bg-amber-500 hidden peer-checked:flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- ─── 5. CATATAN ───────────────────────────────────────── --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs">5</span>
                        <h2 class="font-bold text-slate-800">Catatan Order</h2>
                        <span class="text-xs text-slate-400 ml-auto">Opsional</span>
                    </div>
                    <div class="p-6">
                        <textarea name="notes" rows="3" placeholder="Catatan dari customer via WhatsApp, permintaan khusus, dll..."
                            class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 bg-slate-50 hover:bg-white transition resize-none"></textarea>
                    </div>
                </div>

            </div>

            {{-- ============================================================ --}}
            {{-- RIGHT COLUMN (1/3): Order Summary + Submit                   --}}
            {{-- ============================================================ --}}
            <div class="xl:col-span-1">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sticky top-6 space-y-5">
                    <h3 class="font-bold text-slate-800 text-base border-b border-slate-100 pb-3">Ringkasan Pesanan</h3>

                    {{-- Customer summary --}}
                    <div x-show="selectedCustomer || customerType === 'walkin'" x-cloak class="text-sm space-y-1 pb-4 border-b border-slate-100">
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Customer</div>
                        <div class="font-semibold text-slate-700" x-text="selectedCustomer ? selectedCustomer.name : 'Walk-in / Guest'"></div>
                        <div class="text-slate-500 text-xs" x-show="selectedCustomer" x-text="selectedCustomer ? (selectedCustomer.phone || '') : ''"></div>
                    </div>

                    {{-- Products count --}}
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Items</span>
                            <span class="font-bold text-slate-800" x-text="selectedItems.length + ' produk (' + totalQty + ' qty)'"></span>
                        </div>
                        <div x-show="totalDiscount > 0" class="flex justify-between text-slate-600">
                            <span>Total Diskon</span>
                            <span class="font-bold text-red-500" x-text="'- Rp ' + formatNumber(totalDiscount)"></span>
                        </div>
                    </div>

                    {{-- Grand total --}}
                    <div class="bg-slate-900 rounded-xl p-4 text-white text-center">
                        <div class="text-xs font-semibold text-slate-400 mb-1">Total Pembayaran</div>
                        <div class="text-2xl font-extrabold" x-text="'Rp ' + formatNumber(grandTotal)">Rp 0</div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" id="btn-create-so"
                        class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-bold rounded-xl transition shadow-lg shadow-blue-600/25 flex items-center justify-center gap-2 group"
                        :disabled="selectedItems.length === 0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        Buat Sales Order
                    </button>

                    <p class="text-xs text-slate-400 text-center">Invoice akan otomatis di-generate setelah order dibuat.</p>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
function salesOrderForm() {
    return {
        customerType: 'registered',
        customerSearch: '',
        searchResults: [],
        searchLoading: false,
        selectedCustomer: null,
        overridePhone: '',
        overrideAddress: '',

        productSearch: '',
        productsList: @json($products),
        selectedProductId: '',
        selectedQty: 1,
        selectedItems: [],

        searchTimer: null,

        init() {},

        get filteredProducts() {
            const q = this.productSearch.toLowerCase();
            if (!q) return this.productsList;
            return this.productsList.filter(p => p.name.toLowerCase().includes(q));
        },

        get selectedProduct() {
            if (!this.selectedProductId) return null;
            return this.productsList.find(p => p.id == this.selectedProductId) || null;
        },

        get selectedMaxStock() {
            return this.selectedProduct ? this.selectedProduct.stock : 1;
        },

        async searchCustomers() {
            const q = this.customerSearch.trim();
            if (q.length < 2) { this.searchResults = []; return; }
            this.searchLoading = true;
            try {
                const res = await fetch(`{{ route('admin.sales-order.search-customers') }}?q=` + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.searchResults = await res.json();
            } catch(e) { this.searchResults = []; }
            this.searchLoading = false;
        },

        selectCustomer(c) {
            this.selectedCustomer = c;
            this.searchResults = [];
            this.customerSearch = c.name;
        },

        clearCustomer() {
            this.selectedCustomer = null;
            this.customerSearch = '';
            this.overridePhone = '';
            this.overrideAddress = '';
        },

        handleProductChange() {
            this.selectedQty = 1;
        },

        addItem() {
            if (!this.selectedProductId) { alert('Pilih produk terlebih dahulu.'); return; }
            const prod = this.selectedProduct;
            const qty = parseInt(this.selectedQty);
            if (isNaN(qty) || qty < 1) { alert('Jumlah minimal 1.'); return; }

            const existing = this.selectedItems.find(i => i.product_id == prod.id);
            if (existing) {
                const newQty = existing.quantity + qty;
                if (newQty > prod.stock) { alert(`Stok tidak cukup. Tersedia: ${prod.stock}`); return; }
                existing.quantity = newQty;
                existing.subtotal = existing.price * existing.quantity;
            } else {
                if (qty > prod.stock) { alert(`Stok tidak cukup. Tersedia: ${prod.stock}`); return; }
                const finalPrice = parseFloat(prod.discounted_price || prod.price);
                this.selectedItems.push({
                    product_id: prod.id,
                    name: prod.name,
                    original_price: parseFloat(prod.price),
                    price: finalPrice,
                    quantity: qty,
                    discount_amount: parseFloat(prod.price) - finalPrice,
                    subtotal: finalPrice * qty,
                    stock: prod.stock,
                });
            }
            this.selectedProductId = '';
            this.selectedQty = 1;
            this.productSearch = '';
        },

        removeItem(index) { this.selectedItems.splice(index, 1); },

        increaseQty(item) {
            if (item.quantity + 1 > item.stock) { alert(`Stok maksimal: ${item.stock}`); return; }
            item.quantity++;
            item.subtotal = item.price * item.quantity;
        },

        decreaseQty(item) {
            if (item.quantity <= 1) return;
            item.quantity--;
            item.subtotal = item.price * item.quantity;
        },

        get totalQty() { return this.selectedItems.reduce((s, i) => s + i.quantity, 0); },
        get totalDiscount() { return this.selectedItems.reduce((s, i) => s + (i.discount_amount * i.quantity), 0); },
        get grandTotal() { return this.selectedItems.reduce((s, i) => s + i.subtotal, 0); },

        formatNumber(v) { return new Intl.NumberFormat('id-ID').format(v || 0); },

        handleSubmit(event) {
            if (this.customerType === 'registered' && !this.selectedCustomer) {
                alert('Silakan pilih customer terdaftar atau gunakan mode Walk-in.'); event.preventDefault(); return;
            }
            if (this.selectedItems.length === 0) {
                alert('Tambahkan minimal 1 produk ke order.'); event.preventDefault(); return;
            }
        },
    };
}
</script>
@endsection
