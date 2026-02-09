<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - DEPOSUSU</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-800 text-white flex-shrink-0 hidden md:block">
            <div class="h-16 flex items-center justify-center border-b border-slate-700">
                <span class="text-xl font-bold">DEPOSUSU</span>
            </div>

            <nav class="mt-4 px-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}"
                    class="block py-2.5 px-4 rounded hover:bg-slate-700 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700' : '' }}">
                    Dashboard
                </a>

                <div class="pt-4 pb-2">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Master Data</p>
                </div>
                <a href="{{ route('admin.master.categories.index') }}"
                    class="block py-2.5 px-4 rounded hover:bg-slate-700 {{ request()->routeIs('admin.master.categories.*') ? 'bg-slate-700' : '' }}">Kategori</a>
                <a href="{{ route('admin.master.products.index') }}"
                    class="block py-2.5 px-4 rounded hover:bg-slate-700 {{ request()->routeIs('admin.master.products.*') ? 'bg-slate-700' : '' }}">Produk</a>
                <a href="{{ route('admin.master.warehouses.index') }}"
                    class="block py-2.5 px-4 rounded hover:bg-slate-700 {{ request()->routeIs('admin.master.warehouses.*') ? 'bg-slate-700' : '' }}">Gudang
                    & Rak</a>
                <a href="{{ route('admin.master.customers.index') }}"
                    class="block py-2.5 px-4 rounded hover:bg-slate-700 {{ request()->routeIs('admin.master.customers.*') ? 'bg-slate-700' : '' }}">Customer</a>
                <a href="{{ route('admin.master.drivers.index') }}"
                    class="block py-2.5 px-4 rounded hover:bg-slate-700 {{ request()->routeIs('admin.master.drivers.*') ? 'bg-slate-700' : '' }}">Driver</a>

                <div class="pt-4 pb-2">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Transaksi</p>
                </div>
                <a href="{{ route('admin.orders.index') }}"
                    class="block py-2.5 px-4 rounded hover:bg-slate-700 {{ request()->routeIs('admin.orders.*') ? 'bg-slate-700' : '' }}">Orderan
                    Masuk</a>
                <a href="{{ route('admin.invoices.index') }}"
                    class="block py-2.5 px-4 rounded hover:bg-slate-700 {{ request()->routeIs('admin.invoices.*') ? 'bg-slate-700' : '' }}">Invoices</a>
                <a href="{{ route('admin.stock.index') }}"
                    class="block py-2.5 px-4 rounded hover:bg-slate-700 {{ request()->routeIs('admin.stock.*') ? 'bg-slate-700' : '' }}">Stock
                    Opname</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Topbar -->
            <header class="h-16 bg-white shadow flex items-center justify-between px-6">
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
                    <!-- You might want to add logout here -->
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