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
                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">{{ ucfirst($order->status) }}</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $order->payment_status === 'PAID' ? 'bg-green-100 text-green-800' : ($order->payment_status === 'CANCELLED' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">{{ $order->payment_status }}</span>
            </div>
        </div>

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
