@extends('layouts.admin')

@section('header', 'Shift Kasir & Rekonsiliasi')

@section('content')
<div class="space-y-6">
    <!-- Active Shift Card -->
    <div class="bg-gradient-to-br from-indigo-800 to-slate-900 text-white rounded-2xl shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-1 bg-emerald-500 text-white font-bold text-[10px] uppercase rounded-full tracking-wider animate-pulse">Shift Aktif</span>
                <h3 class="text-xl font-bold mt-2">Shift Siang (14:00 - 22:00)</h3>
                <p class="text-xs text-slate-300 mt-1">Kasir Bertugas: <strong>Kasir Utama</strong></p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs text-slate-400 uppercase font-semibold">Uang Modal Awal</p>
                    <p class="text-lg font-bold">Rp 500.000</p>
                </div>
                <div class="h-10 w-px bg-slate-700"></div>
                <div class="text-right">
                    <p class="text-xs text-slate-400 uppercase font-semibold">Total POS Offline</p>
                    <p class="text-lg font-bold text-emerald-400">Rp {{ number_format(\App\Models\TrxOrder::where('source', 'kasir')->sum('total_amount'), 0, ',', '.') }}</p>
                </div>
            </div>
            <button class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-xl transition shadow">
                Tutup Shift & Print Laporan
            </button>
        </div>
    </div>

    <!-- Historical Shifts -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Riwayat Shift Kasir</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase">Tanggal</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase">Nama Kasir</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase">Shift</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase">Modal Awal</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase">Total Sales POS</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase">Uang Aktual</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-600 uppercase rounded-r-lg">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-slate-500">Yesterday</td>
                        <td class="px-6 py-4 font-bold text-slate-700">Felinika Kasir</td>
                        <td class="px-6 py-4 text-slate-600">Pagi (06:00 - 14:00)</td>
                        <td class="px-6 py-4 text-right font-mono text-slate-600">Rp 500.000</td>
                        <td class="px-6 py-4 text-right font-mono font-bold text-slate-700">Rp 1.250.000</td>
                        <td class="px-6 py-4 text-right font-mono font-bold text-emerald-600">Rp 1.750.000</td>
                        <td class="px-6 py-4"><span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-xs">Closed</span></td>
                    </tr>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-slate-500">2 days ago</td>
                        <td class="px-6 py-4 font-bold text-slate-700">Felinika Kasir</td>
                        <td class="px-6 py-4 text-slate-600">Siang (14:00 - 22:00)</td>
                        <td class="px-6 py-4 text-right font-mono text-slate-600">Rp 500.000</td>
                        <td class="px-6 py-4 text-right font-mono font-bold text-slate-700">Rp 850.000</td>
                        <td class="px-6 py-4 text-right font-mono font-bold text-emerald-600">Rp 1.350.000</td>
                        <td class="px-6 py-4"><span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-xs">Closed</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
