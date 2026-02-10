<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DEPOSUSU - Mengantar kebaikan, sepenuh hati</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
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
                <div class="hidden md:block flex-1 max-w-2xl mx-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="product-search" onkeyup="searchProducts()"
                            class="block w-full pl-10 pr-3 py-2 border border-blue-500 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 sm:text-sm"
                            placeholder="Cari Produk...">
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

                    <!-- Cart (Always Visible) -->
                    <a href="{{ route('cart.index') }}" class="p-2 text-gray-500 hover:text-blue-500 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <!-- Cart Badge -->
                        <span
                            class="cart-badge absolute top-0 right-0 bg-blue-600 text-white text-xs font-bold rounded-full w-4 h-4 flex items-center justify-center"
                            style="display: none;">0</span>
                    </a>

                    <!-- Desktop Icons -->
                    <div class="hidden md:flex items-center gap-4 text-gray-500">
                        <!-- Heart -->
                        <button class="hover:text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                        <!-- List -->
                        <button class="hover:text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </button>
                        <!-- Chat -->
                        <button class="hover:text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </button>
                    </div>

                    <div class="h-6 w-px bg-gray-300 hidden md:block"></div>

                    <!-- Auth Buttons (Desktop) -->
                    <div class="hidden md:flex items-center gap-4">
                        @auth
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-medium text-gray-700">Halo, <span
                                        class="text-blue-600 font-bold">{{ Auth::user()->name }}</span></span>
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
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" id="mobile-product-search" onkeyup="searchProducts('mobile')"
                class="block w-full pl-10 pr-3 py-2 border border-blue-500 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 sm:text-sm"
                placeholder="Cari Produk...">
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

        // Call on page load
        document.addEventListener('DOMContentLoaded', updateCartBadge);
    </script>

</body>

</html>