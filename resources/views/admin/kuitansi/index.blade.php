@extends('layouts.admin')

@section('header', 'Kuitansi Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                🧾 Kuitansi Pembayaran
            </h1>
            <p class="text-slate-500 text-sm mt-1">Daftar kuitansi bukti pembayaran yang telah disetujui (Approved).</p>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('admin.kuitansi.index') }}" class="bg-white p-4 rounded-xl shadow-sm flex items-end gap-4 border border-slate-200">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Cari Kuitansi / Customer</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="No. Kuitansi atau Nama Customer" class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm">
                Cari
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                        <th class="py-3 px-4 font-semibold">No Kuitansi</th>
                        <th class="py-3 px-4 font-semibold">Customer</th>
                        <th class="py-3 px-4 font-semibold">Tgl Transaksi</th>
                        <th class="py-3 px-4 font-semibold">Nominal</th>
                        <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($kuitansis as $k)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-semibold text-indigo-600">
                                {{ $k->receipt_number ?? 'Belum Digenerate' }}
                                <div class="text-xs text-slate-400 mt-0.5">Inv: {{ $k->invoice->invoice_number ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-900">{{ $k->invoice->order->customer->name ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $k->payment_method }}</div>
                            </td>
                            <td class="py-3 px-4">
                                {{ $k->payment_date ? \Carbon\Carbon::parse($k->payment_date)->format('d M Y') : '-' }}
                            </td>
                            <td class="py-3 px-4 font-bold">
                                Rp {{ number_format($k->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.payments.receipt', $k->id) }}" target="_blank" class="inline-flex items-center gap-1 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Cetak Kuitansi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Belum ada data kuitansi
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($kuitansis->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $kuitansis->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
