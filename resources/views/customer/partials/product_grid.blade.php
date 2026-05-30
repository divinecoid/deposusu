@if($products->isEmpty())
    <div class="col-span-full py-20 text-center">
        <svg class="w-20 h-20 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Produk tidak ditemukan</h3>
        <p class="text-gray-500">Coba gunakan kata kunci lain atau pilih kategori yang berbeda.</p>
    </div>
@else
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-6">
        @foreach($products as $product)
            <div onclick="window.location.href='{{ route('products.show', $product->id) }}'"
                class="product-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 cursor-pointer"
                data-category-ids="{{ $product->categories->pluck('id')->implode(',') }}"
                style="animation-delay: {{ ($loop->iteration - 1) * 0.05 }}s">
                
                <div class="relative group">
                    <!-- Promo Tag -->
                    @if($product->active_discount)
                        <div class="absolute top-2 left-2 z-10">
                            <span class="px-2 py-1 bg-red-500 text-white text-[10px] md:text-xs font-bold rounded-md shadow-sm">
                                @if($product->active_discount->discount_type === 'PERCENTAGE')
                                    -{{ number_format($product->active_discount->discount_value, 0) }}%
                                @else
                                    -{{ $product->active_discount->discount_value >= 1000 ? number_format($product->active_discount->discount_value / 1000, 0) . 'K' : number_format($product->active_discount->discount_value, 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                    @endif

                    <!-- Big Photo -->
                    <div class="image-container skeleton aspect-square bg-gray-50 overflow-hidden relative">
                        <img src="{{ $product->image && str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                            style="filter: none !important; background-color: transparent !important;"
                            onload="this.classList.add('loaded'); this.parentElement.classList.remove('skeleton');"
                            onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image'; this.classList.add('loaded'); this.parentElement.classList.remove('skeleton');">
                        
                        <!-- Wishlist Button Overlay -->
                        <button onclick="event.stopPropagation(); toggleWishlist({{ $product->id }}, this)"
                            class="absolute top-2 right-2 z-10 p-2 bg-white/90 backdrop-blur-sm rounded-full shadow-sm transition-all duration-300 hover:bg-red-50">
                            @php
                                $isWishlisted = Auth::check() && $product->isWishlistedBy(Auth::user());
                            @endphp
                            <svg class="w-4 h-4 md:w-5 md:h-5 {{ $isWishlisted ? 'text-red-500' : 'text-gray-400' }} hover:text-red-500"
                                fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-3 md:p-4">
                    <!-- Title -->
                    <h3 class="text-sm md:text-base font-semibold text-gray-900 mb-1 line-clamp-2 h-10 md:h-12 leading-tight">
                        {{ $product->name }}
                    </h3>

                    <!-- Price & Action -->
                    <div class="mt-2 flex items-end justify-between gap-2">
                        <div class="flex-1">
                            @if($product->active_discount)
                                <p class="text-[10px] md:text-xs text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p class="text-base md:text-xl font-bold text-gray-900 leading-none">Rp {{ number_format($product->discounted_price, 0, ',', '.') }}</p>
                            @else
                                <p class="text-base md:text-xl font-bold text-gray-900 leading-none">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            @endif
                        </div>
                        
                        <!-- Quick Add Button -->
                        <button onclick="event.stopPropagation(); addToCart({{ $product->id }})"
                            class="flex-shrink-0 w-8 h-8 md:w-10 md:h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 hover:scale-110 active:scale-95 transition-all shadow-md shadow-blue-500/30"
                            title="Tambah ke Keranjang">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif