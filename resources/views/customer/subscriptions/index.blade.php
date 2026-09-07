@extends('layouts.customer')

@section('title', 'Langganan Saya - DEPOSUSU')

@section('content')
<div class="min-h-screen bg-slate-50 py-6 md:py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Langganan Saya</h1>
                <p class="text-sm text-slate-500 mt-1">Produk yang otomatis dipesan ulang sesuai hari yang Anda pilih.</p>
            </div>
            <a href="{{ route('home') }}"
                class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:border-brand-500 hover:text-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Lanjut Belanja
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        @if($subscriptions->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200/80 p-10 md:p-16 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-900 mb-2">Belum ada langganan aktif</h2>
                <p class="text-slate-500 max-w-sm mx-auto mb-7 text-sm">
                    Aktifkan "Pesan Rutin" pada produk di keranjang belanja Anda dan pilih hari pengantaran — pesanan akan dibuat otomatis setiap hari itu.
                </p>
                <a href="{{ route('cart.index') }}"
                    class="inline-flex items-center h-12 px-7 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 transition-colors">
                    Buka Keranjang
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($subscriptions as $subscription)
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 md:p-5 flex flex-wrap items-center gap-4 {{ $subscription->is_active ? '' : 'opacity-60' }}">
                        <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center overflow-hidden shrink-0">
                            @if($subscription->product?->image)
                                <img src="{{ asset('storage/' . $subscription->product->image) }}" alt="{{ $subscription->product->name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            @endif
                        </div>

                        <div class="flex-1 min-w-[200px]">
                            <p class="font-bold text-slate-900">{{ $subscription->product?->name ?? 'Produk tidak tersedia' }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ (int) $subscription->quantity }} pcs setiap hari:</p>
                            <div class="flex flex-wrap gap-1 mt-1.5">
                                @foreach($subscription->days_of_week ?? [] as $day)
                                    <span class="px-2 py-0.5 rounded-full bg-brand-50 text-brand-600 text-[11px] font-semibold border border-brand-100">{{ $day }}</span>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1.5">{{ $subscription->shipping_address }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-full {{ $subscription->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                                {{ $subscription->is_active ? 'Aktif' : 'Dijeda' }}
                            </span>

                            <form action="{{ route('subscriptions.toggle', $subscription) }}" method="POST">
                                @csrf
                                <button type="submit" class="h-9 px-3 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:border-brand-400 hover:text-brand-600 transition-colors">
                                    {{ $subscription->is_active ? 'Jeda' : 'Aktifkan' }}
                                </button>
                            </form>

                            <form action="{{ route('subscriptions.destroy', $subscription) }}" method="POST" onsubmit="return confirm('Hapus langganan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="h-9 w-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:border-red-300 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
