<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" translate="no" class="notranslate" @stack('html_attr')>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google" content="notranslate">
    <title>DEPOSUSU - Mengantar kebaikan, sepenuh hati</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    @stack('head')
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50">

    <!-- Top Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center gap-4">

                <!-- Logo & Categories -->
                <div class="flex items-center gap-4 md:gap-6">
                    <!-- Mobile Menu Button -->
                    <button onclick="toggleMobileMenu()" class="md:hidden text-gray-500 hover:text-blue-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                        <span class="text-xl md:text-2xl font-bold text-blue-500">DEPOSUSU</span>
                    </a>
                </div>

                <!-- Search Bar (Desktop) -->
                <div class="hidden md:block flex-1 max-w-2xl mx-4 relative" id="desktop-search-container">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="product-search" onkeyup="handleSearchInput(this.value, 'desktop')" onfocus="handleSearchInput(this.value, 'desktop')"
                            class="block w-full pl-10 pr-3 py-2 border border-blue-500 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 sm:text-sm"
                            placeholder="Cari Produk...">
                    </div>
                    <!-- Suggestions Dropdown -->
                    <div id="desktop-suggestions" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-50">
                        <ul id="desktop-suggestions-list" class="max-h-80 overflow-y-auto py-2"></ul>
                    </div>
                </div>

                <!-- Icons & Auth -->
                <div class="flex items-center gap-2 md:gap-4">
                    <!-- Mobile Search Icon -->
                    <button onclick="toggleMobileSearch()" class="md:hidden p-2 text-gray-500 hover:text-blue-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                    <!-- Wishlist link -->
                    <a href="{{ route('wishlist.index') }}" class="p-2 text-gray-500 hover:text-blue-500 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <!-- Wishlist Badge -->
                        <span
                            class="wishlist-badge absolute top-0 right-0 bg-red-600 text-white text-xs font-bold rounded-full w-4 h-4 flex items-center justify-center"
                            style="display: none;">0</span>
                    </a>

                    <!-- Cart (Always Visible) -->
                    <a href="{{ route('cart.index') }}" class="p-2 text-gray-500 hover:text-blue-500 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <!-- Cart Badge -->
                        <span
                            class="cart-badge absolute top-0 right-0 bg-blue-600 text-white text-xs font-bold rounded-full w-4 h-4 flex items-center justify-center"
                            style="display: none;">0</span>
                    </a>
                    @auth
                        <a href="{{ route('transactions.index') }}"
                            class="hidden md:inline-flex p-2 text-gray-500 hover:text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2h6v2m-7 4h8a2 2 0 002-2V7a2 2 0 00-2-2h-3V3H9v2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </a>
                    @endauth

                    <div class="h-6 w-px bg-gray-300 hidden md:block"></div>

                    <!-- Auth Buttons (Desktop) -->
                    <div class="hidden md:flex items-center gap-4">
                        @auth
                            <div class="flex items-center gap-4">
                                <a href="{{ route('account.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">
                                    Halo, <span class="font-bold">{{ Auth::user()->name }}</span>
                                </a>
                                <a href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="px-4 py-2 border border-red-500 text-red-500 rounded-xl hover:bg-red-50 font-bold text-xs transition-all uppercase tracking-wider">Keluar</a>
                            </div>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 border border-blue-500 text-blue-500 rounded-xl hover:bg-blue-50 font-bold text-sm transition-all">Masuk</a>
                            <a href="{{ route('register') }}"
                                class="px-4 py-2 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/10">Daftar</a>
                        @endauth
                    </div>

                    <!-- Mobile User/Logout Icon -->
                    @auth
                        <a href="{{ route('transactions.index') }}" class="md:hidden p-2 text-gray-500 hover:text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2h6v2m-7 4h8a2 2 0 002-2V7a2 2 0 00-2-2h-3V3H9v2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </a>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="md:hidden p-2 text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="md:hidden p-2 text-gray-500 hover:text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Search Bar -->
    <div id="mobile-search-bar"
        class="hidden md:hidden bg-white border-b border-gray-100 p-4 sticky top-16 left-0 w-full z-45 animate-slide-in shadow-sm">
        <div class="relative" id="mobile-search-container">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="mobile-product-search" onkeyup="handleSearchInput(this.value, 'mobile')" onfocus="handleSearchInput(this.value, 'mobile')"
                    class="block w-full pl-10 pr-3 py-2 border border-blue-500 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 sm:text-sm"
                    placeholder="Cari Produk...">
            </div>
            <!-- Suggestions Dropdown -->
            <div id="mobile-suggestions" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-50">
                <ul id="mobile-suggestions-list" class="max-h-80 overflow-y-auto py-2"></ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Mobile Menu Drawer -->
    <div id="mobile-menu" class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="toggleMobileMenu()"></div>

        <!-- Drawer -->
        <div class="fixed inset-y-0 left-0 w-full max-w-xs bg-white shadow-xl transform transition-transform duration-300 -translate-x-full"
            id="mobile-menu-drawer">
            <div class="flex flex-col h-full">
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <span class="text-xl font-bold text-blue-600">DEPOSUSU</span>
                    <button onclick="toggleMobileMenu()" class="text-gray-400 hover:text-gray-600 p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto py-6 px-6">
                    <div class="space-y-8">
                        <!-- Navigation -->
                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Navigasi</h3>
                            <div class="space-y-4">
                                <a href="{{ route('home') }}"
                                    class="flex items-center gap-3 text-gray-700 hover:text-blue-600 font-medium transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Beranda
                                </a>
                                <a href="{{ route('cart.index') }}"
                                    class="flex items-center gap-3 text-gray-700 hover:text-blue-600 font-medium transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Keranjang
                                </a>
                                <a href="{{ route('wishlist.index') }}"
                                    class="flex items-center gap-3 text-gray-700 hover:text-blue-600 font-medium transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    Wishlist
                                </a>
                                @auth
                                    <a href="{{ route('transactions.index') }}"
                                        class="flex items-center gap-3 text-gray-700 hover:text-blue-600 font-medium transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 17v-2h6v2m-7 4h8a2 2 0 002-2V7a2 2 0 00-2-2h-3V3H9v2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Transaksi
                                    </a>
                                    <a href="{{ route('account.index') }}"
                                        class="flex items-center gap-3 text-gray-700 hover:text-blue-600 font-medium transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Akun Saya
                                    </a>
                                @endauth
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Footer -->
                <div class="p-6 border-t border-gray-100 bg-gray-50">
                    @auth
                        <div class="flex flex-col gap-4">
                            <div
                                class="flex items-center gap-3 px-4 py-3 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                <div
                                    class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                                    {{ Auth::user()->initials() }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs text-gray-400 font-medium">Selamat datang,</span>
                                    <span
                                        class="text-sm font-bold text-gray-800 leading-none">{{ Auth::user()->name }}</span>
                                </div>
                            </div>
                            <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="w-full py-3 bg-red-50 text-red-600 rounded-2xl font-bold text-sm hover:bg-red-100 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Keluar dari Akun
                            </button>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('login') }}"
                                class="flex items-center justify-center px-4 py-2 border border-blue-600 text-blue-600 rounded-xl font-bold text-sm hover:bg-blue-50 transition-all">Masuk</a>
                            <a href="{{ route('register') }}"
                                class="flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all">Daftar</a>
                        </div>
                    @endauth
                </div>

                <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>

    <!-- Product Quick View Modal -->
    <div id="quick-view-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" onclick="closeQuickView()">
            </div>

            <!-- Modal Content -->
            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full animate-fade-in-up relative">
                <button onclick="closeQuickView()"
                    class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 z-10 p-2 hover:bg-gray-100 rounded-full transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="grid md:grid-cols-2">
                    <!-- Image Section -->
                    <div
                        class="bg-gradient-to-br from-blue-50 to-blue-100 p-8 flex items-center justify-center min-h-[400px]">
                        <div id="qv-image-container"
                            class="image-container skeleton w-full aspect-square rounded-2xl overflow-hidden shadow-inner bg-white">
                            <img id="qv-image" src="" alt=""
                                class="w-full h-full object-contain transform hover:scale-105 transition-transform duration-500"
                                onload="this.classList.add('loaded'); this.parentElement.classList.remove('skeleton');"
                                onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image'; this.classList.add('loaded'); this.parentElement.classList.remove('skeleton');">
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="p-8 md:p-12 flex flex-col justify-center">
                        <span id="qv-category"
                            class="inline-block px-3 py-1 bg-blue-100 text-blue-600 text-sm font-semibold rounded-full mb-4 w-fit"></span>
                        <h2 id="qv-name" class="text-3xl md:text-4xl font-bold text-gray-900 mb-4"></h2>



                        <p id="qv-description" class="text-gray-600 text-lg mb-8 leading-relaxed"></p>

                        <div class="mb-8">
                            <div id="qv-discount-wrapper" class="hidden flex items-center gap-3 mb-2">
                                <span id="qv-old-price" class="text-xl text-gray-400 line-through"></span>
                                <span id="qv-discount-badge"
                                    class="px-3 py-1 bg-red-100 text-red-600 text-sm font-bold rounded-lg animate-pulse"></span>
                            </div>
                            <p id="qv-price" class="text-4xl font-bold text-blue-600"></p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <button id="qv-add-btn" onclick=""
                                class="flex-1 px-8 py-4.5 bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400 text-white rounded-2xl font-bold text-lg shadow-xl shadow-blue-600/20 hover:shadow-blue-600/40 transform hover:scale-[1.02] active:scale-95 transition-all duration-300 flex items-center justify-center gap-3 border border-blue-400/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile Menu toggle
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileDrawer = document.getElementById('mobile-menu-drawer');

        function toggleMobileMenu() {
            const isHidden = mobileMenu.classList.contains('hidden');

            if (isHidden) {
                mobileMenu.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    mobileDrawer.classList.remove('-translate-x-full');
                }, 10);
            } else {
                mobileDrawer.classList.add('-translate-x-full');
                document.body.style.overflow = '';
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                }, 300);
            }
        }

        function toggleMobileSearch() {
            const searchBar = document.getElementById('mobile-search-bar');
            const categoryNav = document.getElementById('category-navigation');

            searchBar.classList.toggle('hidden');

            if (!searchBar.classList.contains('hidden')) {
                document.getElementById('mobile-product-search').focus();
                if (categoryNav) {
                    categoryNav.style.top = '136px'; // 64px (header) + 72px (search bar)
                }
            } else {
                if (categoryNav) {
                    categoryNav.style.top = '64px'; // Back to header height
                }
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-24 right-4 px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white font-semibold`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Update cart badge on page load
        async function updateCartBadge() {
            try {
                const response = await fetch('/cart/data');
                const data = await response.json();

                if (data.success) {
                    const badge = document.querySelector('.cart-badge');
                    if (badge) {
                        badge.textContent = data.cart.total_items;
                        badge.style.display = data.cart.total_items > 0 ? 'flex' : 'none';
                    }
                }
            } catch (error) {
                console.error('Error updating cart badge:', error);
            }
        }

        // Add to cart function
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function addToCart(productId) {
            const event = window.event;
            const button = event ? event.currentTarget : null;

            let originalContent = '';
            if (button) {
                originalContent = button.innerHTML;
                button.innerHTML = '<svg class="w-5 h-5 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                button.disabled = true;
            }

            try {
                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1
                    })
                });

                const data = await response.json();

                if (data.success) {
                    if (button) button.innerHTML = '<svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                    updateCartBadge();
                    showNotification(data.message, 'success');
                    if (button) {
                        setTimeout(() => {
                            button.innerHTML = originalContent;
                            button.disabled = false;
                        }, 1000);
                    }
                } else {
                    showNotification(data.message, 'error');
                    if (button) {
                        button.innerHTML = originalContent;
                        button.disabled = false;
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
                if (button) {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }
            }
        }

        // Quick View Modal functions
        const qvModal = document.getElementById('quick-view-modal');
        const qvImage = document.getElementById('qv-image');
        const qvImageContainer = document.getElementById('qv-image-container');
        const qvName = document.getElementById('qv-name');
        const qvCategory = document.getElementById('qv-category');
        const qvDescription = document.getElementById('qv-description');
        const qvPrice = document.getElementById('qv-price');
        const qvOldPrice = document.getElementById('qv-old-price');
        const qvDiscountBadge = document.getElementById('qv-discount-badge');
        const qvDiscountWrapper = document.getElementById('qv-discount-wrapper');
        const qvAddBtn = document.getElementById('qv-add-btn');

        function openQuickView(product) {
            if (!qvModal) return;
            qvImage.classList.remove('loaded');
            qvImageContainer.classList.add('skeleton');
            const imagePath = product.image && product.image.startsWith('storage/') ? `/storage/${product.image.replace('storage/', '')}` : product.image;
            qvImage.src = imagePath;
            qvImage.alt = product.name;
            qvName.textContent = product.name;
            qvCategory.textContent = product.categories && product.categories.length > 0
                ? product.categories.map(c => c.name).join(', ')
                : (product.category ? product.category.name : 'Susu Segar');
            qvDescription.textContent = product.description;
            const price = parseFloat(product.price);
            let finalPrice = price;
            if (product.active_discount) {
                const discount = product.active_discount;
                qvDiscountWrapper.classList.remove('hidden');
                qvOldPrice.textContent = `Rp ${price.toLocaleString('id-ID')}`;
                if (discount.discount_type === 'PERCENTAGE') {
                    finalPrice = price * (1 - (discount.discount_value / 100));
                    qvDiscountBadge.textContent = `-${discount.discount_value}% OFF`;
                } else {
                    finalPrice = Math.max(0, price - discount.discount_value);
                    const formattedDiscount = discount.discount_value >= 1000 ? (discount.discount_value / 1000) + 'K' : discount.discount_value.toLocaleString('id-ID');
                    qvDiscountBadge.textContent = `Hemat Rp ${formattedDiscount}`;
                }
            } else if (product.discounted_price && parseFloat(product.discounted_price) < price) {
                qvDiscountWrapper.classList.remove('hidden');
                qvOldPrice.textContent = `Rp ${price.toLocaleString('id-ID')}`;
                finalPrice = parseFloat(product.discounted_price);
                const saving = price - finalPrice;
                qvDiscountBadge.textContent = `Hemat Rp ${saving.toLocaleString('id-ID')}`;
            } else {
                qvDiscountWrapper.classList.add('hidden');
            }
            qvPrice.textContent = `Rp ${finalPrice.toLocaleString('id-ID')}`;
            qvAddBtn.setAttribute('onclick', `addToCart(${product.id})`);
            qvModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeQuickView() {
            if (qvModal) {
                qvModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && qvModal && !qvModal.classList.contains('hidden')) {
                closeQuickView();
            }
        });

        async function toggleWishlist(productId, button) {
            try {
                const response = await fetch('{{ route('wishlist.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ product_id: productId })
                });
                if (response.status === 401) {
                    window.location.href = '{{ route('login') }}';
                    return;
                }
                const data = await response.json();
                if (data.success) {
                    const svg = button.querySelector('svg');
                    if (data.status === 'added') {
                        svg.classList.remove('text-gray-400');
                        svg.classList.add('text-red-500');
                        svg.setAttribute('fill', 'currentColor');
                    } else {
                        svg.classList.add('text-gray-400');
                        svg.classList.remove('text-red-500');
                        svg.setAttribute('fill', 'none');
                        if (window.location.pathname.includes('/wishlist')) {
                            const card = button.closest('.product-card');
                            if (card) {
                                card.style.opacity = '0';
                                card.style.transform = 'scale(0.9)';
                                setTimeout(() => {
                                    card.remove();
                                    if (document.querySelectorAll('.product-card').length === 0) location.reload();
                                }, 300);
                            }
                        }
                    }
                    showNotification(data.message, 'success');
                }
            } catch (error) {
                console.error('Error toggling wishlist:', error);
            }
        }

        // Call on page load
        document.addEventListener('DOMContentLoaded', updateCartBadge);

        // --- Auto Suggestions Logic ---
        let suggestionTimeout = null;

        function handleSearchInput(query, platform) {
            // Also trigger the existing searchProducts on home page if we are there
            if (typeof searchProducts === 'function' && window.location.pathname === '/') {
                searchProducts(platform);
            }

            const dropdownId = platform === 'desktop' ? 'desktop-suggestions' : 'mobile-suggestions';
            const listId = platform === 'desktop' ? 'desktop-suggestions-list' : 'mobile-suggestions-list';
            const dropdown = document.getElementById(dropdownId);
            const list = document.getElementById(listId);

            if (!query || query.trim() === '') {
                dropdown.classList.add('hidden');
                return;
            }

            if (suggestionTimeout) clearTimeout(suggestionTimeout);

            suggestionTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/products/suggest?q=${encodeURIComponent(query)}`);
                    const results = await response.json();

                    list.innerHTML = '';
                    if (results.length > 0) {
                        results.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'hover:bg-blue-50 transition-colors cursor-pointer border-b border-gray-50 last:border-0';
                            
                            // Navigate to product page
                            li.onclick = () => {
                                dropdown.classList.add('hidden');
                                window.location.href = `/products/${item.id}`;
                            };

                            let priceHtml = '';
                            if (item.has_discount) {
                                priceHtml = `
                                    <span class="text-xs text-gray-400 line-through">${item.original_price_formatted}</span>
                                    <span class="text-sm font-bold text-blue-600">${item.price_formatted}</span>
                                `;
                            } else {
                                priceHtml = `<span class="text-sm font-bold text-blue-600">${item.price_formatted}</span>`;
                            }

                            li.innerHTML = `
                                <div class="flex items-center gap-3 px-4 py-2">
                                    <img src="${item.image}" alt="${item.name}" class="w-10 h-10 object-contain rounded bg-gray-50 flex-shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-gray-900 truncate">${item.name}</h4>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            ${priceHtml}
                                        </div>
                                    </div>
                                </div>
                            `;
                            list.appendChild(li);
                        });
                        dropdown.classList.remove('hidden');
                    } else {
                        list.innerHTML = `
                            <li class="px-4 py-3 text-sm text-gray-500 text-center">
                                Produk tidak ditemukan.
                            </li>
                        `;
                        dropdown.classList.remove('hidden');
                    }
                } catch (err) {
                    console.error('Error fetching suggestions:', err);
                }
            }, 300); // 300ms debounce
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const desktopContainer = document.getElementById('desktop-search-container');
            const mobileContainer = document.getElementById('mobile-search-container');
            
            if (desktopContainer && !desktopContainer.contains(e.target)) {
                const dd = document.getElementById('desktop-suggestions');
                if (dd) dd.classList.add('hidden');
            }
            if (mobileContainer && !mobileContainer.contains(e.target)) {
                const md = document.getElementById('mobile-suggestions');
                if (md) md.classList.add('hidden');
            }
        });

    </script>

</body>

</html>