@extends('layouts.admin')

@section('header', 'Dashboard Utama')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Stat Card 1 -->
    <div class="bg-white rounded-lg shadow p-6 border border-slate-100 border-l-4 border-l-blue-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase">Omzet Hari Ini</p>
                <p class="text-2xl font-bold text-slate-800">Rp 12.500.000</p>
            </div>
        </div>
    </div>

    <!-- Stat Card 2 -->
    <div class="bg-white rounded-lg shadow p-6 border border-slate-100 border-l-4 border-l-green-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase">Order Masuk</p>
                <p class="text-2xl font-bold text-slate-800">145</p>
            </div>
        </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="bg-white rounded-lg shadow p-6 border border-slate-100 border-l-4 border-l-red-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase">Stok Menipis</p>
                <p class="text-2xl font-bold text-slate-800">12 Item</p>
            </div>
        </div>
    </div>

    <!-- Stat Card 4 -->
    <div class="bg-white rounded-lg shadow p-6 border border-slate-100 border-l-4 border-l-purple-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase">Customer Aktif</p>
                <p class="text-2xl font-bold text-slate-800">1,240</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Chart Section -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow border border-slate-100 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Grafik Omzet & Penjualan (Bulan Ini)</h3>
        <div class="h-64 bg-slate-50 border border-slate-200 rounded flex items-center justify-center">
            <span class="text-slate-400">[Area Grafik Chart.js]</span>
        </div>
    </div>

    <!-- Quick Actions / Notifications -->
    <div class="bg-white rounded-lg shadow border border-slate-100 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Quick Actions</h3>
        <div class="space-y-3">
            <a href="#" class="block p-3 rounded border border-slate-200 hover:bg-slate-50 transition">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-slate-700">📦 Buka Aplikasi POS Kasir</span>
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
            <a href="#" class="block p-3 rounded border border-slate-200 hover:bg-slate-50 transition">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-slate-700">🛒 Cek Order Baru (5)</span>
                    <span class="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded-full">New</span>
                </div>
            </a>
            <a href="#" class="block p-3 rounded border border-slate-200 hover:bg-slate-50 transition">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-slate-700">🤖 AI Generate Thumbnail</span>
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
            </a>
            <a href="#" class="block p-3 rounded border border-slate-200 hover:bg-slate-50 transition">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-slate-700">🔄 Sinkronisasi Tokopedia</span>
                    <span class="text-xs text-slate-400">2 jam lalu</span>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection