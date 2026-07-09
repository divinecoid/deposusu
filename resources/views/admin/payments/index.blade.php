@extends('layouts.admin')
@section('header', 'Payment Management')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></span>
                Payment Management
            </h1>
            <p class="text-slate-500 text-sm mt-1">Konfirmasi pembayaran customer</p>
        </div>
        <a href="{{ route('admin.payments.create') }}" class="flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Pembayaran
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
            ['l'=>'Semua','k'=>'all','c'=>'slate','n'=>$counts['all']],
            ['l'=>'Pending','k'=>'pending','c'=>'amber','n'=>$counts['pending']],
            ['l'=>'Approved','k'=>'approved','c'=>'emerald','n'=>$counts['approved']],
            ['l'=>'Rejected','k'=>'rejected','c'=>'red','n'=>$counts['rejected']],
        ] as $s)
        <a href="{{ route('admin.payments.index', ['status' => $s['k']]) }}"
            class="bg-white rounded-2xl border {{ $status === $s['k'] ? 'border-amber-400 ring-2 ring-amber-100' : 'border-slate-100' }} p-4 shadow-sm hover:shadow-md transition text-center">
            <div class="text-2xl font-extrabold text-slate-800">{{ $s['n'] }}</div>
            <div class="text-xs text-slate-500 font-medium">{{ $s['l'] }}</div>
        </a>
        @endforeach
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Invoice</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Customer</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Metode</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Nominal</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($payments as $p)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-5 py-4 text-sm">
                            <div class="font-mono font-bold text-blue-700">{{ $p->invoice->invoice_number ?? '-' }}</div>
                            <div class="text-xs text-slate-400">{{ $p->payment_date->format('d M Y') }}</div>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-700">{{ $p->invoice->order->customer_name ?? '-' }}</td>
                        <td class="px-5 py-4 text-sm">{{ $p->payment_method_label }}</td>
                        <td class="px-5 py-4 text-sm text-right font-bold text-slate-800">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $p->status_color }}-100 text-{{ $p->status_color }}-700 capitalize">{{ $p->status }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="{{ route('admin.payments.show', $p->id) }}" class="text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-slate-400">Belum ada data pembayaran</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $payments->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection
