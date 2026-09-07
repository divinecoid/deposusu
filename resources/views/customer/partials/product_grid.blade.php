@php
    // When `bare` is true only the cards are rendered, so the "load more"
    // action can append them into the existing grid.
    $bare = $bare ?? false;
@endphp

@if($products->isEmpty() && !$bare)
    <div class="py-20 text-center">
        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-1">Produk tidak ditemukan</h3>
        <p class="text-slate-500 text-sm max-w-sm mx-auto">Coba kata kunci lain, atau lihat semua produk kami.</p>
        <button type="button" onclick="resetFilters()"
            class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors">
            Lihat semua produk
        </button>
    </div>
@else
    @if(!$bare)
        <div id="product-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-5">
    @endif

    @foreach($products as $product)
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

        <div class="product-card group relative flex flex-col bg-white rounded-2xl border border-slate-200/80 overflow-hidden hover:border-blue-200 hover:shadow-lg hover:shadow-slate-900/5 transition-all">

            <!-- Media -->
            <div class="relative aspect-square bg-slate-50 overflow-hidden {{ $isOut ? 'opacity-60' : '' }}">
                <a href="{{ route('products.show', $product->id) }}"
                   class="block w-full h-full"
                   aria-label="Lihat detail {{ $product->name }}">
                    <img src="{{ $product->image ? (str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image) : 'https://placehold.co/400x400/f1f5f9/94a3b8?text=No+Image' }}"
                        alt="{{ $product->name }}"
                        loading="lazy" decoding="async"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        onerror="this.onerror=null; this.src='https://placehold.co/400x400/f1f5f9/94a3b8?text=No+Image';">
                </a>

                @if($hasDiscount)
                    <span class="absolute top-2 left-2 z-10 px-2 py-1 rounded-lg bg-rose-600 text-white text-[11px] font-bold shadow-sm">
                        -{{ $percentOff }}%
                    </span>
                @endif

                @if($isOut)
                    <div class="absolute inset-0 flex items-center justify-center bg-white/60">
                        <span class="px-3 py-1.5 rounded-lg bg-slate-900/85 text-white text-xs font-bold">Stok Habis</span>
                    </div>
                @endif

                <button type="button"
                    onclick="toggleWishlist({{ $product->id }}, this)"
                    aria-label="{{ $isWishlisted ? 'Hapus dari wishlist' : 'Simpan ke wishlist' }}"
                    class="absolute top-2 right-2 z-10 p-2 bg-white/95 backdrop-blur rounded-full shadow-sm hover:bg-rose-50 transition-colors">
                    <svg class="w-4 h-4 {{ $isWishlisted ? 'text-rose-500' : 'text-slate-400' }}"
                        fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="flex flex-col flex-1 p-3 md:p-4">
                <a href="{{ route('products.show', $product->id) }}"
                   class="text-sm md:text-[15px] font-semibold text-slate-900 line-clamp-2 leading-snug hover:text-blue-600 transition-colors">
                    {{ $product->name }}
                </a>

                <div class="mt-2">
                    @if($hasDiscount)
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-slate-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            <span class="text-[11px] font-semibold text-emerald-600">Hemat {{ number_format($saving, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <p class="text-base md:text-lg font-bold text-slate-900 leading-tight">
                        Rp {{ number_format($finalPrice, 0, ',', '.') }}
                    </p>
                </div>

                @if($isLow)
                    <div class="mt-2">
                        <div class="h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full bg-amber-500"
                                 style="width: {{ max(8, min(100, ($stock / max(1, $lowStockAt)) * 100)) }}%"></div>
                        </div>
                        <p class="mt-1 text-[11px] font-semibold text-amber-600">Sisa {{ $stock }} item</p>
                    </div>
                @endif

                <div class="mt-auto pt-3">
                    @if($isOut)
                        <button type="button" disabled
                            class="w-full h-9 rounded-xl bg-slate-100 text-slate-400 text-sm font-semibold cursor-not-allowed">
                            Stok Habis
                        </button>
                    @else
                        <button type="button"
                            onclick="addToCart({{ $product->id }}, 1, this)"
                            class="w-full h-9 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 active:scale-[0.98] transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                            </svg>
                            Keranjang
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    @if(!$bare)
        </div>
    @endif
@endif
