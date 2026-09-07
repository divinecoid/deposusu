@extends('layouts.customer')

@section('title', 'Checkout - DEPOSUSU')

@section('content')
<div class="min-h-screen bg-slate-50 py-6 md:py-10 pb-40 lg:pb-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Steps -->
        <ol class="flex items-center gap-2 text-xs md:text-sm mb-6" aria-label="Langkah checkout">
            <li class="flex items-center gap-2 text-slate-400">
                <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 font-bold flex items-center justify-center text-[11px]">1</span>
                Keranjang
            </li>
            <li class="w-6 h-px bg-slate-200" aria-hidden="true"></li>
            <li class="flex items-center gap-2 text-brand-600 font-semibold" aria-current="step">
                <span class="w-6 h-6 rounded-full bg-brand-600 text-white font-bold flex items-center justify-center text-[11px]">2</span>
                Pembayaran
            </li>
            <li class="w-6 h-px bg-slate-200" aria-hidden="true"></li>
            <li class="flex items-center gap-2 text-slate-400">
                <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 font-bold flex items-center justify-center text-[11px]">3</span>
                Selesai
            </li>
        </ol>

        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight mb-1">Checkout</h1>
        <p class="text-sm text-slate-500 mb-6">Periksa alamat dan pilih metode pembayaran Anda.</p>

        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm" novalidate>
            @csrf

            <div class="grid lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-4">

                    <!-- Address -->
                    <section class="bg-white rounded-2xl border border-slate-200/80 p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            <h2 class="text-base font-bold text-slate-900">Alamat Pengiriman</h2>
                        </div>

                        <label for="shipping_address" class="block text-sm font-medium text-slate-700 mb-2">Alamat lengkap</label>
                        <textarea id="shipping_address" name="shipping_address" rows="3" required
                            placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, patokan..."
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100">{{ old('shipping_address', $address) }}</textarea>
                        <p id="address-error" class="hidden mt-2 text-sm text-rose-600">Mohon isi alamat pengiriman lengkap (minimal 10 karakter).</p>
                        <p class="mt-2 text-xs text-slate-400">Sertakan RT/RW dan patokan rumah agar kurir mudah menemukan lokasi Anda.</p>
                    </section>

                    <!-- Payment -->
                    <section class="bg-white rounded-2xl border border-slate-200/80 p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </span>
                            <h2 class="text-base font-bold text-slate-900">Metode Pembayaran</h2>
                        </div>

                        <fieldset class="space-y-5">
                            <legend class="sr-only">Pilih metode pembayaran</legend>

                            @php $firstOption = true; @endphp

                            {{-- QRIS --}}
                            @foreach($paymentChannels->get('qris', []) as $channel)
                                <label class="payment-option group relative flex items-center gap-3 rounded-xl border border-slate-200 p-4 cursor-pointer transition-colors hover:border-brand-400 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/60 has-[:checked]:ring-1 has-[:checked]:ring-brand-600">
                                    <input type="radio" name="payment_method" value="{{ $channel['code'] }}" class="sr-only peer"
                                        data-fee="{{ $channel['convenience_fee'] }}" data-total="{{ $channel['total_with_fee'] }}"
                                        {{ $firstOption ? 'checked' : '' }}>
                                    @php $firstOption = false; @endphp
                                    <span class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 group-has-[:checked]:border-brand-600 transition-colors">
                                        <span class="w-2.5 h-2.5 rounded-full bg-brand-600 scale-0 group-has-[:checked]:scale-100 transition-transform"></span>
                                    </span>
                                    <span class="flex-1">
                                        <span class="block text-sm font-semibold text-slate-900">{{ $channel['label'] }}</span>
                                        <span class="block mt-0.5 text-xs text-slate-500">{{ $channel['description'] }}</span>
                                    </span>
                                    <span class="shrink-0 text-xs font-semibold text-slate-500">+Rp {{ number_format($channel['convenience_fee'], 0, ',', '.') }}</span>
                                </label>
                            @endforeach

                            {{-- E-wallets --}}
                            @if($paymentChannels->get('ewallet'))
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">E-Wallet</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($paymentChannels->get('ewallet') as $channel)
                                            <label class="payment-option group relative flex flex-col gap-1 rounded-xl border border-slate-200 p-3 cursor-pointer transition-colors hover:border-brand-400 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/60 has-[:checked]:ring-1 has-[:checked]:ring-brand-600">
                                                <input type="radio" name="payment_method" value="{{ $channel['code'] }}" class="sr-only peer"
                                                    data-fee="{{ $channel['convenience_fee'] }}" data-total="{{ $channel['total_with_fee'] }}">
                                                <span class="text-sm font-semibold text-slate-900">{{ $channel['label'] }}</span>
                                                <span class="text-xs text-slate-500">+Rp {{ number_format($channel['convenience_fee'], 0, ',', '.') }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Virtual Accounts --}}
                            @if($paymentChannels->get('va'))
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Transfer Virtual Account</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($paymentChannels->get('va') as $channel)
                                            <label class="payment-option group relative flex flex-col gap-1 rounded-xl border border-slate-200 p-3 cursor-pointer transition-colors hover:border-brand-400 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/60 has-[:checked]:ring-1 has-[:checked]:ring-brand-600">
                                                <input type="radio" name="payment_method" value="{{ $channel['code'] }}" class="sr-only peer"
                                                    data-fee="{{ $channel['convenience_fee'] }}" data-total="{{ $channel['total_with_fee'] }}">
                                                <span class="text-sm font-semibold text-slate-900">{{ str_replace(' Virtual Account', '', $channel['label']) }}</span>
                                                <span class="text-xs text-slate-500">+Rp {{ number_format($channel['convenience_fee'], 0, ',', '.') }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Cash on Delivery — only for admin-verified customers --}}
                            @if($isVerifiedCustomer)
                                <label class="payment-option group relative flex items-center gap-3 rounded-xl border border-slate-200 p-4 cursor-pointer transition-colors hover:border-brand-400 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/60 has-[:checked]:ring-1 has-[:checked]:ring-brand-600">
                                    <input type="radio" name="payment_method" value="COD" class="sr-only peer" data-fee="0" data-total="{{ $totalPrice }}">
                                    <span class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 group-has-[:checked]:border-brand-600 transition-colors">
                                        <span class="w-2.5 h-2.5 rounded-full bg-brand-600 scale-0 group-has-[:checked]:scale-100 transition-transform"></span>
                                    </span>
                                    <span class="flex-1">
                                        <span class="block text-sm font-semibold text-slate-900">Bayar di Tempat (COD)</span>
                                        <span class="block mt-0.5 text-xs text-slate-500">Bayar tunai langsung ke kurir saat pesanan tiba.</span>
                                    </span>
                                    <span class="shrink-0 text-xs font-semibold text-emerald-600">Gratis</span>
                                </label>
                            @else
                                <div class="flex items-center gap-3 rounded-xl border border-dashed border-slate-200 p-4 bg-slate-50/60">
                                    <span class="w-5 h-5 rounded-full border-2 border-slate-200 shrink-0"></span>
                                    <span class="flex-1">
                                        <span class="block text-sm font-semibold text-slate-400">Bayar di Tempat (COD)</span>
                                        <span class="block mt-0.5 text-xs text-slate-400">Tersedia untuk akun terverifikasi. Hubungi kami untuk verifikasi akun Anda.</span>
                                    </span>
                                </div>
                            @endif
                        </fieldset>
                    </section>
                </div>

                <!-- Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 lg:sticky lg:top-24">
                        <h2 class="text-base font-bold text-slate-900 mb-4">Ringkasan Pesanan</h2>

                        <div class="space-y-3 mb-4 max-h-56 overflow-y-auto pr-1">
                            @foreach($cartItems as $item)
                                @php
                                    $product = $item->product;
                                    $image = $product && $product->image
                                        ? (str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image)
                                        : null;
                                @endphp
                                <div class="flex items-center gap-3">
                                    <img src="{{ $image ?: 'https://placehold.co/80x80/f1f5f9/94a3b8?text=%20' }}" alt=""
                                         loading="lazy" class="w-12 h-12 rounded-xl object-cover bg-slate-50 shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-900 line-clamp-1">{{ $product->name ?? 'Produk' }}</p>
                                        <p class="text-xs text-slate-500">{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-900 shrink-0">Rp {{ number_format($item->getSubtotal(), 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>

                        <dl class="border-t border-slate-100 pt-4 space-y-3 text-sm">
                            <div class="flex justify-between text-slate-600">
                                <dt>Total item</dt><dd class="font-semibold">{{ $totalItems }}</dd>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <dt>Subtotal</dt><dd class="font-semibold">Rp {{ number_format($subtotalBeforeDiscount, 0, ',', '.') }}</dd>
                            </div>
                            @if($totalDiscount > 0)
                                <div class="flex justify-between text-emerald-600">
                                    <dt>Diskon</dt><dd class="font-semibold">- Rp {{ number_format($totalDiscount, 0, ',', '.') }}</dd>
                                </div>
                            @endif
                            <div class="flex justify-between text-slate-600">
                                <dt>Ongkos kirim</dt><dd class="font-semibold text-emerald-600">Gratis</dd>
                            </div>
                        </dl>

                        <div id="fee-row" class="flex justify-between text-slate-600 text-sm mt-3 border-t border-slate-100 pt-3">
                            <span>Biaya layanan</span>
                            <span id="fee-amount" class="font-semibold">Rp 0</span>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-baseline justify-between">
                            <span class="font-bold text-slate-900">Total Pembayaran</span>
                            <span id="grand-total-desktop" class="text-2xl font-extrabold text-slate-900">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>

                        <button type="submit"
                            class="hidden lg:flex mt-5 w-full h-12 rounded-xl bg-brand-600 text-white font-bold items-center justify-center gap-2 hover:bg-brand-700 transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                            <span class="submit-label">Buat Pesanan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7-7 7"/></svg>
                        </button>

                        <a href="{{ route('cart.index') }}" class="mt-3 block text-center text-sm font-semibold text-slate-500 hover:text-slate-700">
                            Kembali ke keranjang
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile sticky submit (above the bottom navigation) -->
            <div class="lg:hidden fixed bottom-16 md:bottom-0 inset-x-0 z-40 bg-white border-t border-slate-200 px-4 py-3 flex items-center gap-4">
                <div class="min-w-0">
                    <p class="text-[11px] text-slate-500">Total bayar</p>
                    <p id="grand-total-mobile" class="text-lg font-extrabold text-slate-900 truncate">Rp {{ number_format($totalPrice, 0, ',', '.') }}</p>
                </div>
                <button type="submit"
                    class="flex-1 h-12 rounded-xl bg-brand-600 text-white font-bold disabled:opacity-60 disabled:cursor-not-allowed">
                    <span class="submit-label">Buat Pesanan</span>
                </button>
            </div>
        </form>

        @include('customer.partials.payment-modal')
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('checkoutForm');
        const buttons = form.querySelectorAll('button[type="submit"]');
        const addressField = document.getElementById('shipping_address');
        const addressError = document.getElementById('address-error');
        let submitting = false;

        const rupiah = value => 'Rp ' + Math.round(Number(value) || 0).toLocaleString('id-ID');

        function setBusy(busy) {
            buttons.forEach(button => {
                button.disabled = busy;
                const label = button.querySelector('.submit-label');
                if (label) label.textContent = busy ? 'Memproses...' : 'Buat Pesanan';
            });
        }

        /* ---------------- Live fee/total per selected channel ---------------- */
        function updateTotals() {
            const checked = form.querySelector('input[name="payment_method"]:checked');
            if (!checked) return;

            const fee = Number(checked.dataset.fee || 0);
            const total = Number(checked.dataset.total || 0);

            document.getElementById('fee-amount').textContent = rupiah(fee);
            document.getElementById('fee-row').classList.toggle('hidden', fee <= 0);
            document.getElementById('grand-total-desktop').textContent = rupiah(total);
            document.getElementById('grand-total-mobile').textContent = rupiah(total);
        }

        form.querySelectorAll('input[name="payment_method"]').forEach(input => {
            input.addEventListener('change', updateTotals);
        });
        updateTotals();

        addressField.addEventListener('input', () => {
            if (addressField.value.trim().length >= 10) {
                addressError.classList.add('hidden');
                addressField.classList.remove('border-rose-400');
            }
        });

        /* ---------------- Submit ---------------- */
        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            if (submitting) return;

            if (addressField.value.trim().length < 10) {
                addressError.classList.remove('hidden');
                addressField.classList.add('border-rose-400');
                addressField.focus();
                addressField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            submitting = true;
            setBusy(true);

            const payload = Object.fromEntries(new FormData(this).entries());

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                const result = await response.json();

                if (result.success) {
                    const payment = result.payment || {};

                    if (payment.group === 'qris' || payment.group === 'va' || payment.group === 'ewallet') {
                        await window.showXenditPaymentModal(payment);
                        return;
                    }

                    if (payment.group === 'failed') {
                        window.location.href = payment.retry_url;
                        return;
                    }

                    // COD or anything without an online payment step
                    window.location.href = `/transactions/${result.order_id}`;
                    return;
                }

                showNotification(result.message || 'Pesanan gagal diproses', 'error');
            } catch (error) {
                console.error(error);
                showNotification('Koneksi bermasalah, coba lagi', 'error');
            }

            submitting = false;
            setBusy(false);
        });
    })();
</script>
@endpush
