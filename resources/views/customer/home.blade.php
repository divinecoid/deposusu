@extends('layouts.customer')

@push('html_attr')
    translate="no"
@endpush

@push('head')
    <meta name="google" content="notranslate">
@endpush

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

        <!-- Hero Carousel Section -->
        @if($heroSlides->count() > 0)
            <section id="hero-section" class="relative overflow-hidden animate-fade-in-up">
                <div class="hero-carousel relative">
                    @foreach($heroSlides as $slide)
                        <div class="hero-slide {{ $loop->first ? 'active' : '' }}" data-slide-index="{{ $loop->index }}">
                            @if($slide->type === 'text')
                                <!-- Text Slide -->
                                <div class="gradient-bg text-white py-12 md:py-24">
                                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                        <div class="grid md:grid-cols-2 gap-8 items-center">
                                            <div class="space-y-6 text-center md:text-left">
                                                <h1 class="text-3xl md:text-6xl font-bold leading-tight">
                                                    {!! nl2br(e($slide->title ?: 'Produk Susu Terbaik untuk Anda')) !!}
                                                </h1>
                                                @if($slide->subtitle)
                                                    <p class="text-lg md:text-xl text-blue-100">
                                                        {{ $slide->subtitle }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="hidden md:block animate-slide-in">
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-0 bg-blue-300 rounded-full blur-3xl opacity-30 animate-pulse">
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
                                </div>
                            @else
                                <!-- Image Slide -->
                                <div class="relative h-[400px] md:h-[600px] bg-gray-900">
                                    <img src="{{ asset('storage/' . $slide->image_path) }}" alt="{{ $slide->title }}"
                                        class="w-full h-full object-cover opacity-90">
                                    @if($slide->title)
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="text-center text-white px-4">
                                                <h2 class="text-3xl md:text-5xl font-bold drop-shadow-lg">
                                                    {{ $slide->title }}
                                                </h2>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @if($heroSlides->count() > 1)
                        <!-- Navigation Arrows -->
                        <button onclick="prevSlide()"
                            class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg transition-all z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button onclick="nextSlide()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg transition-all z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Dots Navigation -->
                        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                            @foreach($heroSlides as $slide)
                                <button onclick="goToSlide({{ $loop->index }})"
                                    class="slide-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all {{ $loop->first ? 'active bg-white w-8' : '' }}">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <style>
                    .hero-slide {
                        display: none;
                        animation: fadeIn 0.5s ease-in;
                    }

                    .hero-slide.active {
                        display: block;
                    }

                    .slide-dot.active {
                        background: white;
                        width: 2rem;
                    }

                    @keyframes fadeIn {
                        from {
                            opacity: 0;
                        }

                        to {
                            opacity: 1;
                        }
                    }
                </style>

                <script>
                    let currentSlideIndex = 0;
                    const slides = document.querySelectorAll('.hero-slide');
                    const dots = document.querySelectorAll('.slide-dot');
                    const totalSlides = slides.length;
                    let autoplayInterval;

                    function showSlide(index) {
                        slides.forEach(slide => slide.classList.remove('active'));
                        dots.forEach(dot => {
                            dot.classList.remove('active', 'w-8');
                            dot.classList.add('w-3');
                        });

                        slides[index].classList.add('active');
                        if (dots[index]) {
                            dots[index].classList.add('active', 'w-8');
                            dots[index].classList.remove('w-3');
                        }
                    }

                    function nextSlide() {
                        currentSlideIndex = (currentSlideIndex + 1) % totalSlides;
                        showSlide(currentSlideIndex);
                        resetAutoplay();
                    }

                    function prevSlide() {
                        currentSlideIndex = (currentSlideIndex - 1 + totalSlides) % totalSlides;
                        showSlide(currentSlideIndex);
                        resetAutoplay();
                    }

                    function goToSlide(index) {
                        currentSlideIndex = index;
                        showSlide(currentSlideIndex);
                        resetAutoplay();
                    }

                    function startAutoplay() {
                        if (totalSlides > 1) {
                            autoplayInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
                        }
                    }

                    function resetAutoplay() {
                        clearInterval(autoplayInterval);
                        startAutoplay();
                    }

                    // Start autoplay on page load
                    startAutoplay();

                    // Pause autoplay on hover
                    document.querySelector('.hero-carousel')?.addEventListener('mouseenter', () => {
                        clearInterval(autoplayInterval);
                    });
                    document.querySelector('.hero-carousel')?.addEventListener('mouseleave', () => {
                        startAutoplay();
                    });
                </script>
            </section>
        @endif

        <!-- Quick Action Buttons Section -->
        <section class="bg-gray-50 py-3 border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Mobile: Full Width Buttons -->
                <div class="flex md:hidden gap-2">
                    <a href="#products"
                        class="flex-1 flex flex-col items-center justify-center gap-1 py-3 bg-white border border-gray-200 text-blue-600 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span class="text-xs font-medium">Belanja</span>
                    </a>
                    @if($products->whereNotNull('active_discount')->count() > 0)
                        <a href="#promo-section"
                            class="flex-1 flex flex-col items-center justify-center gap-1 py-3 bg-white border border-gray-200 text-blue-600 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                            <span class="text-xs font-medium">Promo</span>
                        </a>
                    @endif
                    @auth
                        <a href="{{ route('transactions.index') }}"
                            class="flex-1 flex flex-col items-center justify-center gap-1 py-3 bg-white border border-gray-200 text-blue-600 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-xs font-medium">Transaksi</span>
                        </a>
                    @endauth
                </div>

                <!-- Desktop: Wider Buttons -->
                <div class="hidden md:flex gap-3">
                    <a href="#products"
                        class="flex-1 justify-center px-8 py-3 bg-white border border-gray-200 text-blue-600 rounded-lg font-medium hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Belanja Sekarang</span>
                    </a>
                    @if($products->whereNotNull('active_discount')->count() > 0)
                        <a href="#promo-section"
                            class="flex-1 justify-center px-8 py-3 bg-white border border-gray-200 text-blue-600 rounded-lg font-medium hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                            <span>Produk Promo</span>
                        </a>
                    @endif
                    @auth
                        <a href="{{ route('transactions.index') }}"
                            class="flex-1 justify-center px-8 py-3 bg-white border border-gray-200 text-blue-600 rounded-lg font-medium hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Transaksi Saya</span>
                        </a>
                    @endauth
                </div>
            </div>
        </section>


        @if($products->whereNotNull('active_discount')->count() > 0)
            <!-- Flash Sale Section -->
            <section class="py-6 md:py-8 bg-red-600 animate-fade-in-up mt-2 mb-2" style="animation-delay: 0.2s">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                        <div class="flex items-center gap-3">
                            <h2 class="text-2xl md:text-3xl font-black text-white italic tracking-wide flex items-center gap-2">
                                <svg class="w-8 h-8 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                                </svg>
                                FLASH SALE
                            </h2>
                            <div class="flex items-center gap-1 bg-white/20 px-3 py-1.5 rounded-lg text-white font-mono font-bold text-lg backdrop-blur-sm">
                                <span id="fs-hours">02</span><span class="text-red-200 animate-pulse">:</span><span id="fs-mins">45</span><span class="text-red-200 animate-pulse">:</span><span id="fs-secs">10</span>
                            </div>
                        </div>
                        <a href="#products" class="text-white font-bold text-sm flex items-center gap-1 hover:text-red-100 transition-colors">
                            Lihat Semua
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <!-- Horizontal Scroll for Flash Sale -->
                    <div class="flex overflow-x-auto gap-4 pb-4 snap-x hide-scrollbar">
                        @foreach($products->whereNotNull('active_discount')->take(4) as $product)
                            <div onclick="window.location.href='{{ route('products.show', $product->id) }}'"
                                class="flex-none w-[160px] md:w-[220px] product-card group bg-white rounded-2xl overflow-hidden shadow-xl cursor-pointer snap-start relative border-2 border-transparent hover:border-red-400 transition-all">
                                
                                <div class="absolute top-0 left-0 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-br-xl z-20 shadow-md">
                                    Segera Habis!
                                </div>

                                <div class="relative">
                                    <div class="absolute top-2 right-2 z-10 flex flex-col gap-1">
                                        @if($product->active_discount)
                                            <span class="px-2 py-1 bg-yellow-400 text-red-900 rounded-lg text-xs font-black shadow-sm transform rotate-3 scale-110">
                                                @if($product->active_discount->discount_type === 'PERCENTAGE')
                                                    -{{ number_format($product->active_discount->discount_value, 0) }}%
                                                @else
                                                    -{{ $product->active_discount->discount_value >= 1000 ? number_format($product->active_discount->discount_value / 1000, 0) . 'K' : number_format($product->active_discount->discount_value, 0, ',', '.') }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>

                                    <div class="image-container skeleton aspect-square bg-gray-50 overflow-hidden relative">
                                        <img src="{{ $product->image && str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image }}"
                                            alt="{{ $product->name }}"
                                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                                            style="filter: none !important; background-color: transparent !important;"
                                            onload="this.classList.add('loaded'); this.parentElement.classList.remove('skeleton');"
                                            onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image'; this.classList.add('loaded'); this.parentElement.classList.remove('skeleton');">
                                    </div>
                                </div>

                                <div class="p-4">
                                    <h3 class="text-sm font-bold text-gray-900 mb-2 line-clamp-2 h-10 leading-tight">{{ $product->name }}</h3>
                                    
                                    <div class="mb-3">
                                        @if($product->active_discount)
                                            <p class="text-[10px] md:text-xs text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                            <p class="text-base md:text-lg font-black text-red-600 leading-none">Rp {{ number_format($product->discounted_price, 0, ',', '.') }}</p>
                                        @else
                                            <p class="text-base md:text-lg font-black text-red-600 leading-none">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        @endif
                                    </div>
                                    
                                    <!-- Stock Progress Bar -->
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                                        <div class="bg-red-500 h-2 rounded-full" style="width: 85%"></div>
                                    </div>
                                    <div class="text-[10px] text-gray-500 font-semibold mb-3">Sisa 5 produk!</div>

                                    <button onclick="event.stopPropagation(); addToCart({{ $product->id }})"
                                        class="w-full py-2 bg-red-600 text-white rounded-xl font-bold text-sm flex items-center justify-center gap-2 hover:bg-red-700 active:scale-95 transition-all shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Sikat!
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Promo Mingguan Section -->
            <section id="promo-section" class="py-6 md:py-8 animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900">Spesial Promo Mingguan 🤑</h2>
                        </div>
                        <a href="#products" onclick="selectCategory('promo', 'Spesial Promo')" class="text-blue-600 font-bold text-sm flex items-center gap-1">
                            Lihat Semua
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <!-- Horizontal Scroll for Promo (Astro style) -->
                    <div class="flex overflow-x-auto gap-4 pb-4 snap-x hide-scrollbar">
                        @foreach($products->whereNotNull('active_discount')->take(5) as $product)
                            <div onclick="window.location.href='{{ route('products.show', $product->id) }}'"
                                class="flex-none w-[160px] md:w-[200px] product-card group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 cursor-pointer snap-start"
                                style="animation-delay: {{ ($loop->iteration - 1) * 0.1 }}s">
                                
                                <div class="relative">
                                    <div class="absolute top-2 left-2 z-10 flex flex-col gap-1">
                                        @if($product->active_discount)
                                            <span class="px-2 py-0.5 bg-red-500 text-white rounded text-[10px] md:text-xs font-bold shadow-sm animate-pulse">
                                                @if($product->active_discount->discount_type === 'PERCENTAGE')
                                                    -{{ number_format($product->active_discount->discount_value, 0) }}%
                                                @else
                                                    -{{ $product->active_discount->discount_value >= 1000 ? number_format($product->active_discount->discount_value / 1000, 0) . 'K' : number_format($product->active_discount->discount_value, 0, ',', '.') }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>

                                    <div class="image-container skeleton aspect-square bg-gray-50 overflow-hidden relative">
                                        <img src="{{ $product->image && str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image }}"
                                            alt="{{ $product->name }}"
                                            class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-500"
                                            style="filter: none !important; background-color: transparent !important;"
                                            onload="this.classList.add('loaded'); this.parentElement.classList.remove('skeleton');"
                                            onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image'; this.classList.add('loaded'); this.parentElement.classList.remove('skeleton'); console.error('Image failing to load:', this.src);">

                                        <button onclick="event.stopPropagation(); toggleWishlist({{ $product->id }}, this)"
                                            class="absolute top-2 right-2 z-10 p-1.5 bg-white/90 backdrop-blur-sm rounded-full shadow-sm transition-all duration-300 hover:bg-red-50">
                                            @php
                                                $isWishlisted = Auth::check() && $product->isWishlistedBy(Auth::user());
                                            @endphp
                                            <svg class="w-4 h-4 {{ $isWishlisted ? 'text-red-500' : 'text-gray-400' }} hover:text-red-500"
                                                fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="p-3">
                                    <h3 class="text-xs md:text-sm font-semibold text-gray-900 mb-1 line-clamp-2 h-8 leading-tight">{{ $product->name }}</h3>
                                    
                                    <div class="mt-2 flex items-end justify-between gap-1">
                                        <div class="flex-1">
                                            @if($product->active_discount)
                                                <p class="text-[9px] md:text-[10px] text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                                <p class="text-sm md:text-base font-bold text-gray-900 leading-none">Rp {{ number_format($product->discounted_price, 0, ',', '.') }}</p>
                                            @else
                                                <p class="text-sm md:text-base font-bold text-gray-900 leading-none">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                            @endif
                                        </div>
                                        
                                        <button onclick="event.stopPropagation(); addToCart({{ $product->id }})"
                                            class="flex-shrink-0 w-7 h-7 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 hover:scale-110 active:scale-95 transition-all shadow-sm"
                                            title="Tambah ke Keranjang">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- Category Horizontal List -->
        <section class="py-4 md:py-6 bg-white animate-fade-in-up border-y border-gray-100 sticky top-[64px] z-30" style="animation-delay: 0.3s">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-4">Belanja Berdasarkan Kategori</h2>
                <div class="flex overflow-x-auto gap-4 md:gap-6 pb-2 hide-scrollbar snap-x">
                    <button onclick="selectCategory('all', 'Semua Produk')" class="flex flex-col items-center gap-2 group snap-start">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-blue-50 border border-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-100 group-hover:scale-105 transition-all">
                            <span class="text-xl md:text-2xl font-bold text-blue-600">All</span>
                        </div>
                        <span class="text-[10px] md:text-xs font-semibold text-center text-gray-700 group-hover:text-blue-600 max-w-[80px]">Semua</span>
                    </button>
                    <!-- Promo Category Chip -->
                    <button onclick="selectCategory('promo', 'Spesial Promo')" class="flex flex-col items-center gap-2 group snap-start">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-red-50 border border-red-100 rounded-full flex items-center justify-center group-hover:bg-red-100 group-hover:scale-105 transition-all relative overflow-hidden">
                            <div class="absolute inset-0 bg-red-500 opacity-10 animate-pulse"></div>
                            <span class="text-xl md:text-2xl font-bold text-red-600">%</span>
                        </div>
                        <span class="text-[10px] md:text-xs font-bold text-center text-red-600 max-w-[80px]">Promo</span>
                    </button>
                    @foreach($categories as $category)
                        <button onclick="selectCategory('{{ $category->id }}', '{{ $category->name }}')" class="flex flex-col items-center gap-2 group snap-start">
                            <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center group-hover:bg-blue-50 group-hover:border-blue-200 group-hover:scale-105 transition-all">
                                <span class="text-xl md:text-2xl font-bold text-gray-600 group-hover:text-blue-600">{{ substr($category->name, 0, 1) }}</span>
                            </div>
                            <span class="text-[10px] md:text-xs font-semibold text-center text-gray-700 group-hover:text-blue-600 max-w-[80px] leading-tight line-clamp-2">{{ $category->name }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
            <style>
                .hide-scrollbar::-webkit-scrollbar {
                    display: none;
                }
                .hide-scrollbar {
                    -ms-overflow-style: none;
                    scrollbar-width: none;
                }
            </style>
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





        // Flash Sale Countdown Logic
        function startFlashSaleTimer() {
            let hours = 2;
            let minutes = 45;
            let seconds = 10;
            
            setInterval(() => {
                seconds--;
                if (seconds < 0) {
                    seconds = 59;
                    minutes--;
                    if (minutes < 0) {
                        minutes = 59;
                        hours--;
                        if (hours < 0) {
                            hours = 2; // Reset for demo purposes
                        }
                    }
                }
                
                const hEl = document.getElementById('fs-hours');
                const mEl = document.getElementById('fs-mins');
                const sEl = document.getElementById('fs-secs');
                
                if (hEl && mEl && sEl) {
                    hEl.textContent = hours.toString().padStart(2, '0');
                    mEl.textContent = minutes.toString().padStart(2, '0');
                    sEl.textContent = seconds.toString().padStart(2, '0');
                }
            }, 1000);
        }
        
        // Start timer
        startFlashSaleTimer();

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
                if (heroSection) heroSection.classList.add('hidden');
                if (promoSection) promoSection.classList.add('hidden');
            } else {
                if (heroSection) heroSection.classList.remove('hidden');
                if (promoSection) promoSection.classList.remove('hidden');
            }

            // Show loading state
            if (gridContainer) {
                gridContainer.style.opacity = '0.5';
                gridContainer.style.pointerEvents = 'none';
            }

            try {
                const params = new URLSearchParams({
                    category: currentCategoryId,
                    q: currentSearchQuery
                });

                const response = await fetch(`/products/search?${params.toString()}`);
                const html = await response.text();

                if (gridContainer) {
                    gridContainer.innerHTML = html;
                }
            } catch (error) {
                console.error('Error fetching products:', error);
            } finally {
                if (gridContainer) {
                    gridContainer.style.opacity = '1';
                    gridContainer.style.pointerEvents = 'auto';
                }
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
            applyFilters();

            // Smooth scroll to product section
            const productsSection = document.getElementById('products');
            if (productsSection) {
                productsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    </script>
@endsection