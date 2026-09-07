@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-gray-50 pb-24 md:pb-12">
    <!-- Header Section (Astro Style) -->
    <div class="bg-white px-4 pt-8 pb-6 shadow-sm rounded-b-3xl mb-4">
        <div class="flex justify-between items-start max-w-3xl mx-auto">
            <div class="flex items-center gap-4">
                <!-- User Avatar -->
                <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl font-black shadow-inner">
                    {{ substr($user->name, 0, 1) }}
                </div>
                
                <!-- User Info -->
                <div class="flex flex-col">
                    <h1 class="text-xl font-bold text-gray-900 tracking-tight">{{ $user->name }}</h1>
                    <div class="flex items-center gap-1 mt-1 text-gray-500">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <p class="text-sm truncate max-w-[200px]">
                            {{ $customer && $customer->address ? $customer->address : 'Belum ada alamat' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notification Icon -->
            <a href="{{ route('notifications.index') }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors relative inline-block">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                @if(Auth::user()->unreadNotifications()->count() > 0)
                    <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
                @endif
            </a>
        </div>
    </div>

    <!-- Vertical Menu List -->
    <div class="max-w-3xl mx-auto px-4 space-y-6">
        
        <!-- Section: Pesanan -->
        <div>
            <a href="{{ route('transactions.index') }}" class="flex items-center justify-between p-4 bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-100 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    </div>
                    <span class="font-bold text-gray-800 text-sm md:text-base">Pesanan Saya</span>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>

        <!-- Section: Pengaturan -->
        <div>
            <h2 class="text-sm font-bold text-gray-900 mb-3 px-1">Pengaturan</h2>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-50">
                
                <!-- Detail Akun -->
                <a href="{{ route('profile.edit') }}" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-800 text-sm md:text-base">Detail Akun</span>
                            <span class="text-xs text-gray-500">Nama, email, nomor HP</span>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </a>

                <!-- Pengaturan PIN -->
                <button type="button" onclick="showNotification('Fitur ini akan segera hadir', 'info')" class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group text-left">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-cyan-50 text-cyan-600 rounded-full flex items-center justify-center group-hover:bg-cyan-500 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-800 text-sm md:text-base">Pengaturan PIN</span>
                            <span class="text-xs text-gray-500">Buat PIN, ubah PIN</span>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </button>

                <!-- Pembayaran -->
                <button type="button" onclick="showNotification('Fitur ini akan segera hadir', 'info')" class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group text-left">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-green-50 text-green-600 rounded-full flex items-center justify-center group-hover:bg-green-500 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-800 text-sm md:text-base">Pembayaran</span>
                            <span class="text-xs text-gray-500">E-wallet, kartu</span>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </button>

                <!-- Alamat Tersimpan -->
                <button type="button" onclick="showNotification('Fitur ini akan segera hadir', 'info')" class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group text-left">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center group-hover:bg-orange-500 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-800 text-sm md:text-base">Alamat Tersimpan</span>
                            <span class="text-xs text-gray-500">Tambah/edit alamat utama</span>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </button>

                <!-- Pengaturan Notifikasi -->
                <button type="button" onclick="showNotification('Fitur ini akan segera hadir', 'info')" class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group text-left">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center group-hover:bg-purple-500 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-800 text-sm md:text-base">Pengaturan Notifikasi</span>
                            <span class="text-xs text-gray-500">Promo, order update, push</span>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>

        <!-- Section: Bantuan -->
        <div>
            <h2 class="text-sm font-bold text-gray-900 mb-3 px-1">Bantuan</h2>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-50">
                <button type="button" onclick="showNotification('Fitur ini akan segera hadir', 'info')" class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group text-left">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-full flex items-center justify-center group-hover:bg-teal-500 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <span class="font-bold text-gray-800 text-sm md:text-base">FAQ</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </button>
                
                <button type="button" onclick="showNotification('Fitur ini akan segera hadir', 'info')" class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group text-left">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-full flex items-center justify-center group-hover:bg-teal-500 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-800 text-sm md:text-base">Deposusu Care</span>
                            <span class="text-xs text-gray-500">WhatsApp, CS</span>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>

        <!-- Section: Logout -->
        <div class="pt-4 pb-2">
            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center justify-between p-4 bg-red-50 rounded-2xl border border-red-100 hover:bg-red-100 hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white text-red-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        </div>
                        <span class="font-bold text-red-600 text-sm md:text-base">Keluar dari Akun</span>
                    </div>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
