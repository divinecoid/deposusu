@extends('layouts.customer')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-4">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900">#{{ $order->order_number }}</h1>
                    <p class="text-gray-600 text-xs md:text-sm">{{ $order->created_at->format('d M Y H:i') }}</p>
                </div>
                <a href="{{ route('transactions.index') }}" class="px-3 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 text-sm">Kembali</a>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">{{ $order->status->label() }}</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $order->payment_status === 'PAID' ? 'bg-green-100 text-green-800' : ($order->payment_status === 'CANCELLED' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">{{ $order->payment_status }}</span>
            </div>
        </div>

        @php
            $statusValue = $order->status->value ?? $order->status;
            $step = 0;
            if (in_array($statusValue, ['pending', 'onprocess', 'onpreparation'])) {
                $step = 1;
            } elseif ($statusValue === 'prepared') {
                $step = 2;
            } elseif ($statusValue === 'ondelivery') {
                $step = 3;
            } elseif (in_array($statusValue, ['delivered', 'partialdelivered', 'done'])) {
                $step = 4;
            }
        @endphp

        @if($step > 0)
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-4 md:p-6 mb-4 mt-4">
            <div class="text-sm font-semibold text-gray-800 mb-4">Status Pesanan</div>
            
            <div class="relative ml-2 space-y-6">
                <!-- Vertical Line Base -->
                <div class="absolute left-4 top-4 bottom-4 w-0.5 bg-gray-200"></div>
                <!-- Vertical Line Active -->
                <div class="absolute left-4 top-4 w-0.5 bg-blue-600 transition-all duration-500" style="height: {{ ($step > 0 ? ($step - 1) * 33.33 : 0) }}%;"></div>

                <!-- Step 1: Diproses -->
                <div class="relative z-10 flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-4 border-white shadow-sm shrink-0 {{ $step >= 1 ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' }}">
                        <span class="text-sm">🛒</span>
                    </div>
                    <div class="pt-1.5">
                        <h4 class="text-sm {{ $step >= 1 ? 'text-gray-900 font-bold' : 'text-gray-400 font-medium' }}">Pesanan Diproses</h4>
                        <p class="text-xs {{ $step >= 1 ? 'text-gray-500' : 'text-gray-400' }} mt-0.5">Kami telah menerima pesanan Anda.</p>
                    </div>
                </div>

                <!-- Step 2: Dikemas -->
                <div class="relative z-10 flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-4 border-white shadow-sm shrink-0 {{ $step >= 2 ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' }}">
                        <span class="text-sm">📦</span>
                    </div>
                    <div class="pt-1.5">
                        <h4 class="text-sm {{ $step >= 2 ? 'text-gray-900 font-bold' : 'text-gray-400 font-medium' }}">Pesanan Dikemas</h4>
                        <p class="text-xs {{ $step >= 2 ? 'text-gray-500' : 'text-gray-400' }} mt-0.5">Pesanan Anda sedang disiapkan dan dikemas.</p>
                    </div>
                </div>

                <!-- Step 3: Dikirim -->
                <div class="relative z-10 flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-4 border-white shadow-sm shrink-0 {{ $step >= 3 ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' }}">
                        <span class="text-sm">🚚</span>
                    </div>
                    <div class="pt-1.5">
                        <h4 class="text-sm {{ $step >= 3 ? 'text-gray-900 font-bold' : 'text-gray-400 font-medium' }}">Pesanan Dikirim</h4>
                        <p class="text-xs {{ $step >= 3 ? 'text-gray-500' : 'text-gray-400' }} mt-0.5">Kurir sedang dalam perjalanan menuju lokasi Anda.</p>
                    </div>
                </div>

                <!-- Step 4: Selesai -->
                <div class="relative z-10 flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-4 border-white shadow-sm shrink-0 {{ $step >= 4 ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' }}">
                        <span class="text-sm">✅</span>
                    </div>
                    <div class="pt-1.5">
                        <h4 class="text-sm {{ $step >= 4 ? 'text-gray-900 font-bold' : 'text-gray-400 font-medium' }}">Pesanan Selesai</h4>
                        <p class="text-xs {{ $step >= 4 ? 'text-gray-500' : 'text-gray-400' }} mt-0.5">Pesanan telah tiba dan diterima dengan baik.</p>
                    </div>
                </div>
            </div>
        </div>
        @elseif(in_array($statusValue, ['cancelled', 'rejected']))
        <div class="bg-red-50 rounded-2xl border border-red-100 p-4 mb-4 mt-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-red-800">Pesanan Dibatalkan</h3>
                    <p class="text-xs text-red-600 mt-0.5">Pesanan ini telah dibatalkan atau ditolak.</p>
                </div>
            </div>
        </div>
        @endif

        <div class="space-y-4">
            <div class="bg-white rounded-2xl shadow border border-gray-100">
                <div class="p-4 md:p-6">
                    <div class="text-sm font-semibold text-gray-800 mb-3">Item Pesanan</div>
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                    <div class="text-xs text-gray-500">Qty {{ $item->quantity }} • Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                                </div>
                                <div class="text-right text-sm font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow border border-gray-100">
                <div class="p-4 md:p-6">
                    <div class="text-sm font-semibold text-gray-800 mb-3">Ringkasan</div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Diskon</span>
                            <span class="font-medium">Rp {{ number_format($order->total_discount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($order->invoice)
                <div class="bg-white rounded-2xl shadow border border-gray-100">
                    <div class="p-4 md:p-6">
                        <div class="text-sm font-semibold text-gray-800 mb-3">Invoice</div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nomor</span>
                                <span class="font-medium">{{ $order->invoice->invoice_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Terbit</span>
                                <span class="font-medium">{{ $order->invoice->issue_date->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jatuh Tempo</span>
                                <span class="font-medium">{{ $order->invoice->due_date->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total</span>
                                <span class="font-bold text-gray-900">Rp {{ number_format($order->invoice->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
