@extends('layouts.customer')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-4">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Transaksi Saya</h1>
            <p class="text-gray-600 text-xs md:text-sm">Daftar semua pesanan Anda</p>
        </div>

        <div class="space-y-3">
            @forelse($orders as $order)
                <a href="{{ route('transactions.show', $order->id) }}" class="block bg-white rounded-2xl shadow border border-gray-100 hover:border-blue-500 transition">
                    <div class="p-4 md:p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-[11px] text-gray-400">Nomor</div>
                                <div class="text-sm font-bold text-blue-600">#{{ $order->order_number }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[11px] text-gray-400">Tanggal</div>
                                <div class="text-sm font-medium text-gray-900">{{ $order->created_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">{{ ucfirst($order->status) }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $order->payment_status === 'PAID' ? 'bg-green-100 text-green-800' : ($order->payment_status === 'CANCELLED' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">{{ $order->payment_status }}</span>
                            </div>
                            <div class="text-right">
                                <div class="text-[11px] text-gray-400">Total</div>
                                <div class="text-base md:text-lg font-bold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-6 text-center text-gray-500">
                    Belum ada transaksi.
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
