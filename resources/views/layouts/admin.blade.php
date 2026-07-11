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
    <style>
        @media print {
            aside, header, .no-print { display: none !important; }
            body, .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; background: white !important; }
            .h-screen { height: auto !important; overflow: visible !important; }
            .flex-1 { display: block !important; }
            .overflow-y-auto { overflow: visible !important; }
            .p-8 { padding: 0 !important; }
            .max-w-7xl { max-width: none !important; }
            .shadow-sm { box-shadow: none !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased h-screen overflow-hidden print:h-auto print:overflow-visible print:bg-white">
    <div class="h-screen flex overflow-hidden print:h-auto print:overflow-visible print:block">
        <!-- Sidebar -->
        <aside class="print:hidden w-64 bg-slate-800 text-white flex-shrink-0 hidden md:flex flex-col h-full">
            <div class="h-16 flex items-center justify-center border-b border-slate-700 flex-shrink-0">
                <span class="text-xl font-bold">DEPOSUSU</span>
            </div>

            <nav class="mt-4 px-3 space-y-1.5 overflow-y-auto flex-1 pb-4 no-print" x-data="{ openGroup: '{{ request()->segment(2) }}' }">
                <!-- 1. Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </div>
                </a>

                <!-- 2. Order Management -->
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
                        <a href="{{ route('admin.orders.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.orders.*') && !request()->routeIs('admin.sales-order.*') ? 'bg-slate-700 text-white font-bold' : '' }}">Order (E-Commerce)</a>
                        <a href="{{ route('admin.sales-order.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.sales-order.*') ? 'bg-slate-700 text-white font-bold' : '' }}">Sales Order (Manual)</a>
                        <a href="{{ route('admin.kasir.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.kasir.*') ? 'bg-slate-700 text-white font-bold' : '' }}">POS / Kasir</a>
                    </div>
                </div>

                <!-- 3. Finance -->
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'finance' ? '' : 'finance')" 
                            class="w-full flex items-center justify-between py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition text-slate-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Finance
                        </div>
                        <svg class="w-3.5 h-3.5 transform transition duration-200" :class="openGroup === 'finance' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'finance'" class="pl-7 space-y-1" style="display: none;">
                        <a href="{{ route('admin.invoices.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.invoices.*') ? 'bg-slate-700 text-white font-bold' : '' }}">Invoice</a>
                        <a href="{{ route('admin.payments.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.payments.*') ? 'bg-slate-700 text-white font-bold' : '' }}">Payment</a>
                        <a href="{{ route('admin.kuitansi.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.kuitansi.*') ? 'bg-slate-700 text-white font-bold' : '' }}">Kuitansi</a>
                        <a href="{{ route('admin.finance.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.finance.*') ? 'bg-slate-700 text-white font-bold' : '' }}">Expense & Accounting</a>
                    </div>
                </div>

                <!-- 4. Inventory -->
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

                <!-- 5. Delivery -->
                <a href="{{ route('admin.deliveries.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.deliveries.*') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Delivery Management
                    </div>
                </a>

                <!-- 6. Master Data -->
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'master' ? '' : 'master')" 
                            class="w-full flex items-center justify-between py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition text-slate-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                            Master Data
                        </div>
                        <svg class="w-3.5 h-3.5 transform transition duration-200" :class="openGroup === 'master' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'master'" class="pl-7 space-y-1" style="display: none;">
                        <a href="{{ route('admin.master.products.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.master.products.*') ? 'bg-slate-700 text-white font-bold' : '' }}">Produk</a>
                        <a href="{{ route('admin.master.customers.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.master.customers.*') ? 'bg-slate-700 text-white font-bold' : '' }}">Customer</a>
                        <a href="{{ route('admin.suppliers.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.suppliers.*') ? 'bg-slate-700 text-white font-bold' : '' }}">Supplier</a>
                        <a href="{{ route('admin.master.branches.index') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.master.branches.*') ? 'bg-slate-700 text-white font-bold' : '' }}">Cabang & Area</a>
                    </div>
                </div>

                <!-- Marketplace Integration -->
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'marketplace' ? '' : 'marketplace')" 
                            class="w-full flex items-center justify-between py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition text-slate-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            Marketplace
                        </div>
                        <svg class="w-3.5 h-3.5 transform transition duration-200" :class="openGroup === 'marketplace' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openGroup === 'marketplace'" class="pl-7 space-y-1" style="display: none;">
                        <a href="{{ route('admin.marketplace.connection') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.marketplace.connection*') ? 'bg-slate-700 text-white font-bold' : '' }}">Connection</a>
                        <a href="{{ route('admin.marketplace.sync.products') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.marketplace.sync.products*') ? 'bg-slate-700 text-white font-bold' : '' }}">Product Sync</a>
                        <a href="{{ route('admin.marketplace.orders') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.marketplace.orders*') ? 'bg-slate-700 text-white font-bold' : '' }}">Order Sync</a>
                        <a href="{{ route('admin.marketplace.sync.inventory') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.marketplace.sync.inventory*') ? 'bg-slate-700 text-white font-bold' : '' }}">Inventory Sync</a>
                        <a href="{{ route('admin.marketplace.customers') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.marketplace.customers*') ? 'bg-slate-700 text-white font-bold' : '' }}">Marketplace Customer</a>
                        <a href="{{ route('admin.marketplace.payments') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.marketplace.payments*') ? 'bg-slate-700 text-white font-bold' : '' }}">Marketplace Payment</a>
                        <a href="{{ route('admin.marketplace.reports') }}" class="block py-1.5 px-2 rounded-md text-xs hover:bg-slate-700 text-slate-400 hover:text-white {{ request()->routeIs('admin.marketplace.reports*') ? 'bg-slate-700 text-white font-bold' : '' }}">Marketplace Report</a>
                    </div>
                </div>

                <!-- 7. Live Chat -->
                <a href="{{ route('admin.live-chat.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.live-chat.*') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        Live Chat
                    </div>
                </a>

                <!-- 8. Reports & Analytics -->
                <a href="{{ route('admin.reports.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.reports.*') ? 'bg-slate-700 font-bold text-white' : 'text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Reports & Analytics
                    </div>
                </a>

                <!-- 9. Employee Performance -->
                @if(auth()->check() && auth()->user()->isAdmin())
                <a href="{{ route('admin.performance.index') }}"
                    class="block py-2 px-3 rounded-lg text-sm hover:bg-slate-700 transition {{ request()->routeIs('admin.performance.*') ? 'bg-slate-700 font-bold text-white' : 'text-amber-500' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                        Employee Performance
                    </div>
                </a>
                @endif

                <!-- 10. Settings -->
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
            <header class="print:hidden h-16 bg-white shadow flex items-center justify-between px-6 flex-shrink-0">
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