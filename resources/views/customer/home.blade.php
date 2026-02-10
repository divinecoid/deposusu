@extends('layouts.customer')

@section('content')
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .animate-slide-in {
            animation: slideIn 0.5s ease-out forwards;
        }

        .product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-card:hover {
            transform: translateY(-8px);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }

        .category-chip {
            transition: all 0.3s ease;
        }

        .category-chip:hover {
            transform: scale(1.05);
        }

        .category-chip.active {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
        }
    </style>

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100">

        <!-- Hero Section -->
        <section class="gradient-bg text-white py-12 md:py-24 animate-fade-in-up">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <div class="space-y-6 text-center md:text-left">
                        <h1 class="text-3xl md:text-6xl font-bold leading-tight">
                            Produk Susu <br>
                            <span class="text-blue-200">Terbaik</span> untuk Anda
                        </h1>
                        <p class="text-lg md:text-xl text-blue-100">
                            Nikmati kesegaran dan kualitas terbaik dari berbagai pilihan produk susu premium
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 pt-4 justify-center md:justify-start">
                            <a href="#products"
                                class="px-6 py-3 md:px-8 md:py-4 bg-white text-blue-600 rounded-full font-semibold hover:bg-blue-500 hover:text-white transform hover:scale-105 transition-all duration-300 shadow-lg text-center text-sm md:text-base">
                                Belanja Sekarang
                            </a>
                            <a href="#featured"
                                class="px-6 py-3 md:px-8 md:py-4 border-2 border-white text-white rounded-full font-semibold hover:bg-white hover:text-blue-600 transform hover:scale-105 transition-all duration-300 text-center text-sm md:text-base">
                                Produk Unggulan
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:block animate-slide-in">
                        <div class="relative">
                            <div class="absolute inset-0 bg-blue-300 rounded-full blur-3xl opacity-30 animate-pulse">
                            </div>
                            <svg class="w-full h-auto relative z-10" viewBox="0 0 400 400" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="200" cy="200" r="150" fill="white" opacity="0.2" />
                                <circle cx="200" cy="200" r="120" fill="white" opacity="0.3" />
                                <circle cx="200" cy="200" r="90" fill="white" opacity="0.4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Category Filter Section -->
        <section class="py-8 bg-white shadow-sm sticky top-16 z-40 animate-slide-in">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-hide">
                    <span class="text-gray-700 font-semibold whitespace-nowrap">Kategori:</span>
                    <button
                        class="category-chip active px-6 py-2 bg-blue-100 text-blue-600 rounded-full font-medium whitespace-nowrap text-sm">
                        Semua Produk
                    </button>
                    <button
                        class="category-chip px-6 py-2 bg-gray-100 text-gray-700 rounded-full font-medium hover:bg-blue-100 hover:text-blue-600 whitespace-nowrap text-sm">
                        Susu Segar
                    </button>
                    <button
                        class="category-chip px-6 py-2 bg-gray-100 text-gray-700 rounded-full font-medium hover:bg-blue-100 hover:text-blue-600 whitespace-nowrap text-sm">
                        Susu UHT
                    </button>
                    <button
                        class="category-chip px-6 py-2 bg-gray-100 text-gray-700 rounded-full font-medium hover:bg-blue-100 hover:text-blue-600 whitespace-nowrap text-sm">
                        Yogurt
                    </button>
                    <button
                        class="category-chip px-6 py-2 bg-gray-100 text-gray-700 rounded-full font-medium hover:bg-blue-100 hover:text-blue-600 whitespace-nowrap text-sm">
                        Keju
                    </button>
                    <button
                        class="category-chip px-6 py-2 bg-gray-100 text-gray-700 rounded-full font-medium hover:bg-blue-100 hover:text-blue-600 whitespace-nowrap text-sm">
                        Mentega
                    </button>
                </div>
            </div>
        </section>

        <!-- Featured Products Section -->
        <section id="featured" class="py-12 animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl md:text-4xl font-bold text-gray-900">Produk Unggulan</h2>
                        <p class="text-gray-600 mt-1 md:mt-2 text-sm md:text-base">Pilihan terbaik minggu ini</p>
                    </div>
                    <a href="#"
                        class="hidden md:block text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-2">
                        Lihat Semua
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($products->take(3) as $product)
                        <div class="product-card glass-effect rounded-2xl overflow-hidden shadow-lg"
                            style="animation-delay: {{ ($loop->iteration - 1) * 0.1 }}s">
                            <div class="relative">
                                <div class="absolute top-4 right-4 z-10 flex flex-col gap-2 italic">
                                    <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-xs font-bold shadow-sm">
                                        Featured
                                    </span>
                                    @if($product->active_discount)
                                        <span
                                            class="px-3 py-1 bg-red-500 text-white rounded-full text-xs font-bold shadow-sm animate-pulse">
                                            @if($product->active_discount->discount_type === 'PERCENTAGE')
                                                {{ number_format($product->active_discount->discount_value, 0) }}% OFF
                                            @else
                                                Hemat Rp {{ number_format($product->active_discount->discount_value, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                                <div class="aspect-square bg-gradient-to-br from-blue-50 to-blue-100 p-8">
                                    <img src="{{ $product->image && str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-500"
                                        style="filter: none !important; background-color: transparent !important;"
                                        onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image'; console.error('Image failing to load:', this.src);">
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-1 md:mb-2">{{ $product->name }}</h3>
                                <p class="text-gray-600 text-xs md:text-sm mb-3 md:mb-4 line-clamp-2 md:line-clamp-none">Produk
                                    susu berkualitas premium dengan rasa yang lezat</p>
                                <div class="flex items-center justify-between">
                                    <div>
                                        @if($product->active_discount)
                                            <p class="text-sm text-gray-500 line-through">Rp
                                                {{ number_format($product->price, 0, ',', '.') }}
                                            </p>
                                            <p class="text-2xl font-bold text-blue-600">Rp
                                                {{ number_format($product->discounted_price, 0, ',', '.') }}
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-500 opacity-0">-</p>
                                            <p class="text-2xl font-bold text-blue-600">Rp
                                                {{ number_format($product->price, 0, ',', '.') }}
                                            </p>
                                        @endif
                                    </div>
                                    <button onclick="addToCart({{ $product->id }})"
                                        class="px-4 py-2 md:px-6 md:py-3 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-full font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Main Product Grid Section -->
        <section id="products" class="py-12 animate-fade-in-up" style="animation-delay: 0.4s">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl md:text-4xl font-bold text-gray-900">Semua Produk</h2>
                        <p class="text-gray-600 mt-1 md:mt-2 text-sm md:text-base">{{ count($products) }} produk tersedia
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <select
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option>Terpopuler</option>
                            <option>Harga Terendah</option>
                            <option>Harga Tertinggi</option>
                            <option>Terbaru</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-6">
                    @foreach($products as $product)
                        <div class="product-card bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl"
                            style="animation-delay: {{ ($loop->iteration - 1) * 0.05 }}s">
                            <div class="relative group">
                                <!-- Wishlist Button -->
                                <button
                                    class="absolute top-3 right-3 z-10 p-2 bg-white rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-red-50">
                                    <svg class="w-5 h-5 text-gray-400 hover:text-red-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>

                                @if($product->active_discount)
                                    <div class="absolute top-3 left-3 z-10">
                                        <span class="px-2 py-1 bg-red-500 text-white text-[10px] font-bold rounded-lg shadow-sm">
                                            @if($product->active_discount->discount_type === 'PERCENTAGE')
                                                -{{ number_format($product->active_discount->discount_value, 0) }}%
                                            @else
                                                -{{ $product->active_discount->discount_value >= 1000 ? number_format($product->active_discount->discount_value / 1000, 0) . 'K' : number_format($product->active_discount->discount_value, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </div>
                                @endif

                                <div class="aspect-square bg-gradient-to-br from-gray-50 to-gray-100 p-6">
                                    <img src="{{ $product->image && str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                                        style="filter: none !important; background-color: transparent !important;"
                                        onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image'; console.error('Image failing to load:', this.src);">
                                </div>

                                <!-- Quick View on Hover -->
                                <div
                                    class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center">
                                    <button
                                        class="px-6 py-2 bg-white text-blue-600 rounded-full font-semibold opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 hover:bg-blue-600 hover:text-white">
                                        Quick View
                                    </button>
                                </div>
                            </div>

                            <div class="p-4">
                                <div class="mb-2">
                                    <span
                                        class="inline-block px-2 py-1 bg-blue-100 text-blue-600 text-xs font-semibold rounded">
                                        Susu Segar
                                    </span>
                                </div>
                                <h3 class="text-sm md:text-base font-bold text-gray-900 mb-1 line-clamp-2 h-10">
                                    {{ $product->name }}
                                </h3>

                                <!-- Rating -->
                                <div class="flex items-center gap-1 mb-3">
                                    @for($i = 0; $i < 5; $i++)
                                        <svg class="w-4 h-4 {{ $i < 4 ? 'text-blue-500' : 'text-gray-300' }}" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                    <span class="text-xs text-gray-500 ml-1">(4.0)</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        @if($product->active_discount)
                                            <p class="text-xs text-gray-400 line-through">Rp
                                                {{ number_format($product->price, 0, ',', '.') }}
                                            </p>
                                            <p class="text-base md:text-lg font-bold text-blue-600">Rp
                                                {{ number_format($product->discounted_price, 0, ',', '.') }}
                                            </p>
                                        @else
                                            <p class="text-base md:text-lg font-bold text-blue-600">Rp
                                                {{ number_format($product->price, 0, ',', '.') }}
                                            </p>
                                            <p class="text-[10px] md:text-xs text-gray-400">per unit</p>
                                        @endif
                                    </div>
                                    <button onclick="addToCart({{ $product->id }})"
                                        class="p-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-lg hover:shadow-lg transform hover:scale-110 transition-all duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Load More Button -->
                <div class="text-center mt-12">
                    <button
                        class="px-10 py-4 bg-white text-blue-600 border-2 border-blue-600 rounded-full font-semibold hover:bg-blue-600 hover:text-white transform hover:scale-105 transition-all duration-300 shadow-lg">
                        Muat Lebih Banyak
                    </button>
                </div>
            </div>
        </section>

        <!-- Benefits Section -->
        <section class="py-16 bg-white animate-fade-in-up" style="animation-delay: 0.8s">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-12">
                    Mengapa Memilih Kami?
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
                    <div class="text-center group">
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base md:text-lg font-bold text-gray-900 mb-1 md:mb-2">Kualitas Terjamin</h3>
                        <p class="text-gray-600 text-xs md:text-base">Produk berkualitas premium</p>
                    </div>
                    <div class="text-center group">
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base md:text-lg font-bold text-gray-900 mb-1 md:mb-2">Harga Terbaik</h3>
                        <p class="text-gray-600 text-xs md:text-base">Harga kompetitif & terjangkau</p>
                    </div>
                    <div class="text-center group">
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-base md:text-lg font-bold text-gray-900 mb-1 md:mb-2">Pengiriman Cepat</h3>
                        <p class="text-gray-600 text-xs md:text-base">Dikirim dengan cepat & aman</p>
                    </div>
                    <div class="text-center group">
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base md:text-lg font-bold text-gray-900 mb-1 md:mb-2">Dukungan 24/7</h3>
                        <p class="text-gray-600 text-xs md:text-base">Customer service siap membantu</p>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Category filter functionality
        document.querySelectorAll('.category-chip').forEach(chip => {
            chip.addEventListener('click', function () {
                document.querySelectorAll('.category-chip').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Add to cart function
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function addToCart(productId) {
            const button = event.currentTarget;
            const originalContent = button.innerHTML;

            // Show loading
            button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            button.disabled = true;

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
                    // Show success icon
                    button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';

                    // Update cart badge
                    const badge = document.querySelector('.cart-badge');
                    if (badge) {
                        badge.textContent = data.cart.total_items;
                        badge.style.display = data.cart.total_items > 0 ? 'flex' : 'none';
                    }

                    // Show notification
                    showNotification(data.message, 'success');

                    // Reset button after delay
                    setTimeout(() => {
                        button.innerHTML = originalContent;
                        button.disabled = false;
                    }, 1000);
                } else {
                    // Show error
                    showNotification(data.message, 'error');
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-24 right-4 px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'
                } text-white font-semibold`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    </script>
@endsection