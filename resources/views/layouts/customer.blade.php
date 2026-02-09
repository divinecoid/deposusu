<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DEPOSUSU - Toko Susu Terbaik</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50">

    <!-- Top Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center gap-4">

                <!-- Logo & Categories -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                        <span class="text-2xl font-bold text-blue-500">DEPOSUSU</span>
                    </a>
                    <a href="#" class="text-blue-500 hover:text-blue-700 font-medium text-sm hidden md:block">
                        Kategori
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="flex-1 max-w-2xl mx-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text"
                            class="block w-full pl-10 pr-3 py-2 border border-blue-500 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 sm:text-sm"
                            placeholder="Cari Greenfields">
                    </div>
                </div>

                <!-- Icons & Auth -->
                <div class="flex items-center gap-4">
                    <!-- Icons (Hidden on mobile for space) -->
                    <div class="hidden md:flex items-center gap-4 text-blue-500">
                        <!-- Heart -->
                        <button class="hover:text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                        <!-- Cart -->
                        <a href="{{ route('cart.index') }}" class="hover:text-blue-700 relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <!-- Cart Badge -->
                            <span class="cart-badge absolute -top-2 -right-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center" 
                                  style="display: none;">0</span>
                        </a>
                        <!-- List -->
                        <button class="hover:text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </button>
                        <!-- Chat -->
                        <button class="hover:text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </button>
                        <!-- User -->
                        <button class="hover:text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>

                    <div class="h-6 w-px bg-gray-300 hidden md:block"></div>

                    <!-- Buttons -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}"
                            class="px-4 py-2 border border-blue-500 text-blue-500 rounded hover:bg-blue-50 font-medium text-sm">Masuk</a>
                        <a href="{{ route('register') }}"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 font-medium text-sm">Daftar</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <script>
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