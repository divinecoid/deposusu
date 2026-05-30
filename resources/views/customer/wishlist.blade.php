@extends('layouts.customer')

@push('html_attr')
    translate="no"
@endpush

@push('head')
    <meta name="google" content="notranslate">
@endpush

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Wishlist <span
                            class="text-blue-600">Saya</span></h1>
                    <p class="text-gray-500 mt-2">Daftar produk yang Anda simpan untuk dibeli nanti.</p>
                </div>
                <a href="{{ route('home') }}"
                    class="flex items-center gap-2 text-blue-600 font-bold hover:text-blue-700 transition-colors self-start md:self-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Lanjut Belanja
                </a>
            </div>

            @if($wishlistItems->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center shadow-xl shadow-blue-500/5 border border-white">
                    <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Wishlist Anda masih kosong</h2>
                    <p class="text-gray-500 max-w-md mx-auto mb-8">Anda belum menambahkan produk apa pun ke wishlist. Mulai
                        jelajahi produk kami dan simpan yang Anda suka!</p>
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center px-8 py-3 bg-blue-600 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 transform hover:scale-105 transition-all duration-300">
                        Jelajahi Produk
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($wishlistItems as $product)
                        <div onclick="window.location.href='{{ route('products.show', $product->id) }}'"
                            class="product-card bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl cursor-pointer relative group">

                            <!-- Remove from Wishlist Button -->
                            <button onclick="event.stopPropagation(); toggleWishlist({{ $product->id }}, this)"
                                class="absolute top-3 right-3 z-10 p-2 bg-white rounded-full shadow-md hover:bg-red-50 transition-all duration-300">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>

                            <div class="image-container aspect-square bg-gray-50 p-6">
                                <img src="{{ $product->image && str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                                    onerror="this.src='https://placehold.co/400x400?text=No+Image'">
                            </div>

                            <div class="p-4">
                                <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">{{ $product->categories->pluck('name')->implode(', ') ?: 'Uncategorized' }}</span>
                                <h3 class="text-sm font-bold text-gray-900 mt-1 line-clamp-1 capitalize">{{ $product->name }}</h3>

                                <div class="mt-2 flex items-center justify-between">
                                    <div class="flex flex-col">
                                        @if($product->active_discount)
                                            <span class="text-xs text-gray-400 line-through">Rp
                                                {{ number_format($product->price, 0, ',', '.') }}</span>
                                            <span class="text-sm font-extrabold text-blue-600">Rp
                                                {{ number_format($product->discounted_price, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-sm font-extrabold text-gray-900">Rp
                                                {{ number_format($product->price, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-100/50">
                                    <button onclick="event.stopPropagation(); addToCart({{ $product->id }})"
                                        class="w-full py-2.5 bg-blue-600 text-white rounded-xl font-bold shadow-md hover:bg-blue-700 transition-all duration-300 flex items-center justify-center gap-2 text-xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection