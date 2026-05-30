@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-gray-50 pb-24 md:pb-12">
    <!-- Breadcrumb (Desktop) -->
    <div class="hidden md:block max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex text-sm text-gray-500 font-medium">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ $product->name }}</span>
        </nav>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 md:mt-6">
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden flex flex-col md:flex-row">
            
            <!-- Left: Product Image -->
            <div class="w-full md:w-5/12 bg-gray-100 relative p-4 md:p-8 flex items-center justify-center">
                @if($product->active_discount)
                    <div class="absolute top-4 left-4 z-10 bg-red-500 text-white px-3 py-1.5 rounded-lg font-bold shadow-md animate-pulse">
                        @if($product->active_discount->discount_type === 'PERCENTAGE')
                            Diskon {{ number_format($product->active_discount->discount_value, 0) }}%
                        @else
                            Potongan Rp {{ number_format($product->active_discount->discount_value, 0, ',', '.') }}
                        @endif
                    </div>
                @endif
                
                <button onclick="toggleWishlist({{ $product->id }}, this)" class="absolute top-4 right-4 z-10 p-3 bg-white/80 backdrop-blur-sm rounded-full shadow-sm hover:bg-red-50 transition-colors">
                    @php $isWishlisted = Auth::check() && $product->isWishlistedBy(Auth::user()); @endphp
                    <svg class="w-6 h-6 {{ $isWishlisted ? 'text-red-500' : 'text-gray-400' }} hover:text-red-500 transition-colors" fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>

                <img src="{{ $product->image && str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image }}" 
                     alt="{{ $product->name }}" 
                     class="w-full max-w-sm h-auto object-contain drop-shadow-xl transform hover:scale-105 transition-transform duration-500"
                     onerror="this.src='https://placehold.co/600x600?text=No+Image'">
            </div>

            <!-- Right: Product Info -->
            <div class="w-full md:w-7/12 p-6 md:p-10 flex flex-col justify-between">
                <div>
                    <!-- Title & Category -->
                    <div class="mb-2">
                        @foreach($product->categories as $category)
                            <span class="inline-block px-3 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-full mb-2 mr-2">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>
                    <h1 class="text-2xl md:text-4xl font-black text-gray-900 leading-tight mb-4">
                        {{ $product->name }}
                    </h1>
                    
                    <!-- Price & Stock -->
                    <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-100">
                        <div>
                            @if($product->active_discount)
                                <p class="text-sm text-gray-400 line-through mb-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p class="text-3xl md:text-5xl font-black text-blue-600 tracking-tight">Rp {{ number_format($product->discounted_price, 0, ',', '.') }}</p>
                            @else
                                <p class="text-3xl md:text-5xl font-black text-blue-600 tracking-tight">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="inline-flex items-center gap-2 bg-green-50 text-green-700 px-3 py-2 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span class="font-bold text-sm">Stok Tersedia</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 font-medium">Sisa {{ $product->stock }} item</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Deskripsi Produk</h3>
                        <div class="prose prose-blue prose-sm text-gray-600 max-w-none leading-relaxed">
                            {!! nl2br(e($product->description ?: 'Belum ada deskripsi untuk produk ini.')) !!}
                        </div>
                    </div>
                </div>

                <!-- Desktop Action -->
                <div class="hidden md:flex items-center gap-6 mt-8 p-6 bg-gray-50 rounded-2xl border border-gray-100">
                    <div class="flex items-center bg-white border border-gray-200 rounded-xl overflow-hidden h-14 shadow-sm">
                        <button type="button" onclick="updatePageQty(-1)" class="w-14 h-full flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                        </button>
                        <input type="number" id="desktop-qty" value="1" min="1" max="{{ $product->stock }}" class="w-16 h-full text-center font-bold text-lg border-none focus:ring-0 p-0 text-gray-900 bg-transparent">
                        <button type="button" onclick="updatePageQty(1)" class="w-14 h-full flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </button>
                    </div>
                    <button onclick="addToCartFromPage()" class="flex-1 h-14 bg-blue-600 text-white rounded-xl font-bold text-lg hover:bg-blue-700 active:scale-[0.98] transition-all shadow-lg shadow-blue-200 flex items-center justify-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        Masukkan Keranjang
                    </button>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-12 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Mungkin Anda Suka</h2>
            <div class="flex overflow-x-auto gap-4 pb-4 snap-x hide-scrollbar">
                @foreach($relatedProducts as $related)
                    <div class="flex-none w-[160px] md:w-[200px] product-card group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 snap-start">
                        <a href="{{ route('products.show', $related->id) }}" class="block">
                            <div class="image-container aspect-square bg-gray-50 overflow-hidden relative">
                                <img src="{{ $related->image && str_starts_with($related->image, 'storage/') ? asset($related->image) : $related->image }}" 
                                     alt="{{ $related->name }}" 
                                     class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-500"
                                     onerror="this.src='https://placehold.co/400x400?text=No+Image'">
                            </div>
                            <div class="p-3">
                                <h3 class="text-sm font-semibold text-gray-900 mb-1 line-clamp-2 h-10">{{ $related->name }}</h3>
                                <div class="mt-2">
                                    <p class="text-sm font-bold text-blue-600">Rp {{ number_format($related->discounted_price ?? $related->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Mobile Sticky Action Bar -->
    <div class="md:hidden fixed bottom-[64px] left-0 w-full bg-white border-t border-gray-100 p-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-40 flex items-center gap-4">
        <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl overflow-hidden h-12">
            <button type="button" onclick="updatePageQty(-1)" class="w-10 h-full flex items-center justify-center text-gray-600 hover:bg-gray-200 active:bg-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
            </button>
            <input type="number" id="mobile-qty" value="1" min="1" max="{{ $product->stock }}" class="w-12 h-full text-center font-bold text-base border-none focus:ring-0 p-0 text-gray-900 bg-transparent">
            <button type="button" onclick="updatePageQty(1)" class="w-10 h-full flex items-center justify-center text-gray-600 hover:bg-gray-200 active:bg-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            </button>
        </div>
        <button onclick="addToCartFromPage()" class="flex-1 h-12 bg-blue-600 text-white rounded-xl font-bold text-base hover:bg-blue-700 active:scale-[0.98] transition-all shadow-md flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            + Keranjang
        </button>
    </div>
</div>

<script>
    const maxStock = {{ $product->stock }};
    
    function updatePageQty(change) {
        const desktopInput = document.getElementById('desktop-qty');
        const mobileInput = document.getElementById('mobile-qty');
        
        let currentVal = parseInt(desktopInput ? desktopInput.value : mobileInput.value);
        let newVal = currentVal + change;
        
        if (newVal < 1) newVal = 1;
        if (newVal > maxStock) newVal = maxStock;
        
        if (desktopInput) desktopInput.value = newVal;
        if (mobileInput) mobileInput.value = newVal;
    }

    function addToCartFromPage() {
        const desktopInput = document.getElementById('desktop-qty');
        const mobileInput = document.getElementById('mobile-qty');
        const qty = parseInt(desktopInput ? desktopInput.value : (mobileInput ? mobileInput.value : 1));
        
        // Use existing addToCart function from layout
        if (typeof addToCart === 'function') {
            // Need to modify addToCart to accept quantity, or use fetch directly here
            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: {{ $product->id }},
                    quantity: qty
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (typeof updateCartBadge === 'function') updateCartBadge();
                    
                    // Show success toast
                    const toast = document.createElement('div');
                    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-2xl z-[100] font-bold animate-fade-in-up flex items-center gap-3';
                    toast.innerHTML = `
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Berhasil ditambahkan ke keranjang!
                    `;
                    document.body.appendChild(toast);
                    
                    setTimeout(() => {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateY(-20px)';
                        toast.style.transition = 'all 0.3s ease';
                        setTimeout(() => toast.remove(), 300);
                    }, 2500);
                } else {
                    alert(data.message || 'Gagal menambahkan ke keranjang');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan sistem');
            });
        }
    }
</script>
@endsection
