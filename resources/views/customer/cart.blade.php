@extends('layouts.customer')

@section('title', 'Keranjang Belanja - DEPOSUSU')

@section('content')
<div class="min-h-screen bg-slate-50 py-6 md:py-10 pb-40 md:pb-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Keranjang Belanja</h1>
                <p class="text-sm text-slate-500 mt-1"><span class="cart-total-items">{{ $totalItems }}</span> item siap dipesan</p>
            </div>
            <a href="{{ route('home') }}"
                class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:border-brand-500 hover:text-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Lanjut Belanja
            </a>
        </div>

        @if($cartItems->count() > 0)
            <div class="grid lg:grid-cols-3 gap-6">

                <!-- Items -->
                <div class="lg:col-span-2 space-y-3">
                    @foreach($cartItems as $item)
                        @php
                            $product = $item->product;
                            $stock = (int) ($product->stock ?? 0);
                            $hasDiscount = $product && $product->price > $item->price;
                            $image = $product && $product->image
                                ? (str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image)
                                : null;
                        @endphp
                        <div class="cart-item bg-white rounded-2xl border border-slate-200/80 p-4"
                            data-item-id="{{ $item->id }}"
                            data-item-price="{{ $item->price }}"
                            data-item-quantity="{{ $item->quantity }}"
                            data-item-stock="{{ $stock }}">

                            <div class="flex gap-4">
                                <a href="{{ $product ? route('products.show', $product->id) : '#' }}" class="shrink-0">
                                    <img src="{{ $image ?: 'https://placehold.co/200x200/f1f5f9/94a3b8?text=No+Image' }}"
                                        alt="{{ $product->name ?? 'Produk' }}" loading="lazy"
                                        class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-xl bg-slate-50">
                                </a>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <a href="{{ $product ? route('products.show', $product->id) : '#' }}"
                                           class="text-sm md:text-base font-semibold text-slate-900 line-clamp-2 leading-snug hover:text-brand-600">
                                            {{ $product->name ?? 'Produk' }}
                                        </a>
                                        <button type="button" onclick="removeItem({{ $item->id }})"
                                            aria-label="Hapus {{ $product->name ?? 'produk' }} dari keranjang"
                                            class="shrink-0 p-2 -mt-1 -mr-1 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>

                                    <div class="mt-1 flex items-center gap-2 flex-wrap">
                                        @if($hasDiscount)
                                            <span class="text-xs text-slate-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        @endif
                                        <span class="text-xs text-slate-500">Rp {{ number_format($item->price, 0, ',', '.') }} / item</span>
                                        @if($stock > 0 && $stock <= 5)
                                            <span class="text-[11px] font-semibold text-amber-600">Sisa {{ $stock }}</span>
                                        @elseif($stock <= 0)
                                            <span class="text-[11px] font-semibold text-rose-600">Stok habis</span>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex items-center justify-between gap-3">
                                        <div class="flex items-center h-10 border border-slate-200 rounded-xl overflow-hidden">
                                            <button type="button" onclick="changeQuantity({{ $item->id }}, -1)"
                                                aria-label="Kurangi jumlah"
                                                class="quantity-btn w-9 h-full flex items-center justify-center text-slate-500 hover:bg-slate-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                            </button>
                                            <input type="number" value="{{ $item->quantity }}" min="1" max="{{ max(1, $stock) }}"
                                                aria-label="Jumlah {{ $product->name ?? 'produk' }}"
                                                class="quantity-value w-11 h-full text-center text-sm font-bold bg-transparent border-none focus:ring-0 p-0 text-slate-900"
                                                onchange="updateQuantity({{ $item->id }}, this.value)">
                                            <button type="button" onclick="changeQuantity({{ $item->id }}, 1)"
                                                aria-label="Tambah jumlah"
                                                class="quantity-btn w-9 h-full flex items-center justify-center text-slate-500 hover:bg-slate-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                            </button>
                                        </div>

                                        <p class="item-subtotal text-base md:text-lg font-bold text-slate-900">
                                            Rp {{ number_format($item->getSubtotal(), 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @auth
                                <!-- Routine ordering -->
                                <div class="mt-4 pt-4 border-t border-slate-100">
                                    <label class="inline-flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" class="sr-only peer"
                                            onchange="toggleRoutine({{ $item->id }}, this.checked)"
                                            {{ $item->is_routine ? 'checked' : '' }}>
                                        <span class="relative w-10 h-6 bg-slate-200 rounded-full transition-colors peer-checked:bg-brand-600 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-transform peer-checked:after:translate-x-4"></span>
                                        <span class="text-sm font-medium text-slate-700">Langganan rutin</span>
                                    </label>
                                    <p class="mt-1 text-xs text-slate-400">Pilih hari pengiriman, kami kirim otomatis setiap minggu.</p>

                                    @php
                                        $days = $activeDays ?? ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                        $selectedDays = $item->routine_schedule ?? [];
                                    @endphp
                                    <div id="routine-days-{{ $item->id }}" class="mt-3 flex flex-wrap gap-1.5 {{ $item->is_routine ? '' : 'hidden' }}">
                                        @foreach($days as $day)
                                            <button type="button" data-day="{{ $day }}"
                                                onclick="toggleDay({{ $item->id }}, '{{ $day }}')"
                                                aria-pressed="{{ in_array($day, $selectedDays) ? 'true' : 'false' }}"
                                                class="day-btn px-3 h-8 text-xs font-semibold rounded-full border transition-colors {{ in_array($day, $selectedDays) ? 'is-selected bg-brand-600 text-white border-brand-600' : 'bg-white text-slate-600 border-slate-200 hover:border-brand-300' }}">
                                                {{ substr($day, 0, 3) }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endauth
                        </div>
                    @endforeach
                </div>

                <!-- Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 lg:sticky lg:top-24">
                        <h2 class="text-base font-bold text-slate-900 mb-4">Ringkasan Belanja</h2>

                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between text-slate-600">
                                <dt>Total item</dt>
                                <dd class="font-semibold cart-total-items">{{ $totalItems }}</dd>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <dt>Subtotal</dt>
                                <dd class="font-semibold cart-subtotal-before">Rp {{ number_format($subtotalBeforeDiscount, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between text-emerald-600 cart-discount-row {{ $totalDiscount > 0 ? '' : 'hidden' }}">
                                <dt>Diskon</dt>
                                <dd class="font-semibold cart-discount">- Rp {{ number_format($totalDiscount, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <dt>Ongkos kirim</dt>
                                <dd class="font-semibold text-emerald-600">Gratis</dd>
                            </div>
                        </dl>

                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-baseline justify-between">
                            <span class="font-bold text-slate-900">Total</span>
                            <span class="text-2xl font-extrabold text-slate-900 cart-total">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>

                        <a href="{{ route('checkout.index') }}"
                            class="mt-5 w-full h-12 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center gap-2 hover:bg-brand-700 transition-colors">
                            Lanjut ke Pembayaran
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7-7 7"/></svg>
                        </a>

                        <p class="mt-3 text-xs text-slate-400 text-center">Pembayaran QRIS, transfer, e-wallet, atau bayar di tempat.</p>
                    </div>
                </div>
            </div>

            <!-- Mobile sticky checkout bar (above the bottom navigation) -->
            <div class="lg:hidden fixed bottom-16 md:bottom-0 inset-x-0 z-40 bg-white border-t border-slate-200 px-4 py-3 flex items-center gap-4">
                <div class="min-w-0">
                    <p class="text-[11px] text-slate-500">Total</p>
                    <p class="text-lg font-extrabold text-slate-900 cart-total truncate">Rp {{ number_format($totalPrice, 0, ',', '.') }}</p>
                </div>
                <a href="{{ route('checkout.index') }}"
                    class="flex-1 h-12 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center">
                    Checkout
                </a>
            </div>
        @else
            <!-- Empty state -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-10 md:p-16 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-900 mb-2">Keranjang Anda masih kosong</h2>
                <p class="text-slate-500 max-w-sm mx-auto mb-7 text-sm">Belum ada produk di sini. Mulai dari promo hari ini, atau jelajahi katalog kami.</p>
                <div class="flex flex-wrap gap-3 justify-center">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center h-12 px-7 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 transition-colors">
                        Mulai Belanja
                    </a>
                    <a href="{{ route('home') }}#promo-section"
                        class="inline-flex items-center h-12 px-7 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:border-brand-500 hover:text-brand-600 transition-colors">
                        Lihat Promo
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    const rupiah = value => 'Rp ' + Number(value || 0).toLocaleString('id-ID');

    // One debounce timer per cart item, so editing two rows quickly does not
    // drop the first request.
    const pendingUpdates = new Map();

    function itemElement(cartItemId) {
        return document.querySelector(`[data-item-id="${cartItemId}"]`);
    }

    function changeQuantity(cartItemId, delta) {
        const el = itemElement(cartItemId);
        if (!el) return;
        updateQuantity(cartItemId, (parseInt(el.dataset.itemQuantity, 10) || 1) + delta);
    }

    async function updateQuantity(cartItemId, newQuantity) {
        const el = itemElement(cartItemId);
        if (!el) return;

        newQuantity = parseInt(newQuantity, 10);
        if (Number.isNaN(newQuantity)) return;

        const stock = parseInt(el.dataset.itemStock, 10) || 0;
        const oldQty = parseInt(el.dataset.itemQuantity, 10) || 1;
        const input = el.querySelector('.quantity-value');

        if (newQuantity < 1) {
            input.value = oldQty;
            removeItem(cartItemId);
            return;
        }

        if (stock > 0 && newQuantity > stock) {
            newQuantity = stock;
            showNotification(`Stok tersedia hanya ${stock} item`, 'info');
        }

        // Optimistic update
        const price = parseInt(el.dataset.itemPrice, 10) || 0;
        el.dataset.itemQuantity = newQuantity;
        input.value = newQuantity;
        el.querySelector('.item-subtotal').textContent = rupiah(price * newQuantity);

        const buttons = el.querySelectorAll('.quantity-btn');
        buttons.forEach(button => button.classList.add('opacity-50', 'pointer-events-none'));

        if (pendingUpdates.has(cartItemId)) clearTimeout(pendingUpdates.get(cartItemId));

        pendingUpdates.set(cartItemId, setTimeout(async () => {
            pendingUpdates.delete(cartItemId);
            try {
                const response = await fetch(`/cart/update/${cartItemId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ quantity: newQuantity }),
                });
                const data = await response.json();

                if (data.success) {
                    if (data.item) el.querySelector('.item-subtotal').textContent = rupiah(data.item.subtotal);
                    updateCartSummary(data.cart);
                } else {
                    // Roll the optimistic update back
                    el.dataset.itemQuantity = oldQty;
                    input.value = oldQty;
                    el.querySelector('.item-subtotal').textContent = rupiah(price * oldQty);
                    showNotification(data.message || 'Gagal mengubah jumlah', 'error');
                }
            } catch (error) {
                console.error(error);
                el.dataset.itemQuantity = oldQty;
                input.value = oldQty;
                el.querySelector('.item-subtotal').textContent = rupiah(price * oldQty);
                showNotification('Koneksi bermasalah, coba lagi', 'error');
            } finally {
                buttons.forEach(button => button.classList.remove('opacity-50', 'pointer-events-none'));
            }
        }, 350));
    }

    async function removeItem(cartItemId) {
        const el = itemElement(cartItemId);
        if (!el) return;

        // Optimistically hide the row; restore it if the request fails.
        el.style.transition = 'opacity .2s';
        el.style.opacity = '0.4';

        try {
            const response = await fetch(`/cart/remove/${cartItemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });
            const data = await response.json();

            if (data.success) {
                el.remove();
                if (data.cart.total_items === 0) {
                    location.reload();
                    return;
                }
                updateCartSummary(data.cart);
                showNotification('Item dihapus dari keranjang', 'success');
            } else {
                el.style.opacity = '1';
                showNotification(data.message || 'Gagal menghapus item', 'error');
            }
        } catch (error) {
            console.error(error);
            el.style.opacity = '1';
            showNotification('Koneksi bermasalah, coba lagi', 'error');
        }
    }

    function updateCartSummary(cart) {
        document.querySelectorAll('.cart-total-items').forEach(el => el.textContent = cart.total_items);
        document.querySelectorAll('.cart-total').forEach(el => el.textContent = rupiah(cart.total_price));

        const subtotal = document.querySelector('.cart-subtotal-before');
        if (subtotal) subtotal.textContent = rupiah(cart.subtotal_before_discount ?? cart.total_price);

        const discountRow = document.querySelector('.cart-discount-row');
        const discount = document.querySelector('.cart-discount');
        if (discountRow && discount) {
            const hasDiscount = (cart.total_discount || 0) > 0;
            discountRow.classList.toggle('hidden', !hasDiscount);
            if (hasDiscount) discount.textContent = '- ' + rupiah(cart.total_discount);
        }

        renderCartBadges(cart.total_items);
        refreshCart();
    }

    /* ---------------- Routine subscription ---------------- */
    function selectedDays(cartItemId) {
        return Array.from(
            document.querySelectorAll(`#routine-days-${cartItemId} .day-btn.is-selected`)
        ).map(button => button.dataset.day);
    }

    async function toggleRoutine(cartItemId, isRoutine) {
        document.getElementById(`routine-days-${cartItemId}`)?.classList.toggle('hidden', !isRoutine);
        await updateRoutineStatus(cartItemId, isRoutine, selectedDays(cartItemId));
    }

    async function toggleDay(cartItemId, day) {
        const button = document.querySelector(`#routine-days-${cartItemId} [data-day="${day}"]`);
        if (!button) return;

        const selected = button.classList.toggle('is-selected');
        button.setAttribute('aria-pressed', String(selected));
        button.classList.toggle('bg-brand-600', selected);
        button.classList.toggle('text-white', selected);
        button.classList.toggle('border-brand-600', selected);
        button.classList.toggle('bg-white', !selected);
        button.classList.toggle('text-slate-600', !selected);
        button.classList.toggle('border-slate-200', !selected);

        await updateRoutineStatus(cartItemId, true, selectedDays(cartItemId));
    }

    async function updateRoutineStatus(cartItemId, isRoutine, schedule) {
        try {
            const response = await fetch(`/cart/update-routine/${cartItemId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ is_routine: isRoutine, routine_schedule: schedule }),
            });
            const data = await response.json();
            if (!data.success) showNotification(data.message || 'Gagal menyimpan jadwal rutin', 'error');
        } catch (error) {
            console.error(error);
            showNotification('Gagal menyimpan jadwal rutin', 'error');
        }
    }
</script>
@endpush
