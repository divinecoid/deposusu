@extends('layouts.customer')

@section('title', 'Wishlist Saya - DEPOSUSU')

@section('content')
<div class="min-h-screen bg-slate-50 py-6 md:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Wishlist Saya</h1>
                <p class="text-sm text-slate-500 mt-1">{{ $wishlistItems->count() }} produk disimpan untuk nanti</p>
            </div>
            <a href="{{ route('home') }}"
                class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:border-brand-500 hover:text-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Lanjut Belanja
            </a>
        </div>

        @if($wishlistItems->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200/80 p-10 md:p-16 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-900 mb-2">Wishlist Anda masih kosong</h2>
                <p class="text-slate-500 max-w-sm mx-auto mb-7 text-sm">Tekan ikon hati pada produk untuk menyimpannya di sini.</p>
                <a href="{{ route('home') }}"
                    class="inline-flex items-center h-12 px-7 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 transition-colors">
                    Jelajahi Produk
                </a>
            </div>
        @else
            @include('customer.partials.product_grid', ['products' => $wishlistItems])
        @endif
    </div>
</div>
@endsection
