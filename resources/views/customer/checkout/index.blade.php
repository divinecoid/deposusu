@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Checkout <span class="text-blue-600">Pesanan</span></h1>
            <p class="text-gray-500 mt-2">Selangkah lagi pesanan Anda akan segera diproses.</p>
        </div>

        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
            @csrf
            
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Left Column (Address & Payment) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Address Section -->
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Alamat Pengiriman</h2>
                        </div>
                        
                        <div>
                            <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                            <textarea id="shipping_address" name="shipping_address" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>{{ old('shipping_address', $address) }}</textarea>
                            <p class="text-xs text-gray-500 mt-2">Pastikan alamat Anda lengkap beserta RT/RW dan patokan rumah.</p>
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Metode Pembayaran</h2>
                        </div>
                        
                        <div class="space-y-3">
                            <!-- QRIS -->
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none border-gray-200 hover:border-blue-500 has-[:checked]:border-blue-600 has-[:checked]:ring-1 has-[:checked]:ring-blue-600">
                                <input type="radio" name="payment_method" value="QRIS" class="sr-only" checked>
                                <div class="flex flex-1">
                                    <div class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">QRIS</span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">Scan QR Code dari aplikasi m-banking atau e-wallet.</span>
                                    </div>
                                </div>
                                <svg class="h-5 w-5 text-blue-600 hidden group-has-[:checked]:block" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                            </label>

                            <!-- Transfer -->
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none border-gray-200 hover:border-blue-500 has-[:checked]:border-blue-600 has-[:checked]:ring-1 has-[:checked]:ring-blue-600">
                                <input type="radio" name="payment_method" value="BANK_TRANSFER" class="sr-only">
                                <div class="flex flex-1">
                                    <div class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">Transfer Bank</span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">Transfer manual ke rekening kami.</span>
                                    </div>
                                </div>
                                <svg class="h-5 w-5 text-blue-600 hidden group-has-[:checked]:block" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                            </label>

                            <!-- COD -->
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none border-gray-200 hover:border-blue-500 has-[:checked]:border-blue-600 has-[:checked]:ring-1 has-[:checked]:ring-blue-600">
                                <input type="radio" name="payment_method" value="COD" class="sr-only">
                                <div class="flex flex-1">
                                    <div class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">Bayar di Tempat (COD)</span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">Bayar tunai langsung ke kurir kami.</span>
                                    </div>
                                </div>
                                <svg class="h-5 w-5 text-blue-600 hidden group-has-[:checked]:block" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                            </label>

                            <!-- E-Wallet -->
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none border-gray-200 hover:border-blue-500 has-[:checked]:border-blue-600 has-[:checked]:ring-1 has-[:checked]:ring-blue-600">
                                <input type="radio" name="payment_method" value="EWALLET" class="sr-only">
                                <div class="flex flex-1">
                                    <div class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">E-Wallet (OVO/GoPay/Dana/dll)</span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">Pembayaran digital via dompet elektronik.</span>
                                    </div>
                                </div>
                                <svg class="h-5 w-5 text-blue-600 hidden group-has-[:checked]:block" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Summary) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Ringkasan Pesanan</h2>

                        <!-- Small item list -->
                        <div class="space-y-4 mb-6 max-h-48 overflow-y-auto pr-2">
                            @foreach($cartItems as $item)
                            <div class="flex items-center gap-3">
                                <img src="{{ $item->product->image }}" class="w-12 h-12 rounded object-cover bg-gray-50">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 line-clamp-1">{{ $item->product->name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-100 pt-4 space-y-4 mb-6">
                            <div class="flex justify-between text-gray-600 text-sm">
                                <span>Total Item</span>
                                <span class="font-semibold">{{ $totalItems }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600 text-sm">
                                <span>Subtotal</span>
                                <span class="font-semibold">Rp {{ number_format($subtotalBeforeDiscount, 0, ',', '.') }}</span>
                            </div>
                            @if($totalDiscount > 0)
                                <div class="flex justify-between text-green-600 text-sm">
                                    <span>Diskon</span>
                                    <span class="font-semibold">- Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between text-gray-600 text-sm">
                                <span>Ongkos Kirim</span>
                                <span class="font-semibold text-green-600">Gratis</span>
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-base font-bold text-gray-900">Total Pembayaran</span>
                                    <span class="text-2xl font-bold text-blue-600">
                                        Rp {{ number_format($totalPrice, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-xl font-bold hover:shadow-lg transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2">
                            <span>Proses Pesanan</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = 'Memproses...';

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                // Redirect directly to the order tracking page!
                window.location.href = `/transactions/${result.order_id}`;
            } else {
                alert(result.message);
                btn.disabled = false;
                btn.innerHTML = 'Proses Pesanan';
            }
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan.');
            btn.disabled = false;
            btn.innerHTML = 'Proses Pesanan';
        }
    });
</script>
@endsection
