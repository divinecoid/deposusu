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
                <!-- 1. Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </div>
                </a>

                <!-- 2. Produk (Root Level) -->
                <a href="{{ route('admin.master.products.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.master.products.*') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Produk
                    </div>
                </a>

                <!-- 3. E-Commerce Order (Original Order) -->
                <a href="{{ route('admin.orders.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.orders.*') && !request()->routeIs('admin.sales-order.*') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        Order (E-Commerce)
                    </div>
                </a>

                <!-- 4. Sales Order (Manual WhatsApp) -->
                <a href="{{ route('admin.sales-order.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.sales-order.*') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Sales Order (Manual)
                    </div>
                </a>

                <!-- 5. POS / Kasir -->
                <a href="{{ route('admin.kasir.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.kasir.*') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        POS / Kasir
                    </div>
                </a>

                <!-- 6. Invoice Management -->
                <a href="{{ route('admin.invoices.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.invoices.*') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Invoice Management
                    </div>
                </a>

                <!-- 7. Payment Management -->
                <a href="{{ route('admin.payments.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.payments.*') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Payment Management
                    </div>
                </a>

                <!-- 8. Marketplace (Original Link) -->
                <a href="#" class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 text-slate-300">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Marketplace
                    </div>
                </a>

                <!-- 9. Gudang (Original Link) -->
                <a href="{{ route('admin.warehouse.stock') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.warehouse.stock') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Gudang
                    </div>
                </a>

                <!-- 10. Inventory (Original Dropdown) -->
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

                <!-- 11. Customer (Original Customer Link) -->
                <a href="{{ route('admin.master.customers.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.master.customers.*') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Customer
                    </div>
                </a>

                <!-- 12. Live Chat / Customer Service -->
                <a href="{{ route('admin.live-chat.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.live-chat.*') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        Live Chat
                    </div>
                </a>

                <!-- 13. Finance & Accounting -->
                <a href="{{ route('admin.finance.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.finance.*') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Finance & Accounting
                    </div>
                </a>

                <!-- 14. Supplier Management -->
                <a href="{{ route('admin.suppliers.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.suppliers.*') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Supplier Management
                    </div>
                </a>

                <!-- 15. Delivery Management (Original Dropdown) -->
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

                <!-- 16. AI Generator (Original Link) -->
                <a href="#" class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 text-slate-300">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        AI Generator
                    </div>
                </a>

                <!-- 17. Reports -->
                <a href="{{ route('admin.reports.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.reports.*') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Reports
                    </div>
                </a>

                <!-- 18. Settings -->
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