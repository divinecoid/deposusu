<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" translate="no" class="notranslate">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google" content="notranslate">
    <title>Admin Dashboard - DEPOSUSU</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100 font-sans antialiased h-screen overflow-hidden">
    <div class="h-screen flex overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-800 text-white flex-shrink-0 hidden md:flex flex-col h-full">
            <div class="h-16 flex items-center justify-center border-b border-slate-700 flex-shrink-0">
                <span class="text-xl font-bold">DEPOSUSU</span>
            </div>

            <nav class="mt-4 px-3 space-y-1.5 overflow-y-auto flex-1 pb-4" x-data="{ openGroup: '{{ request()->segment(2) }}' }">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700 font-bold' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </div>
                </a>

                <!-- Order Management Group -->
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'orders' ? '' : 'orders')" 
                            class="w-full flex items-center justify-between py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition text-slate-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            Order Management
                        </div>
                        <svg class="w-3.5 h-3.5 transform transition duration-200" :class="openGroup === 'orders' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'orders'" class="pl-7 space-y-1" style="display: none;">
                        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->query('status') === 'pending' ? 'bg-slate-700 text-white font-bold' : '' }}">Pesanan Masuk</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'onprocess']) }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->query('status') === 'onprocess' ? 'bg-slate-700 text-white font-bold' : '' }}">Diproses</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'onpreparation']) }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->query('status') === 'onpreparation' ? 'bg-slate-700 text-white font-bold' : '' }}">Packing</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'ondelivery']) }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->query('status') === 'ondelivery' ? 'bg-slate-700 text-white font-bold' : '' }}">Dikirim</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'done']) }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->query('status') === 'done' ? 'bg-slate-700 text-white font-bold' : '' }}">Selesai</a>
                    </div>
                </div>

                <!-- Sales Order -->
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'sales-order' ? '' : 'sales-order')"
                            class="w-full flex items-center justify-between py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition text-slate-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Sales Order
                            <span class="ml-auto text-xs bg-green-500 text-white font-bold px-1.5 py-0.5 rounded-full leading-none">WA</span>
                        </div>
                        <svg class="w-3.5 h-3.5 transform transition duration-200" :class="openGroup === 'sales-order' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'sales-order'" class="pl-7 space-y-1" style="display: none;">
                        <a href="{{ route('admin.sales-order.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.sales-order.index') ? 'bg-slate-700 text-white font-bold' : '' }}">Daftar Sales Order</a>
                        <a href="{{ route('admin.sales-order.create') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.sales-order.create') ? 'bg-slate-700 text-white font-bold' : '' }}">+ Buat Order Baru</a>
                    </div>
                </div>

                <!-- Customer Management Group -->
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'customers' ? '' : 'customers')" 
                            class="w-full flex items-center justify-between py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition text-slate-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Customer Management
                        </div>
                        <svg class="w-3.5 h-3.5 transform transition duration-200" :class="openGroup === 'customers' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'customers'" class="pl-7 space-y-1" style="display: none;">
                        <a href="{{ route('admin.master.customers.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.master.customers.*') ? 'bg-slate-700 text-white font-bold' : '' }}">Data Customer</a>
                        <a href="{{ route('admin.orders.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white">Riwayat Order</a>
                        <a href="{{ route('admin.membership') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.membership') ? 'bg-slate-700 text-white font-bold' : '' }}">Membership / VIP</a>
                    </div>
                </div>

                <!-- Live Chat -->
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'live-chat' ? '' : 'live-chat')" 
                            class="w-full flex items-center justify-between py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition text-slate-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            Live Chat
                        </div>
                        <svg class="w-3.5 h-3.5 transform transition duration-200" :class="openGroup === 'live-chat' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'live-chat'" class="pl-7 space-y-1" style="display: none;">
                        <a href="{{ route('admin.live-chat.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white">Chat Masuk</a>
                        <a href="{{ route('admin.live-chat.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white">Chat Aktif</a>
                        <a href="{{ route('admin.live-chat.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white">Chat Selesai</a>
                        <a href="{{ route('admin.live-chat.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white">Riwayat Chat</a>
                    </div>
                </div>

                <!-- Kasir (POS) -->
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'kasir' ? '' : 'kasir')" 
                            class="w-full flex items-center justify-between py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition text-slate-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Kasir (POS)
                        </div>
                        <svg class="w-3.5 h-3.5 transform transition duration-200" :class="openGroup === 'kasir' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'kasir'" class="pl-7 space-y-1" style="display: none;">
                        <a href="{{ route('admin.kasir.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.kasir.index') ? 'bg-slate-700 text-white font-bold' : '' }}">Transaksi Offline</a>
                        <a href="{{ route('admin.kasir.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white">Scan Barcode</a>
                        <a href="{{ route('admin.kasir.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white">Pembayaran</a>
                        <a href="{{ route('admin.live-chat.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white">Refund</a>
                        <a href="{{ route('admin.kasir.shift') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.kasir.shift') ? 'bg-slate-700 text-white font-bold' : '' }}">Shift Kasir</a>
                    </div>
                </div>

                <!-- Invoice Management -->
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'invoices' ? '' : 'invoices')" 
                            class="w-full flex items-center justify-between py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition text-slate-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Invoice Management
                        </div>
                        <svg class="w-3.5 h-3.5 transform transition duration-200" :class="openGroup === 'invoices' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'invoices'" class="pl-7 space-y-1" style="display: none;">
                        <a href="{{ route('admin.invoices.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.invoices.index') ? 'bg-slate-700 text-white font-bold' : '' }}">Semua Invoice</a>
                    </div>
                </div>

                <!-- Payment Management -->
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'payments' ? '' : 'payments')" 
                            class="w-full flex items-center justify-between py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition text-slate-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Payment Management
                        </div>
                        <svg class="w-3.5 h-3.5 transform transition duration-200" :class="openGroup === 'payments' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'payments'" class="pl-7 space-y-1" style="display: none;">
                        <a href="{{ route('admin.payments.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.payments.index') ? 'bg-slate-700 text-white font-bold' : '' }}">Konfirmasi Pembayaran</a>
                        <a href="{{ route('admin.payments.create') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.payments.create') ? 'bg-slate-700 text-white font-bold' : '' }}">Catat Pembayaran</a>
                    </div>
                </div>

                <!-- Finance -->
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'finance' ? '' : 'finance')" 
                            class="w-full flex items-center justify-between py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition text-slate-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            Finance
                        </div>
                        <svg class="w-3.5 h-3.5 transform transition duration-200" :class="openGroup === 'finance' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'finance'" class="pl-7 space-y-1" style="display: none;">
                        <a href="{{ route('admin.finance.index', ['type' => 'income']) }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white">Pendapatan</a>
                        <a href="{{ route('admin.finance.index', ['type' => 'expense']) }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white">Pengeluaran</a>
                        <a href="{{ route('admin.finance.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.finance.index') ? 'bg-slate-700 text-white font-bold' : '' }}">Kas & Bank</a>
                    </div>
                </div>

                <!-- Inventory -->
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'warehouse' ? '' : 'warehouse')" 
                            class="w-full flex items-center justify-between py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition text-slate-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Inventory
                        </div>
                        <svg class="w-3.5 h-3.5 transform transition duration-200" :class="openGroup === 'warehouse' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'warehouse'" class="pl-7 space-y-1" style="display: none;">
                        <a href="{{ route('admin.warehouse.stock') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.warehouse.stock') ? 'bg-slate-700 text-white font-bold' : '' }}">Stock Barang</a>
                        <a href="{{ route('admin.warehouse.receive') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.warehouse.receive') ? 'bg-slate-700 text-white font-bold' : '' }}">Stock Masuk</a>
                        <a href="{{ route('admin.warehouse.transfer') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.warehouse.transfer') ? 'bg-slate-700 text-white font-bold' : '' }}">Stock Keluar</a>
                        <a href="{{ route('admin.warehouse.expired') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.warehouse.expired') ? 'bg-slate-700 text-white font-bold' : '' }}">Expired Tracking</a>
                    </div>
                </div>

                <!-- Supplier Management -->
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'suppliers' ? '' : 'suppliers')" 
                            class="w-full flex items-center justify-between py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition text-slate-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Supplier Management
                        </div>
                        <svg class="w-3.5 h-3.5 transform transition duration-200" :class="openGroup === 'suppliers' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'suppliers'" class="pl-7 space-y-1" style="display: none;">
                        <a href="{{ route('admin.suppliers.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.suppliers.index') ? 'bg-slate-700 text-white font-bold' : '' }}">Data Supplier</a>
                        <a href="{{ route('admin.suppliers.po.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.suppliers.po.*') ? 'bg-slate-700 text-white font-bold' : '' }}">Purchase Orders</a>
                    </div>
                </div>

                <!-- Delivery Management -->
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'deliveries' ? '' : 'deliveries')" 
                            class="w-full flex items-center justify-between py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition text-slate-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            Delivery Management
                        </div>
                        <svg class="w-3.5 h-3.5 transform transition duration-200" :class="openGroup === 'deliveries' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'deliveries'" class="pl-7 space-y-1" style="display: none;">
                        <a href="{{ route('admin.deliveries.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.deliveries.index') ? 'bg-slate-700 text-white font-bold' : '' }}">Daftar Pengiriman</a>
                    </div>
                </div>

                <!-- Reports -->
                <a href="{{ route('admin.reports.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.reports.index') ? 'bg-slate-700 font-bold' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Analisa & Laporan
                    </div>
                </a>

                <!-- Settings -->
                <a href="{{ route('profile.edit') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition mt-8 border-t border-slate-700 pt-4 {{ request()->routeIs('profile.edit') ? 'bg-slate-700 font-bold' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Settings
                    </div>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-full overflow-hidden">
            <!-- Topbar -->
            <header class="h-16 bg-white shadow flex items-center justify-between px-6 flex-shrink-0">
                <div class="flex items-center">
                    <button class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-800 ml-4 md:ml-0">
                        @yield('header')
                    </h2>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">Admin</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>