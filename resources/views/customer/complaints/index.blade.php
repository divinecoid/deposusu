@extends('layouts.customer')

@section('title', 'Komplain Saya - DEPOSUSU')

@section('content')
<div class="min-h-screen bg-slate-50 py-6 md:py-10">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Komplain Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Riwayat komplain yang Anda ajukan.</p>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        @if($complaints->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200/80 p-10 md:p-16 text-center">
                <h2 class="text-xl font-bold text-slate-900 mb-2">Belum ada komplain</h2>
                <p class="text-slate-500 max-w-sm mx-auto text-sm">Ajukan komplain dari halaman detail pesanan jika ada kendala.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($complaints as $complaint)
                    @php
                        $statusBadge = [
                            'open' => 'bg-amber-100 text-amber-800',
                            'in_progress' => 'bg-blue-100 text-blue-800',
                            'resolved' => 'bg-emerald-100 text-emerald-800',
                        ][$complaint->status] ?? 'bg-slate-100 text-slate-800';
                        $statusLabel = ['open' => 'Menunggu', 'in_progress' => 'Diproses', 'resolved' => 'Selesai'][$complaint->status] ?? $complaint->status;
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4">
                        <div class="flex items-center justify-between gap-3 mb-1.5">
                            <p class="font-bold text-slate-900 text-sm">{{ $complaint->categoryLabel() }}</p>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $statusBadge }}">{{ $statusLabel }}</span>
                        </div>
                        <p class="text-xs text-slate-400 mb-2">Pesanan #{{ $complaint->order->order_number ?? '-' }} &middot; {{ $complaint->created_at->format('d M Y H:i') }}</p>
                        <p class="text-sm text-slate-600">{{ $complaint->description }}</p>
                        @if($complaint->resolution_note)
                            <div class="mt-3 pt-3 border-t border-slate-100">
                                <p class="text-xs font-semibold text-slate-500 mb-1">Tanggapan tim kami:</p>
                                <p class="text-sm text-slate-700">{{ $complaint->resolution_note }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
