@extends('layouts.admin')

@section('header', 'Expired Tracking & Product Batches')

@section('content')
<div class="space-y-6">
    <!-- Expired Warning Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-red-50 border border-red-200 rounded-2xl p-5 flex items-center gap-4">
            <div class="p-3 bg-red-100 text-red-600 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-red-600 font-bold uppercase tracking-wider">Sudah Kedaluwarsa</p>
                <h4 class="text-2xl font-black text-slate-800">2 Batches</h4>
                <p class="text-xs text-red-500 mt-1">Harus segera dimusnahkan / retur</p>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-center gap-4">
            <div class="p-3 bg-amber-100 text-amber-600 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-amber-600 font-bold uppercase tracking-wider">Mendekati Kedaluwarsa (&lt; 30 Hari)</p>
                <h4 class="text-2xl font-black text-slate-800">5 Batches</h4>
                <p class="text-xs text-amber-600 mt-1">Disarankan promo / bundling</p>
            </div>
        </div>

        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-center gap-4">
            <div class="p-3 bg-emerald-100 text-emerald-600 rounded-full">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-emerald-600 font-bold uppercase tracking-wider">Aman (&gt; 90 Hari)</p>
                <h4 class="text-2xl font-black text-slate-800">45 Batches</h4>
                <p class="text-xs text-emerald-500 mt-1">Stok aman & layak jual</p>
            </div>
        </div>
    </div>

    <!-- Batch tracking list -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Batch List & Expired Tracking</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase">Batch ID</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase">Nama Produk</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase font-mono">SKU</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase">Qty Batch</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase">Expired Date</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase rounded-r-lg">Status Kelayakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-bold text-slate-700">#BT-SUSU-001</td>
                        <td class="px-6 py-4">Susu Kambing Etawa Botol Murni</td>
                        <td class="px-6 py-4 font-mono text-xs">SKU-001</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-700">12 Botol</td>
                        <td class="px-6 py-4 text-rose-600 font-bold">{{ now()->subDays(2)->format('d M Y') }}</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100 rounded-full">Kedaluwarsa</span></td>
                    </tr>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-bold text-slate-700">#BT-YOGU-003</td>
                        <td class="px-6 py-4">Yogurt Strawberry Cup Pasteur</td>
                        <td class="px-6 py-4 font-mono text-xs">SKU-004</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-700">45 Cup</td>
                        <td class="px-6 py-4 text-amber-600 font-bold">{{ now()->addDays(12)->format('d M Y') }}</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100 rounded-full">12 Hari Tersisa</span></td>
                    </tr>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-bold text-slate-700">#BT-SAPI-002</td>
                        <td class="px-6 py-4">Susu Sapi Murni Kemasan Liter</td>
                        <td class="px-6 py-4 font-mono text-xs">SKU-002</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-700">80 Liter</td>
                        <td class="px-6 py-4 text-slate-600 font-bold">{{ now()->addDays(120)->format('d M Y') }}</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full">Aman</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
