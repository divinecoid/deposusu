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
            <div onclick="openQuickView({{ json_encode($product) }})"
                class="product-card bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl cursor-pointer"
                data-category-id="{{ $product->category_id }}" style="animation-delay: {{ ($loop->iteration - 1) * 0.05 }}s">
                <div class="relative group">
                    <!-- Wishlist Button -->
                    <button onclick="event.stopPropagation(); toggleWishlist({{ $product->id }}, this)"
                        class="absolute top-3 right-3 z-10 p-2 bg-white rounded-full shadow-md transition-all duration-300 hover:bg-red-50">
                        @php
                            $isWishlisted = Auth::check() && $product->isWishlistedBy(Auth::user());
                        @endphp
                        <svg class="w-5 h-5 {{ $isWishlisted ? 'text-red-500' : 'text-gray-400' }} hover:text-red-500"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                    <div class="image-container skeleton aspect-square bg-gray-50 p-6 rounded-xl overflow-hidden">
                        <img src="{{ $product->image && str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                            style="filter: none !important; background-color: transparent !important;"
                            onload="this.classList.add('loaded'); this.parentElement.classList.remove('skeleton');"
                            onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image'; this.classList.add('loaded'); this.parentElement.classList.remove('skeleton');">
                    </div>
                </div>

                <div class="p-4">
                    <div class="mb-2">
                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-600 text-xs font-semibold rounded">
                            {{ $product->category->name ?? 'Uncategorized' }}
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
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100/50">
                        <button onclick="event.stopPropagation(); addToCart({{ $product->id }})"
                            class="w-full py-3 md:py-3.5 bg-blue-600 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/10 hover:bg-blue-700 transform hover:scale-[1.02] active:scale-95 transition-all duration-300 flex items-center justify-center gap-2.5 text-sm">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif