@extends('layouts.customer')

@section('title', $product->name . ' - DEPOSUSU')

@section('content')
@php
    $hasDiscount = (bool) $product->active_discount;
    $finalPrice = $hasDiscount ? $product->discounted_price : $product->price;
    $saving = $hasDiscount ? ($product->price - $finalPrice) : 0;
    $percentOff = $hasDiscount && $product->price > 0 ? round(($saving / $product->price) * 100) : 0;
    $stock = (int) $product->stock;
    $lowStockAt = (int) ($product->low_stock_threshold ?: 5);
    $isOut = $stock <= 0;
    $isLow = !$isOut && $stock <= $lowStockAt;
    $isWishlisted = Auth::check() && $product->isWishlistedBy(Auth::user());
@endphp

<div class="min-h-screen bg-slate-50 pb-28 md:pb-12">

    <!-- Breadcrumb -->
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 md:py-4" aria-label="Breadcrumb">
        <ol class="flex items-center gap-2 text-xs md:text-sm text-slate-500 overflow-x-auto hide-scrollbar">
            <li><a href="{{ route('home') }}" class="hover:text-brand-600 whitespace-nowrap">Beranda</a></li>
            @if($product->categories->isNotEmpty())
                <li aria-hidden="true">/</li>
                <li class="whitespace-nowrap">{{ $product->categories->first()->name }}</li>
            @endif
            <li aria-hidden="true">/</li>
            <li class="text-slate-900 font-medium truncate max-w-[45vw] md:max-w-none">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden grid md:grid-cols-2">

            <!-- Image -->
            <div class="relative bg-slate-50 p-6 md:p-10 flex items-center justify-center">
                @if($hasDiscount)
                    <span class="absolute top-4 left-4 z-10 px-3 py-1.5 rounded-xl bg-rose-600 text-white text-sm font-bold shadow-sm">
                        Hemat {{ $percentOff }}%
                    </span>
                @endif

                <button type="button" onclick="toggleWishlist({{ $product->id }}, this)"
                    aria-label="{{ $isWishlisted ? 'Hapus dari wishlist' : 'Simpan ke wishlist' }}"
                    class="absolute top-4 right-4 z-10 p-3 bg-white rounded-full shadow-sm border border-slate-100 hover:bg-rose-50 transition-colors">
                    <svg class="w-5 h-5 {{ $isWishlisted ? 'text-rose-500' : 'text-slate-400' }}"
                        fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>

                <img src="{{ $product->image ? (str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image) : 'https://placehold.co/600x600/f1f5f9/94a3b8?text=No+Image' }}"
                    alt="{{ $product->name }}" fetchpriority="high"
                    class="w-full max-w-sm aspect-square object-contain {{ $isOut ? 'opacity-60' : '' }}"
                    onerror="this.onerror=null; this.src='https://placehold.co/600x600/f1f5f9/94a3b8?text=No+Image';">

                @if($isOut)
                    <span class="absolute inset-x-0 bottom-8 mx-auto w-fit px-4 py-2 rounded-xl bg-slate-900/85 text-white text-sm font-bold">
                        Stok Habis
                    </span>
                @endif
            </div>

            <!-- Info -->
            <div class="p-6 md:p-10 flex flex-col">
                @if($product->categories->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        @foreach($product->categories as $category)
                            <span class="px-2.5 py-1 bg-brand-50 text-brand-700 text-[11px] font-bold rounded-full">{{ $category->name }}</span>
                        @endforeach
                    </div>
                @endif

                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 leading-tight">{{ $product->name }}</h1>

                <!-- Price -->
                <div class="mt-5 pb-5 border-b border-slate-100">
                    @if($hasDiscount)
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm text-slate-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-600 text-xs font-bold">
                                Hemat Rp {{ number_format($saving, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                    <p class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Rp {{ number_format($finalPrice, 0, ',', '.') }}
                    </p>

                    <div class="mt-3">
                        @if($isOut)
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-500 text-sm font-semibold">
                                Stok habis — cek lagi nanti
                            </span>
                        @elseif($isLow)
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                Tinggal {{ $stock }} item
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Stok tersedia ({{ $stock }})
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Delivery promise -->
                <ul class="mt-5 space-y-2.5 text-sm text-slate-600">
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Dikirim cepat ke area layanan kami
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Bayar dengan QRIS, transfer, e-wallet, atau COD
                    </li>
                </ul>

                <!-- Description -->
                <div class="mt-6">
                    <h2 class="text-base font-bold text-slate-900 mb-2">Deskripsi Produk</h2>
                    <div class="text-sm text-slate-600 leading-relaxed">
                        {!! nl2br(e($product->description ?: 'Belum ada deskripsi untuk produk ini.')) !!}
                    </div>
                </div>

                <!-- Desktop actions -->
                <div class="hidden md:flex items-center gap-4 mt-8 pt-6 border-t border-slate-100">
                    @if($isOut)
                        <button type="button" disabled
                            class="flex-1 h-12 rounded-xl bg-slate-100 text-slate-400 font-bold cursor-not-allowed">
                            Stok Habis
                        </button>
                    @else
                        <div class="flex items-center h-12 border border-slate-200 rounded-xl overflow-hidden">
                            <button type="button" onclick="updatePageQty(-1)" aria-label="Kurangi jumlah"
                                class="w-12 h-full flex items-center justify-center text-slate-500 hover:bg-slate-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                            </button>
                            <input type="number" id="desktop-qty" value="1" min="1" max="{{ $stock }}"
                                aria-label="Jumlah"
                                class="w-14 text-center font-bold text-slate-900 border-none focus:ring-0 bg-transparent">
                            <button type="button" onclick="updatePageQty(1)" aria-label="Tambah jumlah"
                                class="w-12 h-full flex items-center justify-center text-slate-500 hover:bg-slate-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                        <button type="button" onclick="addToCartFromPage(false, this)"
                            class="flex-1 h-12 rounded-xl border-2 border-brand-600 text-brand-600 font-bold hover:bg-brand-50 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Keranjang
                        </button>
                        <button type="button" onclick="addToCartFromPage(true, this)"
                            class="flex-1 h-12 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 transition-colors">
                            Beli Sekarang
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Related -->
        @if($relatedProducts->count() > 0)
            <section class="mt-10 mb-6">
                <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-4">Produk Serupa</h2>
                <div class="flex overflow-x-auto gap-3 md:gap-4 pb-2 snap-x hide-scrollbar">
                    @foreach($relatedProducts as $related)
                        @php
                            $relatedHasDiscount = (bool) $related->active_discount;
                            $relatedPrice = $relatedHasDiscount ? $related->discounted_price : $related->price;
                        @endphp
                        <div class="product-card flex-none w-[150px] md:w-[190px] bg-white rounded-2xl border border-slate-200/80 overflow-hidden snap-start hover:shadow-lg hover:shadow-slate-900/5 transition-all">
                            <a href="{{ route('products.show', $related->id) }}" class="block group">
                                <div class="aspect-square bg-slate-50 overflow-hidden">
                                    <img src="{{ $related->image ? (str_starts_with($related->image, 'storage/') ? asset($related->image) : $related->image) : 'https://placehold.co/400x400/f1f5f9/94a3b8?text=No+Image' }}"
                                        alt="{{ $related->name }}" loading="lazy" decoding="async"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        onerror="this.onerror=null; this.src='https://placehold.co/400x400/f1f5f9/94a3b8?text=No+Image';">
                                </div>
                                <div class="p-3">
                                    <h3 class="text-[13px] font-semibold text-slate-900 line-clamp-2 leading-snug">{{ $related->name }}</h3>
                                    @if($relatedHasDiscount)
                                        <p class="mt-1.5 text-[11px] text-slate-400 line-through">Rp {{ number_format($related->price, 0, ',', '.') }}</p>
                                    @endif
                                    <p class="{{ $relatedHasDiscount ? '' : 'mt-1.5' }} text-sm font-bold text-slate-900">Rp {{ number_format($relatedPrice, 0, ',', '.') }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <!-- Mobile sticky action bar (sits above the bottom navigation) -->
    <div class="md:hidden fixed bottom-16 inset-x-0 bg-white border-t border-slate-200 px-4 py-3 z-40 flex items-center gap-3">
        @if($isOut)
            <button type="button" disabled class="flex-1 h-12 rounded-xl bg-slate-100 text-slate-400 font-bold">Stok Habis</button>
        @else
            <div class="flex items-center h-12 border border-slate-200 rounded-xl overflow-hidden shrink-0">
                <button type="button" onclick="updatePageQty(-1)" aria-label="Kurangi jumlah"
                    class="w-10 h-full flex items-center justify-center text-slate-500 active:bg-slate-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                </button>
                <input type="number" id="mobile-qty" value="1" min="1" max="{{ $stock }}" aria-label="Jumlah"
                    class="w-10 h-full text-center font-bold border-none focus:ring-0 bg-transparent text-slate-900">
                <button type="button" onclick="updatePageQty(1)" aria-label="Tambah jumlah"
                    class="w-10 h-full flex items-center justify-center text-slate-500 active:bg-slate-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>
            <button type="button" onclick="addToCartFromPage(false, this)" aria-label="Tambah ke keranjang"
                class="w-12 h-12 shrink-0 rounded-xl border-2 border-brand-600 text-brand-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </button>
            <button type="button" onclick="addToCartFromPage(true, this)"
                class="flex-1 h-12 rounded-xl bg-brand-600 text-white font-bold">Beli Sekarang</button>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    const maxStock = {{ $stock }};

    function currentQty() {
        const desktop = document.getElementById('desktop-qty');
        const mobile = document.getElementById('mobile-qty');
        const source = (desktop && desktop.offsetParent !== null) ? desktop : (mobile || desktop);
        return Math.min(maxStock, Math.max(1, parseInt(source?.value, 10) || 1));
    }

    function updatePageQty(change) {
        const next = Math.min(maxStock, Math.max(1, currentQty() + change));
        ['desktop-qty', 'mobile-qty'].forEach(id => {
            const input = document.getElementById(id);
            if (input) input.value = next;
        });
        if (next === maxStock && change > 0) {
            showNotification(`Maksimal ${maxStock} item tersedia`, 'info');
        }
    }

    // Keep the two inputs in sync when typed into directly.
    ['desktop-qty', 'mobile-qty'].forEach(id => {
        document.getElementById(id)?.addEventListener('change', () => updatePageQty(0));
    });

    async function addToCartFromPage(goToCheckout, button) {
        const added = await addToCart({{ $product->id }}, currentQty(), button);
        if (added && goToCheckout) {
            window.location.href = '{{ route('checkout.index') }}';
        }
    }
</script>
@endpush
