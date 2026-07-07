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

            <nav class="mt-4 px-4 space-y-2 overflow-y-auto flex-1 pb-4" x-data="{ 
                openGroup: '{{ request()->routeIs('admin.master.*') ? 'master' : (request()->routeIs('admin.orders.*') || request()->routeIs('admin.invoices.*') ? 'transaction' : (request()->routeIs('admin.warehouse.*') || request()->routeIs('admin.stock.*') ? 'wms' : '')) }}' 
            }">
                <!-- Dashboard Link -->
                <a href="{{ route('admin.dashboard') }}"
                    class="block py-2.5 px-4 rounded hover:bg-slate-700 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </div>
                </a>

                <!-- Group 1: Master Data -->
                <div>
                    <button @click="openGroup = openGroup === 'master' ? '' : 'master'" 
                        class="w-full flex items-center justify-between py-2.5 px-4 rounded hover:bg-slate-700 text-slate-300 focus:outline-none"
                        :class="openGroup === 'master' ? 'bg-slate-700/50' : ''">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                            <span class="font-medium text-sm">Master Data</span>
                        </div>
                        <svg class="w-4 h-4 transform transition-transform" :class="openGroup === 'master' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'master'" x-collapse class="pl-8 pr-2 py-1 space-y-1 bg-slate-900/30 rounded-b">
                        <a href="{{ route('admin.master.products.index') }}"
                            class="block py-2 px-3 text-sm rounded hover:text-white transition-colors {{ request()->routeIs('admin.master.products.*') ? 'text-white font-semibold' : 'text-slate-400' }}">
                            Produk
                        </a>
                        <a href="{{ route('admin.master.warehouses.index') }}"
                            class="block py-2 px-3 text-sm rounded hover:text-white transition-colors {{ request()->routeIs('admin.master.warehouses.index') ? 'text-white font-semibold' : 'text-slate-400' }}">
                            Gudang & Rak
                        </a>
                        <a href="{{ route('admin.master.customers.index') }}"
                            class="block py-2 px-3 text-sm rounded hover:text-white transition-colors {{ request()->routeIs('admin.master.customers.*') ? 'text-white font-semibold' : 'text-slate-400' }}">
                            Customer
                        </a>
                    </div>
                </div>

                <!-- Group 2: Transaksi -->
                <div>
                    <button @click="openGroup = openGroup === 'transaction' ? '' : 'transaction'" 
                        class="w-full flex items-center justify-between py-2.5 px-4 rounded hover:bg-slate-700 text-slate-300 focus:outline-none"
                        :class="openGroup === 'transaction' ? 'bg-slate-700/50' : ''">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            <span class="font-medium text-sm">Transaksi</span>
                        </div>
                        <svg class="w-4 h-4 transform transition-transform" :class="openGroup === 'transaction' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'transaction'" x-collapse class="pl-8 pr-2 py-1 space-y-1 bg-slate-900/30 rounded-b">
                        <a href="{{ route('admin.orders.index') }}"
                            class="block py-2 px-3 text-sm rounded hover:text-white transition-colors {{ request()->routeIs('admin.orders.*') ? 'text-white font-semibold' : 'text-slate-400' }}">
                            Order Pelanggan
                        </a>
                        <a href="{{ route('admin.invoices.index') }}"
                            class="block py-2 px-3 text-sm rounded hover:text-white transition-colors {{ request()->routeIs('admin.invoices.*') ? 'text-white font-semibold' : 'text-slate-400' }}">
                            Invoice & Pembayaran
                        </a>
                    </div>
                </div>

                <!-- Group 3: WMS & Logistik -->
                <div>
                    <button @click="openGroup = openGroup === 'wms' ? '' : 'wms'" 
                        class="w-full flex items-center justify-between py-2.5 px-4 rounded hover:bg-slate-700 text-slate-300 focus:outline-none"
                        :class="openGroup === 'wms' ? 'bg-slate-700/50' : ''">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <span class="font-medium text-sm">WMS & Logistik</span>
                        </div>
                        <svg class="w-4 h-4 transform transition-transform" :class="openGroup === 'wms' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'wms'" x-collapse class="pl-8 pr-2 py-1 space-y-1 bg-slate-900/30 rounded-b">
                        <a href="{{ route('admin.warehouse.stock') }}"
                            class="block py-2 px-3 text-sm rounded hover:text-white transition-colors {{ request()->routeIs('admin.warehouse.stock') ? 'text-white font-semibold' : 'text-slate-400' }}">
                            Stok Gudang
                        </a>
                        <a href="{{ route('admin.warehouse.receive') }}"
                            class="block py-2 px-3 text-sm rounded hover:text-white transition-colors {{ request()->routeIs('admin.warehouse.receive') ? 'text-white font-semibold' : 'text-slate-400' }}">
                            Receive Barang
                        </a>
                        <a href="{{ route('admin.warehouse.transfer') }}"
                            class="block py-2 px-3 text-sm rounded hover:text-white transition-colors {{ request()->routeIs('admin.warehouse.transfer') ? 'text-white font-semibold' : 'text-slate-400' }}">
                            Transfer Antar Gudang
                        </a>
                        <a href="{{ route('admin.warehouse.movements') }}"
                            class="block py-2 px-3 text-sm rounded hover:text-white transition-colors {{ request()->routeIs('admin.warehouse.movements') ? 'text-white font-semibold' : 'text-slate-400' }}">
                            Mutasi Stok
                        </a>
                        <a href="{{ route('admin.stock.index') }}"
                            class="block py-2 px-3 text-sm rounded hover:text-white transition-colors {{ request()->routeIs('admin.stock.index') ? 'text-white font-semibold' : 'text-slate-400' }}">
                            Stock Opname
                        </a>
                    </div>
                </div>

                <!-- Marketplace / Extra Links -->
                <a href="#" class="block py-2.5 px-4 rounded hover:bg-slate-700 text-slate-300">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Marketplace
                    </div>
                </a>

                <a href="#" class="block py-2.5 px-4 rounded hover:bg-slate-700 text-slate-300">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Kasir
                    </div>
                </a>

                <a href="#" class="block py-2.5 px-4 rounded hover:bg-slate-700 text-slate-300">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Laporan
                    </div>
                </a>

                <a href="#" class="block py-2.5 px-4 rounded hover:bg-slate-700 text-slate-300 mt-8 border-t border-slate-700 pt-4">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Pengaturan
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