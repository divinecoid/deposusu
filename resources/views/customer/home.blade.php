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

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        .skeleton {
            background: #eff6ff;
            background-image: linear-gradient(90deg, #eff6ff 0%, #dbeafe 20%, #eff6ff 40%, #eff6ff 100%);
            background-repeat: no-repeat;
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite linear;
        }

        .image-container img {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .image-container img.loaded {
            opacity: 1;
        }
    </style>

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100">

        <!-- Hero Section -->
        <section id="hero-section" class="gradient-bg text-white py-12 md:py-24 animate-fade-in-up">
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
                                Produk Promo
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

        <!-- Category Selection Section -->
        <section id="category-navigation"
            class="py-6 bg-white shadow-sm sticky top-[64px] z-30 animate-slide-in border-b border-gray-100 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-end gap-4">

                    <button onclick="openCategoryModal()"
                        class="flex items-center justify-between gap-4 px-6 py-3 bg-gray-50 border border-gray-200 rounded-2xl hover:border-blue-500 hover:bg-white transition-all duration-300 group shadow-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span class="text-gray-600 font-medium" id="selected-category-name">Semua Produk</span>
                        </div>
                        <div class="flex items-center gap-2 text-blue-600 font-bold text-sm">
                            Pilih Kategori
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>
                </div>
            </div>
        </section>

        <!-- Promo Products Section -->
        <section id="promo-section" class="py-12 animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl md:text-4xl font-bold text-gray-900">Produk Promo</h2>
                        <p class="text-gray-600 mt-1 md:mt-2 text-sm md:text-base">Jangan lewatkan diskon menarik minggu ini
                        </p>
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
                    @foreach($products->whereNotNull('active_discount')->take(3) as $product)
                        <div onclick="openQuickView({{ json_encode($product) }})"
                            class="product-card group glass-effect rounded-2xl overflow-hidden shadow-lg cursor-pointer"
                            style="animation-delay: {{ ($loop->iteration - 1) * 0.1 }}s">
                            <div class="relative">
                                <div class="absolute top-4 right-4 z-10 flex flex-col gap-2 italic">
                                    <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-xs font-bold shadow-sm">
                                        Promo
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
                                <div class="image-container skeleton aspect-square bg-blue-50 p-8 rounded-2xl overflow-hidden">
                                    <img src="{{ $product->image && str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-500"
                                        style="filter: none !important; background-color: transparent !important;"
                                        onload="this.classList.add('loaded'); this.parentElement.classList.remove('skeleton');"
                                        onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image'; this.classList.add('loaded'); this.parentElement.classList.remove('skeleton'); console.error('Image failing to load:', this.src);">
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
                                    <button onclick="event.stopPropagation(); addToCart({{ $product->id }})"
                                        class="px-6 py-2.5 md:px-8 md:py-3.5 bg-blue-600 text-white rounded-full font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 transform hover:scale-105 transition-all duration-300 flex items-center gap-2 text-sm md:text-base">
                                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span>Add to Cart</span>
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

                <div id="product-grid-container" class="min-h-[400px]">
                    @include('customer.partials.product_grid', ['products' => $products])
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

    <!-- Category Selection Modal -->
    <div id="category-modal" class="fixed inset-0 z-[70] hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-md" onclick="closeCategoryModal()">
            </div>

            <!-- Modal Content -->
            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full animate-fade-in-up relative">
                <!-- Header -->
                <div class="px-8 py-6 border-b border-gray-100 sticky top-0 bg-white/80 backdrop-blur-md z-10">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Pilih Kategori</h3>
                        <button onclick="closeCategoryModal()"
                            class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-full transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="category-search" onkeyup="filterCategories()"
                            class="block w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl leading-5 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-lg"
                            placeholder="Cari kategori (misal: Yogurt, Keju...)">
                    </div>
                </div>

                <!-- Category List -->
                <div class="px-8 py-8 max-h-[60vh] overflow-y-auto">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="category-grid">
                        <!-- All Products Option -->
                        <button onclick="selectCategory('all', 'Semua Produk')"
                            class="category-item flex items-center p-4 bg-blue-50 border border-blue-200 rounded-2xl hover:shadow-md transition-all group text-left">
                            <div
                                class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white mr-4 shadow-lg shadow-blue-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                            </div>
                            <span class="font-bold text-blue-700">Semua Produk</span>
                        </button>

                        @foreach($categories as $category)
                            <button onclick="selectCategory('{{ $category->id }}', '{{ $category->name }}')"
                                class="category-item flex items-center p-4 bg-gray-50 border border-gray-100 rounded-2xl hover:border-blue-500 hover:bg-white hover:shadow-md transition-all group text-left">
                                <div
                                    class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-blue-600 mr-4 shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    <span class="text-xl font-bold">{{ substr($category->name, 0, 1) }}</span>
                                </div>
                                <span
                                    class="font-semibold text-gray-700 group-hover:text-blue-600 transition-colors">{{ $category->name }}</span>
                            </button>
                        @endforeach
                    </div>

                    <!-- No Results -->
                    <div id="no-category-results" class="hidden py-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-gray-500 text-lg">Kategori tidak ditemukan...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Quick View Modal -->
    <div id="quick-view-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" onclick="closeQuickView()"></div>

            <!-- Modal Content -->
            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full animate-fade-in-up relative">
                <button onclick="closeQuickView()"
                    class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 z-10 p-2 hover:bg-gray-100 rounded-full transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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

                        <div class="flex items-center gap-2 mb-6 text-blue-500">
                            <div class="flex">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-5 h-5 {{ $i < 4 ? 'text-blue-500' : 'text-gray-300' }}" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-gray-500 font-medium">(4.0 / 5.0)</span>
                        </div>

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
            notification.className = `fixed top-24 right-4 px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white font-semibold`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Quick View Modal functions
        // Category Modal Logic
        const categoryModal = document.getElementById('category-modal');
        const categorySearch = document.getElementById('category-search');
        const categoryGrid = document.getElementById('category-grid');
        const noCategoryResults = document.getElementById('no-category-results');
        const selectedCategoryName = document.getElementById('selected-category-name');

        function openCategoryModal() {
            categoryModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => categorySearch.focus(), 100);
        }

        function closeCategoryModal() {
            categoryModal.classList.add('hidden');
            document.body.style.overflow = '';
            categorySearch.value = '';
            filterCategories();
        }

        function filterCategories() {
            const query = categorySearch.value.toLowerCase();
            const items = categoryGrid.querySelectorAll('.category-item');
            let hasResults = false;

            items.forEach(item => {
                const name = item.querySelector('span').textContent.toLowerCase();
                if (name.includes(query)) {
                    item.classList.remove('hidden');
                    hasResults = true;
                } else {
                    item.classList.add('hidden');
                }
            });

            noCategoryResults.classList.toggle('hidden', hasResults);
        }

        // Global filtering state
        let currentCategoryId = 'all';
        let currentSearchQuery = '';
        let searchTimeout = null;

        async function applyFilters() {
            const gridContainer = document.getElementById('product-grid-container');
            const heroSection = document.getElementById('hero-section');
            const promoSection = document.getElementById('promo-section');

            // Toggle sections based on search query
            if (currentSearchQuery.trim() !== '') {
                heroSection.classList.add('hidden');
                promoSection.classList.add('hidden');
            } else {
                heroSection.classList.remove('hidden');
                promoSection.classList.remove('hidden');
            }

            // Show loading state
            gridContainer.style.opacity = '0.5';
            gridContainer.style.pointerEvents = 'none';

            try {
                const params = new URLSearchParams({
                    category: currentCategoryId,
                    q: currentSearchQuery
                });

                const response = await fetch(`/products/search?${params.toString()}`);
                const html = await response.text();

                gridContainer.innerHTML = html;
            } catch (error) {
                console.error('Error fetching products:', error);
            } finally {
                gridContainer.style.opacity = '1';
                gridContainer.style.pointerEvents = 'auto';
            }
        }

        function searchProducts(source = 'desktop') {
            const desktopSearch = document.getElementById('product-search');
            const mobileSearch = document.getElementById('mobile-product-search');

            if (source === 'desktop' && desktopSearch) {
                currentSearchQuery = desktopSearch.value;
                if (mobileSearch) mobileSearch.value = currentSearchQuery;
            } else if (source === 'mobile' && mobileSearch) {
                currentSearchQuery = mobileSearch.value;
                if (desktopSearch) desktopSearch.value = currentSearchQuery;
            }

            // Clear existing timeout
            if (searchTimeout) clearTimeout(searchTimeout);

            // 1-second debounce
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 1000);
        }

        function selectCategory(id, name) {
            currentCategoryId = id;
            selectedCategoryName.textContent = name;
            closeCategoryModal();
            applyFilters();

            // Smooth scroll to product section
            document.getElementById('products').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

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
            // Reset image state
            qvImage.classList.remove('loaded');
            qvImageContainer.classList.add('skeleton');

            // Set image
            const imagePath = product.image && product.image.startsWith('storage/')
                ? `/storage/${product.image.replace('storage/', '')}`
                : product.image;
            qvImage.src = imagePath;
            qvImage.alt = product.name;

            // Set content
            qvName.textContent = product.name;
            qvCategory.textContent = 'Susu Segar'; // Default category for now
            qvDescription.textContent = product.description || 'Produk susu berkualitas premium dengan rasa yang lezat. Kaya akan nutrisi dan vitamin untuk kesehatan keluarga Anda.';

            // Calculate prices
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
                    const formattedDiscount = discount.discount_value >= 1000
                        ? (discount.discount_value / 1000) + 'K'
                        : discount.discount_value.toLocaleString('id-ID');
                    qvDiscountBadge.textContent = `Hemat Rp ${formattedDiscount}`;
                }
            } else {
                qvDiscountWrapper.classList.add('hidden');
            }

            qvPrice.textContent = `Rp ${finalPrice.toLocaleString('id-ID')}`;
            qvAddBtn.setAttribute('onclick', `addToCart(${product.id})`);

            // Show modal
            qvModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeQuickView() {
            qvModal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close modal on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !qvModal.classList.contains('hidden')) {
                closeQuickView();
            }
        });
    </script>
@endsection