@extends('layouts.customer')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
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

            <!-- Tabs -->
            <div class="flex border-b border-gray-200 mb-6 bg-white/50 backdrop-blur rounded-t-xl px-2 pt-2">
                <a href="{{ route('transactions.index', ['tab' => 'ongoing']) }}" class="flex-1 pb-3 text-center text-sm font-bold transition-colors {{ $tab === 'ongoing' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-800 hover:border-gray-300 border-b-2 border-transparent' }}">Sedang Berjalan</a>
                <a href="{{ route('transactions.index', ['tab' => 'completed']) }}" class="flex-1 pb-3 text-center text-sm font-bold transition-colors {{ $tab === 'completed' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-800 hover:border-gray-300 border-b-2 border-transparent' }}">Selesai</a>
                <a href="{{ route('transactions.index', ['tab' => 'cancelled']) }}" class="flex-1 pb-3 text-center text-sm font-bold transition-colors {{ $tab === 'cancelled' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-800 hover:border-gray-300 border-b-2 border-transparent' }}">Dibatalkan</a>
            </div>

            <div class="space-y-4">
                @forelse($orders as $order)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all overflow-hidden">
                        <!-- Card Header -->
                        <div class="px-4 py-3 md:px-6 md:py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] md:text-xs font-bold {{ in_array($order->status->value, ['cancelled', 'rejected']) ? 'bg-red-100 text-red-800' : (in_array($order->status->value, ['done', 'delivered']) ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }} uppercase tracking-wider">
                                    {{ $order->status->label() }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-400 font-mono">#{{ $order->order_number }}</span>
                            </div>
                        </div>

                        <!-- Card Body (Items) -->
                        <a href="{{ route('transactions.show', $order->id) }}" class="block p-4 md:p-6 hover:bg-gray-50/30 transition-colors">
                            <div class="flex flex-wrap gap-3 mb-4">
                                @foreach($order->items->take(4) as $item)
                                    <div class="w-14 h-14 md:w-16 md:h-16 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center p-1 overflow-hidden relative">
                                        @if($item->product)
                                            <img src="{{ $item->product->image && str_starts_with($item->product->image, 'storage/') ? asset($item->product->image) : $item->product->image }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover rounded-lg" onerror="this.src='https://placehold.co/100x100?text=No+Image';">
                                        @else
                                            <img src="https://placehold.co/100x100?text=Terhapus" alt="Produk Terhapus" class="w-full h-full object-cover rounded-lg">
                                        @endif
                                        @if($item->quantity > 1)
                                            <div class="absolute bottom-0 right-0 bg-gray-900/70 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-tl-lg rounded-br-lg">
                                                x{{ $item->quantity }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                
                                @if($order->items->count() > 4)
                                    <div class="w-14 h-14 md:w-16 md:h-16 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">
                                        +{{ $order->items->count() - 4 }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-xs text-gray-500 mb-0.5">Total Pembayaran</div>
                                    <div class="text-base md:text-lg font-bold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                                </div>
                                <div class="text-blue-600 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                                    Detail <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </div>
                            </div>
                        </a>

                        <!-- Card Footer (Action) -->
                        <div class="px-4 py-3 md:px-6 md:py-4 border-t border-gray-50 bg-white flex justify-end gap-3">
                            @if($order->status->value === 'done')
                                <a href="{{ route('transactions.invoice', $order->id) }}" class="px-6 py-2 border border-gray-200 text-gray-700 bg-white rounded-full text-sm font-bold shadow-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    Invoice
                                </a>
                            @endif
                            
                            @if(in_array($order->status->value, ['done', 'cancelled', 'rejected']))
                                <form action="{{ route('transactions.reorder', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-full text-sm font-bold shadow-sm hover:bg-blue-700 hover:shadow transform hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                        Pesan Lagi
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-3xl p-12 text-center shadow-xl shadow-blue-500/5 border border-white">
                        <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Belum ada transaksi</h2>
                        <p class="text-gray-500 max-w-md mx-auto mb-8">Anda belum memiliki transaksi di kategori ini.</p>
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