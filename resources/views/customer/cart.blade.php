@extends('layouts.customer')

@section('content')
    <style>
        .cart-item {
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            background-color: #f9fafb;
        }

        .quantity-btn {
            transition: all 0.2s ease;
        }

        .quantity-btn:hover {
            transform: scale(1.1);
        }

        .quantity-btn:active {
            transform: scale(0.95);
        }

        .remove-btn {
            transition: all 0.3s ease;
        }

        .remove-btn:hover {
            transform: scale(1.1);
            background-color: #fee2e2;
        }
    </style>

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Keranjang <span
                            class="text-blue-600">Belanja</span></h1>
                    <p class="text-gray-500 mt-2">{{ $totalItems }} item dalam keranjang Anda</p>
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

            @if($cartItems->count() > 0)
                <div class="grid lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 space-y-4">
                        @foreach($cartItems as $item)
                            <div class="cart-item bg-white rounded-2xl shadow-md p-4 md:p-6" data-item-id="{{ $item->id }}">
                                <div class="flex gap-4 md:gap-6">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}"
                                            class="w-20 h-20 md:w-32 md:h-32 object-contain rounded-lg bg-gray-50">
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h3
                                                    class="text-base md:text-lg font-bold text-gray-900 mb-1 line-clamp-2 md:line-clamp-none">
                                                    {{ $item->product->name }}
                                                </h3>
                                                <p class="text-sm text-gray-500">{{ $item->product->description }}</p>
                                            </div>
                                            <button onclick="removeItem({{ $item->id }})"
                                                class="remove-btn p-2 text-gray-400 hover:text-red-600 rounded-full">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-4">
                                            <!-- Quantity Controls -->
                                            <div class="flex items-center gap-3">
                                                <span class="text-sm text-gray-600">Jumlah:</span>
                                                <div class="flex items-center gap-2 bg-gray-100 rounded-lg px-2 py-1">
                                                    <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                                        class="quantity-btn p-1 text-blue-600 hover:text-blue-700">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M20 12H4" />
                                                        </svg>
                                                    </button>
                                                    <span
                                                        class="quantity-value w-8 text-center font-semibold">{{ $item->quantity }}</span>
                                                    <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                                        class="quantity-btn p-1 text-blue-600 hover:text-blue-700">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 4v16m8-8H4" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Price -->
                                            <div class="text-right">
                                                @php
                                                    $product = $item->product;
                                                    $hasDiscount = $product && $product->price > $item->price;
                                                @endphp
                                                
                                                @if($hasDiscount)
                                                    <div class="space-y-1">
                                                        <p class="text-xs text-gray-400 line-through">
                                                            Rp {{ number_format($product->price, 0, ',', '.') }} x {{ $item->quantity }}
                                                        </p>
                                                        <p class="text-sm font-semibold text-green-600">
                                                            Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->quantity }}
                                                        </p>
                                                        <p class="item-subtotal text-lg md:text-xl font-bold text-blue-600">
                                                            Rp {{ number_format($item->getSubtotal(), 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                @else
                                                    <p class="text-sm text-gray-500">Rp {{ number_format($item->price, 0, ',', '.') }} x
                                                        {{ $item->quantity }}
                                                    </p>
                                                    <p class="item-subtotal text-lg md:text-xl font-bold text-blue-600">
                                                        Rp {{ number_format($item->getSubtotal(), 0, ',', '.') }}
                                                    </p>
                                                @endif
                                                
                                                <!-- Routine Toggle -->
                                                <div class="mt-4 flex flex-col items-end gap-2">
                                                    <label class="inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" value="" class="sr-only peer" 
                                                            onchange="toggleRoutine({{ $item->id }}, this.checked)"
                                                            {{ $item->is_routine ? 'checked' : '' }}>
                                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                                        <span class="ms-3 text-sm font-medium text-gray-900">Rutin</span>
                                                    </label>

                                                    <!-- Day Selection -->
                                                    <div id="routine-days-{{ $item->id }}" class="flex flex-wrap gap-1 justify-end {{ $item->is_routine ? '' : 'hidden' }}">
                                                        @php
                                                            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                                            $selectedDays = $item->routine_schedule ?? [];
                                                        @endphp
                                                        @foreach($days as $day)
                                                            <button onclick="toggleDay({{ $item->id }}, '{{ $day }}')"
                                                                class="day-btn-{{ $item->id }}-{{ $day }} px-2 py-1 text-xs rounded-full border {{ in_array($day, $selectedDays) ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }} transition-colors">
                                                                {{ substr($day, 0, 3) }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Cart Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Ringkasan Belanja</h2>

                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between text-gray-600">
                                    <span>Total Item</span>
                                    <span class="font-semibold cart-total-items">{{ $totalItems }}</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal</span>
                                    <span class="font-semibold cart-subtotal-before">Rp
                                        {{ number_format($subtotalBeforeDiscount, 0, ',', '.') }}</span>
                                </div>
                                @if($totalDiscount > 0)
                                    <div class="flex justify-between text-green-600">
                                        <span>Diskon</span>
                                        <span class="font-semibold cart-discount">- Rp
                                            {{ number_format($totalDiscount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="border-t pt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-gray-900">Total</span>
                                        <span class="text-2xl font-bold text-blue-600 cart-total">
                                            Rp {{ number_format($totalPrice, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <button onclick="checkout()"
                                    class="w-full px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-full font-bold hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                                    Checkout
                                </button>
                                <a href="{{ route('home') }}"
                                    class="block w-full px-6 py-4 border-2 border-blue-600 text-blue-600 rounded-full font-semibold text-center hover:bg-blue-50 transition-all duration-300">
                                    Lanjut Belanja
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty Cart State -->
                <div class="bg-white rounded-3xl p-12 text-center shadow-xl shadow-blue-500/5 border border-white">
                    <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Keranjang Anda masih kosong</h2>
                    <p class="text-gray-500 max-w-md mx-auto mb-8">Anda belum menambahkan produk apa pun ke keranjang. Mulai jelajahi produk kami dan tambahkan yang Anda suka!</p>
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center px-8 py-3 bg-blue-600 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 transform hover:scale-105 transition-all duration-300">
                        Mulai Belanja
                    </a>
                </div>
            @endif

        </div>
    </div>

    <script>
        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Checkout
        async function checkout() {
            if (!confirm('Lanjutkan ke checkout?')) {
                return;
            }

            try {
                const response = await fetch('/cart/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = '/'; // Static redirect to home for now
                    }, 2000);
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat checkout', 'error');
            }
        }

        // Update quantity
        async function updateQuantity(cartItemId, newQuantity) {
            if (newQuantity < 1) {
                if (!confirm('Hapus item ini dari keranjang?')) {
                    return;
                }
            }

            try {
                const response = await fetch(`/cart/update/${cartItemId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ quantity: newQuantity })
                });

                const data = await response.json();

                if (data.success) {
                    if (newQuantity === 0) {
                        // Remove item from DOM
                        document.querySelector(`[data-item-id="${cartItemId}"]`).remove();

                        // Check if cart is empty
                        if (data.cart.total_items === 0) {
                            location.reload();
                        }
                    } else {
                        // Update item quantity display
                        const itemEl = document.querySelector(`[data-item-id="${cartItemId}"]`);
                        itemEl.querySelector('.quantity-value').textContent = newQuantity;

                        // Update subtotal
                        if (data.item) {
                            itemEl.querySelector('.item-subtotal').textContent =
                                'Rp ' + data.item.subtotal.toLocaleString('id-ID');
                        }
                    }

                    // Update cart summary
                    updateCartSummary(data.cart);

                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan', 'error');
            }
        }

        // Remove item
        async function removeItem(cartItemId) {
            if (!confirm('Hapus item ini dari keranjang?')) {
                return;
            }

            try {
                const response = await fetch(`/cart/remove/${cartItemId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Remove item from DOM
                    document.querySelector(`[data-item-id="${cartItemId}"]`).remove();

                    // Check if cart is empty
                    if (data.cart.total_items === 0) {
                        location.reload();
                    } else {
                        // Update cart summary
                        updateCartSummary(data.cart);
                    }

                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan', 'error');
            }
        }

        // Toggle Routine
        async function toggleRoutine(cartItemId, isRoutine) {
            const daysContainer = document.getElementById(`routine-days-${cartItemId}`);
            if (isRoutine) {
                daysContainer.classList.remove('hidden');
            } else {
                daysContainer.classList.add('hidden');
            }

            // Get current schedule if unchecking, or empty if checking (default)
            // Actually, we want to persist the schedule if just toggling off/on? 
            // For now, let's just update the flag.
            
            // To be safe, we should get the current selected days from DOM
            const selectedDays = getSelectedDays(cartItemId);

            await updateRoutineStatus(cartItemId, isRoutine, selectedDays);
        }

        // Toggle Day
        async function toggleDay(cartItemId, day) {
            const btn = document.querySelector(`.day-btn-${cartItemId}-${day}`);
            const isSelected = btn.classList.contains('bg-blue-600');

            if (isSelected) {
                btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                btn.classList.add('bg-white', 'text-gray-600', 'border-gray-300', 'hover:bg-gray-50');
            } else {
                btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                btn.classList.remove('bg-white', 'text-gray-600', 'border-gray-300', 'hover:bg-gray-50');
            }

            const selectedDays = getSelectedDays(cartItemId);
            await updateRoutineStatus(cartItemId, true, selectedDays);
        }

        function getSelectedDays(cartItemId) {
            const container = document.getElementById(`routine-days-${cartItemId}`);
            const selectedBtns = container.querySelectorAll('.bg-blue-600'); // Check for selected class
            const days = [];
            selectedBtns.forEach(btn => {
                // We need to extract the day from the onclick or data attribute. 
                // Let's rely on the text content for now (Sen, Sel...) or better parse the onclick.
                // Or better, let's add a data-day attribute to the buttons in the Previous step? 
                // Too late, let's parse the onclick or just add data-day in a separate small edit if needed.
                // Wait, I can just use the day passed to the function if I was updating a single one. 
                // But here I'm collecting all.
                // Let's modify the buttons to have data-day attribute in the next step or regex the onclick.
                // Regex from onclick attribute: toggleDay(123, 'Senin')
                const onclick = btn.getAttribute('onclick');
                const match = onclick.match(/'([^']+)'\)$/);
                if (match) {
                    days.push(match[1]);
                }
            });
            return days;
        }

        async function updateRoutineStatus(cartItemId, isRoutine, schedule) {
            try {
                const response = await fetch(`/cart/update-routine/${cartItemId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        is_routine: isRoutine,
                        routine_schedule: schedule
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // showNotification(data.message, 'success'); // Optional: show success?
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Gagal mengupdate status rutin', 'error');
            }
        }

        // Update cart summary
        function updateCartSummary(cart) {
            document.querySelector('.cart-total-items').textContent = cart.total_items;
            
            // Update subtotal before discount
            const subtotalBeforeEl = document.querySelector('.cart-subtotal-before');
            if (subtotalBeforeEl) {
                subtotalBeforeEl.textContent = 'Rp ' + (cart.subtotal_before_discount || cart.total_price).toLocaleString('id-ID');
            }
            
            // Update discount
            const discountEl = document.querySelector('.cart-discount');
            if (cart.total_discount && cart.total_discount > 0) {
                if (discountEl) {
                    discountEl.textContent = '- Rp ' + cart.total_discount.toLocaleString('id-ID');
                    discountEl.closest('.flex').classList.remove('hidden');
                }
            } else {
                if (discountEl) {
                    discountEl.closest('.flex').classList.add('hidden');
                }
            }
            
            // Update total
            document.querySelector('.cart-total').textContent = 'Rp ' + cart.total_price.toLocaleString('id-ID');

            // Update header badge
            const badge = document.querySelector('.cart-badge');
            if (badge) {
                badge.textContent = cart.total_items;
                badge.style.display = cart.total_items > 0 ? 'flex' : 'none';
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-24 right-4 px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'
                } text-white`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    </script>
@endsection