<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" translate="no" class="notranslate" @stack('html_attr')>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google" content="notranslate">
    <meta name="theme-color" content="#2563eb">
    <title>@yield('title', 'DEPOSUSU - Mengantar kebaikan, sepenuh hati')</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://unpkg.com">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-brand-50:  #eff6ff;
            --color-brand-100: #dbeafe;
            --color-brand-200: #bfdbfe;
            --color-brand-500: #3b82f6;
            --color-brand-600: #2563eb;
            --color-brand-700: #1d4ed8;
        }
    </style>
    <style>
        /* ---- Base ---- */
        html { scroll-behavior: smooth; }
        body { -webkit-tap-highlight-color: transparent; }

        /* Respect users who prefer less motion */
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* ---- Utilities shared by every customer page ---- */
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        .skeleton {
            background: #f1f5f9;
            background-image: linear-gradient(90deg, #f1f5f9 0%, #e2e8f0 20%, #f1f5f9 40%, #f1f5f9 100%);
            background-repeat: no-repeat;
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite linear;
        }

        @keyframes fadeInUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }
        .animate-fade-in-up { animation: fadeInUp .45s cubic-bezier(.16,1,.3,1) forwards; }

        /* Visible keyboard focus everywhere */
        a:focus-visible, button:focus-visible, input:focus-visible,
        select:focus-visible, textarea:focus-visible, [tabindex]:focus-visible {
            outline: 2px solid #2563eb;
            outline-offset: 2px;
            border-radius: 6px;
        }

        /* Skip link */
        .skip-link {
            position: absolute; left: 1rem; top: -3rem; z-index: 100;
            background: #2563eb; color: #fff; padding: .5rem 1rem; border-radius: .5rem;
            font-size: .875rem; font-weight: 600; transition: top .2s;
        }
        .skip-link:focus { top: 1rem; }

        /* Room for the mobile bottom navigation */
        @media (max-width: 767px) { body { padding-bottom: 4.25rem; } }
    </style>
    @stack('head')
</head>

<body class="font-sans antialiased text-slate-900 bg-slate-50">

    <a href="#main-content" class="skip-link">Lompat ke konten</a>

    {{-- ==================== HEADER ==================== --}}
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center gap-3">

                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2" aria-label="DEPOSUSU, beranda">
                    <span class="w-9 h-9 rounded-xl bg-brand-600 text-white font-black flex items-center justify-center text-lg">D</span>
                    <span class="text-lg md:text-xl font-extrabold text-brand-600 tracking-tight">DEPOSUSU</span>
                </a>

                <!-- Search (desktop) -->
                <div class="hidden md:block flex-1 max-w-2xl relative" id="desktop-search-container">
                    <label for="product-search" class="sr-only">Cari produk</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="search" id="product-search" autocomplete="off"
                            role="combobox" aria-expanded="false" aria-controls="desktop-suggestions-list"
                            oninput="handleSearchInput(this.value, 'desktop')"
                            onfocus="handleSearchInput(this.value, 'desktop')"
                            onkeydown="handleSuggestionKeys(event, 'desktop')"
                            class="block w-full h-11 pl-11 pr-4 rounded-xl border border-slate-200 bg-slate-50 text-sm placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 focus:outline-none transition-colors"
                            placeholder="Cari susu, yoghurt, keju...">
                    </div>
                    <div id="desktop-suggestions" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl shadow-slate-900/10 border border-slate-100 overflow-hidden z-50">
                        <ul id="desktop-suggestions-list" role="listbox" class="max-h-96 overflow-y-auto py-1"></ul>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-1 md:gap-2">

                    <a href="{{ route('wishlist.index') }}"
                        class="hidden sm:inline-flex p-2.5 text-slate-500 hover:text-brand-600 hover:bg-slate-50 rounded-xl relative transition-colors"
                        aria-label="Wishlist">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        @auth
                            @php $wishlistCount = Auth::user()->wishlists()->count(); @endphp
                            @if($wishlistCount > 0)
                                <span class="absolute top-1 right-1 min-w-[1.05rem] h-[1.05rem] px-1 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $wishlistCount }}</span>
                            @endif
                        @endauth
                    </a>

                    <button type="button" onclick="openMiniCart()"
                        class="p-2.5 text-slate-500 hover:text-brand-600 hover:bg-slate-50 rounded-xl relative transition-colors"
                        aria-label="Buka keranjang belanja">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="cart-badge absolute top-1 right-1 min-w-[1.05rem] h-[1.05rem] px-1 bg-brand-600 text-white text-[10px] font-bold rounded-full items-center justify-center" style="display:none;">0</span>
                    </button>

                    <div class="h-6 w-px bg-slate-200 hidden md:block mx-1"></div>

                    <div class="hidden md:flex items-center gap-2">
                        @auth
                            <div class="relative" id="account-menu">
                                <button type="button" onclick="toggleAccountMenu()"
                                    class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-xl hover:bg-slate-50 transition-colors"
                                    aria-haspopup="true" aria-expanded="false" id="account-menu-button">
                                    <span class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 font-bold text-xs flex items-center justify-center">
                                        {{ Auth::user()->initials() }}
                                    </span>
                                    <span class="text-sm font-semibold text-slate-700 max-w-[9rem] truncate">{{ Auth::user()->name }}</span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div id="account-dropdown"
                                    class="hidden absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl shadow-slate-900/10 border border-slate-100 py-2 z-50">
                                    <a href="{{ route('account.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Akun Saya
                                    </a>
                                    <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2h6v2m-7 4h8a2 2 0 002-2V7a2 2 0 00-2-2h-3V3H9v2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Pesanan Saya
                                    </a>
                                    <a href="{{ route('subscriptions.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Langganan Saya
                                    </a>
                                    <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        Wishlist
                                    </a>
                                    <div class="my-1 border-t border-slate-100"></div>
                                    <button type="button" onclick="document.getElementById('logout-form').submit();"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Keluar
                                    </button>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-4 h-10 inline-flex items-center rounded-xl border border-slate-200 text-slate-700 hover:border-brand-500 hover:text-brand-600 font-semibold text-sm transition-colors">Masuk</a>
                            <a href="{{ route('register') }}"
                                class="px-4 h-10 inline-flex items-center rounded-xl bg-brand-600 text-white font-semibold text-sm hover:bg-brand-700 transition-colors">Daftar</a>
                        @endauth
                    </div>

                    @guest
                        <a href="{{ route('login') }}"
                            class="md:hidden px-3 h-9 inline-flex items-center rounded-lg bg-brand-600 text-white text-sm font-semibold">Masuk</a>
                    @endguest
                </div>
            </div>

            <!-- Search (mobile, always visible) -->
            <div class="md:hidden pb-3 relative" id="mobile-search-container">
                <label for="mobile-product-search" class="sr-only">Cari produk</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="search" id="mobile-product-search" autocomplete="off"
                        oninput="handleSearchInput(this.value, 'mobile')"
                        onfocus="handleSearchInput(this.value, 'mobile')"
                        onkeydown="handleSuggestionKeys(event, 'mobile')"
                        class="block w-full h-11 pl-11 pr-4 rounded-xl border border-slate-200 bg-slate-50 text-sm placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 focus:outline-none transition-colors"
                        placeholder="Cari susu, yoghurt, keju...">
                </div>
                <div id="mobile-suggestions" class="hidden absolute top-full left-0 right-0 mt-1 bg-white rounded-2xl shadow-2xl shadow-slate-900/10 border border-slate-100 overflow-hidden z-50">
                    <ul id="mobile-suggestions-list" role="listbox" class="max-h-80 overflow-y-auto py-1"></ul>
                </div>
            </div>
        </div>
    </header>

    {{-- ==================== MAIN ==================== --}}
    <main id="main-content">
        @yield('content')
    </main>

    {{-- ==================== FOOTER ==================== --}}
    <footer class="bg-white border-t border-slate-200 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-9 h-9 rounded-xl bg-brand-600 text-white font-black flex items-center justify-center text-lg">D</span>
                        <span class="text-lg font-extrabold text-brand-600">DEPOSUSU</span>
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed">Mengantar kebaikan, sepenuh hati. Susu &amp; produk olahan segar, langsung ke rumah Anda.</p>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900 mb-3">Belanja</h4>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="{{ route('home') }}" class="hover:text-brand-600">Semua Produk</a></li>
                        <li><a href="{{ route('home') }}#promo-section" class="hover:text-brand-600">Promo</a></li>
                        <li><a href="{{ route('cart.index') }}" class="hover:text-brand-600">Keranjang</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900 mb-3">Bantuan</h4>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="{{ route('transactions.index') }}" class="hover:text-brand-600">Lacak Pesanan</a></li>
                        <li><a href="{{ route('account.index') }}" class="hover:text-brand-600">Akun Saya</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900 mb-3">Jaminan Kami</h4>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Produk selalu segar
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Gratis ongkir area layanan
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Bisa bayar di tempat
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-slate-100 text-xs text-slate-400">
                &copy; {{ date('Y') }} DEPOSUSU. Seluruh hak cipta dilindungi.
            </div>
        </div>
    </footer>

    {{-- ==================== MOBILE BOTTOM NAV ==================== --}}
    @php $nav = request()->path(); @endphp
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-white border-t border-slate-200 pb-[env(safe-area-inset-bottom)]"
         aria-label="Navigasi utama">
        <div class="grid grid-cols-4 h-16">
            <a href="{{ route('home') }}"
               class="flex flex-col items-center justify-center gap-1 {{ $nav === '/' ? 'text-brand-600' : 'text-slate-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[10px] font-semibold">Beranda</span>
            </a>
            <a href="{{ route('cart.index') }}"
               class="relative flex flex-col items-center justify-center gap-1 {{ str_starts_with($nav, 'cart') ? 'text-brand-600' : 'text-slate-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="cart-badge absolute top-2 right-[22%] min-w-[1.05rem] h-[1.05rem] px-1 bg-brand-600 text-white text-[10px] font-bold rounded-full items-center justify-center" style="display:none;">0</span>
                <span class="text-[10px] font-semibold">Keranjang</span>
            </a>
            <a href="{{ route('transactions.index') }}"
               class="flex flex-col items-center justify-center gap-1 {{ str_starts_with($nav, 'transactions') ? 'text-brand-600' : 'text-slate-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2h6v2m-7 4h8a2 2 0 002-2V7a2 2 0 00-2-2h-3V3H9v2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-[10px] font-semibold">Pesanan</span>
            </a>
            <a href="{{ Auth::check() ? route('account.index') : route('login') }}"
               class="flex flex-col items-center justify-center gap-1 {{ str_starts_with($nav, 'account') ? 'text-brand-600' : 'text-slate-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] font-semibold">Akun</span>
            </a>
        </div>
    </nav>

    {{-- ==================== MINI CART DRAWER ==================== --}}
    <div id="mini-cart" class="fixed inset-0 z-[70] hidden" role="dialog" aria-modal="true" aria-label="Keranjang belanja">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-200"
             id="mini-cart-overlay" onclick="closeMiniCart()"></div>
        <aside id="mini-cart-panel"
            class="absolute right-0 inset-y-0 w-full max-w-md bg-white shadow-2xl flex flex-col translate-x-full transition-transform duration-300 ease-out">
            <div class="flex items-center justify-between px-5 h-16 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Keranjang Belanja</h2>
                <button type="button" onclick="closeMiniCart()" aria-label="Tutup keranjang"
                    class="p-2 -mr-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="mini-cart-body" class="flex-1 overflow-y-auto px-5 py-4"></div>
            <div id="mini-cart-footer" class="border-t border-slate-100 p-5 hidden">
                <div class="flex items-baseline justify-between mb-4">
                    <span class="text-sm text-slate-500">Total</span>
                    <span id="mini-cart-total" class="text-xl font-bold text-slate-900">Rp 0</span>
                </div>
                <a href="{{ route('checkout.index') }}"
                   class="w-full h-12 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center hover:bg-brand-700 transition-colors">
                    Checkout Sekarang
                </a>
                <a href="{{ route('cart.index') }}"
                   class="mt-2 w-full h-11 rounded-xl border border-slate-200 text-slate-700 font-semibold flex items-center justify-center hover:bg-slate-50 transition-colors">
                    Lihat Keranjang
                </a>
            </div>
        </aside>
    </div>

    {{-- ==================== TOASTS ==================== --}}
    <div id="toast-region" aria-live="polite" aria-atomic="true"
        class="fixed z-[90] bottom-20 left-4 right-4 md:bottom-auto md:left-auto md:top-20 md:right-6 md:w-80 flex flex-col gap-2 pointer-events-none"></div>

    @auth
        <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">@csrf</form>
    @endauth

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const ROUTES = {
            cartData: '{{ route('cart.data') }}',
            cartAdd: '{{ route('cart.add') }}',
            cartIndex: '{{ route('cart.index') }}',
            wishlistToggle: '{{ route('wishlist.toggle') }}',
            login: '{{ route('login') }}',
            suggest: '/products/suggest',
        };

        /* ---------------- Toasts ---------------- */
        function showNotification(message, type = 'success', action = null) {
            const region = document.getElementById('toast-region');
            if (!region) return;

            const tone = {
                success: 'bg-slate-900',
                error: 'bg-rose-600',
                info: 'bg-slate-900',
            }[type] || 'bg-slate-900';

            const toast = document.createElement('div');
            toast.className = `${tone} text-white rounded-xl shadow-xl px-4 py-3 text-sm font-medium flex items-center gap-3 pointer-events-auto animate-fade-in-up`;

            const text = document.createElement('span');
            text.className = 'flex-1';
            text.textContent = message;
            toast.appendChild(text);

            if (action) {
                const link = document.createElement('button');
                link.type = 'button';
                link.className = 'shrink-0 font-bold underline underline-offset-2';
                link.textContent = action.label;
                link.onclick = action.onClick;
                toast.appendChild(link);
            }

            region.appendChild(toast);
            // Keep at most three toasts on screen
            while (region.children.length > 3) region.removeChild(region.firstChild);

            setTimeout(() => {
                toast.style.transition = 'opacity .25s, transform .25s';
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(8px)';
                setTimeout(() => toast.remove(), 250);
            }, 3200);
        }

        /* ---------------- Cart ---------------- */
        let cartState = null;

        function renderCartBadges(totalItems) {
            document.querySelectorAll('.cart-badge').forEach(badge => {
                badge.textContent = totalItems > 99 ? '99+' : totalItems;
                badge.style.display = totalItems > 0 ? 'flex' : 'none';
            });
        }

        async function refreshCart({ silent = true } = {}) {
            try {
                const response = await fetch(ROUTES.cartData, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) return null;
                const data = await response.json();
                if (data.success) {
                    cartState = data.cart;
                    renderCartBadges(data.cart.total_items);
                    renderMiniCart();
                    return data.cart;
                }
            } catch (error) {
                if (!silent) console.error('Gagal memuat keranjang:', error);
            }
            return null;
        }

        async function addToCart(productId, quantity = 1, button = null) {
            button = button || (window.event ? window.event.currentTarget : null);

            let originalContent = null;
            if (button) {
                originalContent = button.innerHTML;
                button.disabled = true;
                button.setAttribute('aria-busy', 'true');
                button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
            }

            const restore = () => {
                if (button && originalContent !== null) {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                    button.removeAttribute('aria-busy');
                }
            };

            try {
                const response = await fetch(ROUTES.cartAdd, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ product_id: productId, quantity }),
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    if (data.cart) renderCartBadges(data.cart.total_items);
                    refreshCart();
                    if (button) {
                        button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Ditambahkan';
                        setTimeout(restore, 1200);
                    }
                    showNotification('Produk masuk keranjang', 'success', {
                        label: 'Lihat',
                        onClick: openMiniCart,
                    });
                    return true;
                }

                showNotification(data.message || 'Gagal menambahkan ke keranjang', 'error');
                restore();
                return false;
            } catch (error) {
                console.error(error);
                showNotification('Koneksi bermasalah, coba lagi', 'error');
                restore();
                return false;
            }
        }

        /* ---------------- Mini cart drawer ---------------- */
        const miniCart = document.getElementById('mini-cart');
        const miniCartPanel = document.getElementById('mini-cart-panel');
        const miniCartOverlay = document.getElementById('mini-cart-overlay');

        function renderMiniCart() {
            const body = document.getElementById('mini-cart-body');
            const footer = document.getElementById('mini-cart-footer');
            if (!body || !cartState) return;

            if (!cartState.items || cartState.items.length === 0) {
                footer.classList.add('hidden');
                body.innerHTML = `
                    <div class="h-full flex flex-col items-center justify-center text-center py-16">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <p class="font-semibold text-slate-900">Keranjang masih kosong</p>
                        <p class="text-sm text-slate-500 mt-1 mb-6">Yuk, isi dengan produk favorit Anda.</p>
                        <button type="button" onclick="closeMiniCart()" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700">Mulai Belanja</button>
                    </div>`;
                return;
            }

            footer.classList.remove('hidden');
            document.getElementById('mini-cart-total').textContent = cartState.total_price_formatted;

            body.innerHTML = cartState.items.map(item => `
                <div class="flex gap-3 py-3 border-b border-slate-50 last:border-0" data-mini-item="${item.id}">
                    <img src="${item.image || 'https://placehold.co/80x80/f1f5f9/94a3b8?text=%20'}" alt=""
                         class="w-16 h-16 rounded-xl object-cover bg-slate-50 shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-900 line-clamp-2 leading-snug">${escapeHtml(item.name)}</p>
                        <p class="text-xs text-slate-500 mt-1">${item.price_formatted} / item</p>
                        <div class="mt-2 flex items-center justify-between gap-2">
                            <div class="flex items-center h-8 border border-slate-200 rounded-lg overflow-hidden">
                                <button type="button" onclick="miniCartChangeQty(${item.id}, -1)" aria-label="Kurangi jumlah"
                                    class="mini-qty-btn w-7 h-full flex items-center justify-center text-slate-500 hover:bg-slate-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                </button>
                                <span class="mini-qty w-7 text-center text-xs font-bold text-slate-900">${item.quantity}</span>
                                <button type="button" onclick="miniCartChangeQty(${item.id}, 1)" aria-label="Tambah jumlah"
                                    class="mini-qty-btn w-7 h-full flex items-center justify-center text-slate-500 hover:bg-slate-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                            <span class="mini-subtotal text-sm font-bold text-slate-900">${item.subtotal_formatted}</span>
                        </div>
                    </div>
                    <button type="button" onclick="miniCartRemove(${item.id})" aria-label="Hapus ${escapeHtml(item.name)} dari keranjang"
                        class="shrink-0 self-start p-1.5 -mt-1 -mr-1 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>`).join('');
        }

        const miniCartPending = new Set();

        async function miniCartChangeQty(itemId, delta) {
            if (miniCartPending.has(itemId) || !cartState) return;
            const item = cartState.items.find(i => i.id === itemId);
            if (!item) return;

            const newQty = item.quantity + delta;
            if (newQty < 1) {
                miniCartRemove(itemId);
                return;
            }

            miniCartPending.add(itemId);
            const row = document.querySelector(`[data-mini-item="${itemId}"]`);
            row?.querySelectorAll('.mini-qty-btn').forEach(b => b.classList.add('opacity-50', 'pointer-events-none'));

            try {
                const response = await fetch(`/cart/update/${itemId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ quantity: newQty }),
                });
                const data = await response.json();

                if (data.success) {
                    await refreshCart();
                } else {
                    showNotification(data.message || 'Gagal mengubah jumlah', 'error');
                }
            } catch (error) {
                console.error(error);
                showNotification('Koneksi bermasalah, coba lagi', 'error');
            } finally {
                miniCartPending.delete(itemId);
            }
        }

        async function miniCartRemove(itemId) {
            if (miniCartPending.has(itemId)) return;
            miniCartPending.add(itemId);

            const row = document.querySelector(`[data-mini-item="${itemId}"]`);
            if (row) row.style.opacity = '0.4';

            try {
                const response = await fetch(`/cart/remove/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });
                const data = await response.json();

                if (data.success) {
                    showNotification('Item dihapus dari keranjang', 'success');
                    await refreshCart();
                } else {
                    if (row) row.style.opacity = '1';
                    showNotification(data.message || 'Gagal menghapus item', 'error');
                }
            } catch (error) {
                console.error(error);
                if (row) row.style.opacity = '1';
                showNotification('Koneksi bermasalah, coba lagi', 'error');
            } finally {
                miniCartPending.delete(itemId);
            }
        }

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value ?? '';
            return div.innerHTML;
        }

        let lastFocusedBeforeCart = null;

        function openMiniCart() {
            if (!miniCart) return;
            lastFocusedBeforeCart = document.activeElement;
            miniCart.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(() => {
                miniCartOverlay.classList.add('opacity-100');
                miniCartPanel.classList.remove('translate-x-full');
            });
            refreshCart();
        }

        function closeMiniCart() {
            if (!miniCart) return;
            miniCartOverlay.classList.remove('opacity-100');
            miniCartPanel.classList.add('translate-x-full');
            document.body.style.overflow = '';
            setTimeout(() => {
                miniCart.classList.add('hidden');
                if (lastFocusedBeforeCart) lastFocusedBeforeCart.focus();
            }, 250);
        }

        /* ---------------- Wishlist ---------------- */
        async function toggleWishlist(productId, button) {
            try {
                const response = await fetch(ROUTES.wishlistToggle, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ product_id: productId }),
                });

                if (response.status === 401) {
                    showNotification('Masuk dulu untuk menyimpan produk', 'info', {
                        label: 'Masuk',
                        onClick: () => window.location.href = ROUTES.login,
                    });
                    return;
                }

                const data = await response.json();
                if (!data.success) return;

                const svg = button.querySelector('svg');
                const added = data.status === 'added';
                svg.classList.toggle('text-rose-500', added);
                svg.classList.toggle('text-slate-400', !added);
                svg.setAttribute('fill', added ? 'currentColor' : 'none');
                button.setAttribute('aria-label', added ? 'Hapus dari wishlist' : 'Simpan ke wishlist');

                if (!added && window.location.pathname.includes('/wishlist')) {
                    const card = button.closest('.product-card');
                    if (card) {
                        card.style.transition = 'opacity .25s, transform .25s';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(.95)';
                        setTimeout(() => {
                            card.remove();
                            if (document.querySelectorAll('.product-card').length === 0) location.reload();
                        }, 250);
                    }
                }

                showNotification(data.message, 'success');
            } catch (error) {
                console.error('Error toggling wishlist:', error);
            }
        }

        /* ---------------- Account dropdown ---------------- */
        function toggleAccountMenu() {
            const dropdown = document.getElementById('account-dropdown');
            const button = document.getElementById('account-menu-button');
            if (!dropdown) return;
            const open = dropdown.classList.toggle('hidden');
            button.setAttribute('aria-expanded', String(!open));
        }

        /* ---------------- Search suggestions ---------------- */
        let suggestionTimeout = null;
        const activeSuggestion = { desktop: -1, mobile: -1 };

        function handleSearchInput(query, platform) {
            // The home page filters its grid live as well.
            if (typeof searchProducts === 'function' && window.location.pathname === '/') {
                searchProducts(platform);
            }

            const dropdown = document.getElementById(`${platform}-suggestions`);
            const list = document.getElementById(`${platform}-suggestions-list`);
            if (!dropdown || !list) return;

            activeSuggestion[platform] = -1;

            if (!query || query.trim() === '') {
                dropdown.classList.add('hidden');
                return;
            }

            if (suggestionTimeout) clearTimeout(suggestionTimeout);
            suggestionTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`${ROUTES.suggest}?q=${encodeURIComponent(query)}`);
                    const results = await response.json();

                    if (!results.length) {
                        list.innerHTML = '<li class="px-4 py-4 text-sm text-slate-500 text-center">Produk tidak ditemukan.</li>';
                        dropdown.classList.remove('hidden');
                        return;
                    }

                    list.innerHTML = results.map(item => `
                        <li role="option" data-href="/products/${item.id}"
                            class="suggestion-item cursor-pointer border-b border-slate-50 last:border-0">
                            <div class="flex items-center gap-3 px-4 py-2.5">
                                <img src="${item.image}" alt="" class="w-11 h-11 object-cover rounded-lg bg-slate-50 shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate">${escapeHtml(item.name)}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        ${item.has_discount ? `<span class="text-[11px] text-slate-400 line-through">${item.original_price_formatted}</span>` : ''}
                                        <span class="text-sm font-bold text-brand-600">${item.price_formatted}</span>
                                    </div>
                                </div>
                            </div>
                        </li>`).join('');

                    list.querySelectorAll('.suggestion-item').forEach(li => {
                        li.addEventListener('click', () => {
                            window.location.href = li.dataset.href;
                        });
                    });

                    dropdown.classList.remove('hidden');
                } catch (error) {
                    console.error('Error fetching suggestions:', error);
                }
            }, 250);
        }

        function handleSuggestionKeys(event, platform) {
            const dropdown = document.getElementById(`${platform}-suggestions`);
            const list = document.getElementById(`${platform}-suggestions-list`);
            if (!dropdown || dropdown.classList.contains('hidden')) return;

            const items = Array.from(list.querySelectorAll('.suggestion-item'));
            if (!items.length) return;

            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                const step = event.key === 'ArrowDown' ? 1 : -1;
                activeSuggestion[platform] = (activeSuggestion[platform] + step + items.length) % items.length;
                items.forEach((li, index) => {
                    li.classList.toggle('bg-brand-50', index === activeSuggestion[platform]);
                });
                items[activeSuggestion[platform]].scrollIntoView({ block: 'nearest' });
            } else if (event.key === 'Enter' && activeSuggestion[platform] >= 0) {
                event.preventDefault();
                window.location.href = items[activeSuggestion[platform]].dataset.href;
            } else if (event.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        }

        /* ---------------- Global listeners ---------------- */
        document.addEventListener('click', (event) => {
            ['desktop', 'mobile'].forEach(platform => {
                const container = document.getElementById(`${platform}-search-container`);
                const dropdown = document.getElementById(`${platform}-suggestions`);
                if (container && dropdown && !container.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            const accountMenu = document.getElementById('account-menu');
            const accountDropdown = document.getElementById('account-dropdown');
            if (accountMenu && accountDropdown && !accountMenu.contains(event.target)) {
                accountDropdown.classList.add('hidden');
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') return;
            if (miniCart && !miniCart.classList.contains('hidden')) closeMiniCart();
            const accountDropdown = document.getElementById('account-dropdown');
            if (accountDropdown) accountDropdown.classList.add('hidden');
        });

        document.addEventListener('DOMContentLoaded', () => refreshCart());
    </script>

    @stack('scripts')
</body>

</html>
