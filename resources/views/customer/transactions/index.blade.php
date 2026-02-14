@extends('layouts.customer')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Transaksi <span
                            class="text-blue-600">Saya</span></h1>
                    <p class="text-gray-500 mt-2">Daftar semua pesanan Anda</p>
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

            <div class="space-y-3">
                @forelse($orders as $order)
                    <a href="{{ route('transactions.show', $order->id) }}"
                        class="block bg-white rounded-2xl shadow border border-gray-100 hover:border-blue-500 transition">
                        <div class="p-4 md:p-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="text-[11px] text-gray-400">Nomor</div>
                                    <div class="text-sm font-bold text-blue-600">#{{ $order->order_number }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[11px] text-gray-400">Tanggal</div>
                                    <div class="text-sm font-medium text-gray-900">{{ $order->created_at->format('d M Y H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">{{ ucfirst($order->status) }}</span>
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $order->payment_status === 'PAID' ? 'bg-green-100 text-green-800' : ($order->payment_status === 'CANCELLED' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">{{ $order->payment_status }}</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-[11px] text-gray-400">Total</div>
                                    <div class="text-base md:text-lg font-bold text-gray-900">Rp
                                        {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="bg-white rounded-3xl p-12 text-center shadow-xl shadow-blue-500/5 border border-white">
                        <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Belum ada transaksi</h2>
                        <p class="text-gray-500 max-w-md mx-auto mb-8">Anda belum melakukan pembelian apa pun. Mulai berbelanja dan
                            nikmati produk berkualitas kami!</p>
                        <a href="{{ route('home') }}"
                            class="inline-flex items-center px-8 py-3 bg-blue-600 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 transform hover:scale-105 transition-all duration-300">
                            Mulai Belanja
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection