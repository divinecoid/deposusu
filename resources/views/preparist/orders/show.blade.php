@extends('layouts.ops')

@section('title', 'Pesanan #' . $order->order_number . ' - DEPOSUSU')

@php
$opsRoleLabel = 'Preparist';
$opsNavItems = [
    ['url' => route('preparist.dashboard'), 'icon' => 'home', 'label' => 'Beranda', 'active' => false],
    ['url' => route('preparist.orders.index'), 'icon' => 'archive', 'label' => 'Pesanan', 'active' => true],
];
@endphp

@section('content')
<a href="{{ route('preparist.orders.index') }}" class="text-sm text-brand-600 font-semibold mb-3 inline-block">&larr; Kembali ke antrian</a>

<div class="bg-white rounded-2xl border border-slate-200 p-4 mb-4">
    <div class="flex items-center justify-between mb-2">
        <h1 class="font-extrabold text-slate-900">#{{ $order->order_number }}</h1>
        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ $order->status->label() }}</span>
    </div>
    <p class="text-sm text-slate-600">{{ $order->customer_name }}</p>
    <p class="text-xs text-slate-400 mt-1">{{ $order->customer_address }}</p>
</div>

<div class="bg-white rounded-2xl border border-slate-200 p-4 mb-4">
    <p class="text-xs font-bold text-slate-500 uppercase mb-3">Item Pesanan</p>
    <div class="space-y-2">
        @foreach($order->items as $item)
            <div class="flex items-center justify-between text-sm border-b border-slate-50 last:border-0 pb-2 last:pb-0">
                <div>
                    <p class="font-semibold text-slate-800">{{ $item->product->name ?? 'Produk' }}</p>
                    <p class="text-xs text-slate-400">{{ (int) $item->quantity }} pcs</p>
                </div>
                @if($order->status->value === 'onpreparation')
                    <input type="number" form="finish-form" name="items[{{ $item->id }}]" value="{{ $item->checked_quantity ?: $item->quantity }}" min="0"
                        class="w-20 h-9 px-2 rounded-lg border border-slate-200 text-sm text-right">
                @endif
            </div>
        @endforeach
    </div>
</div>

@if($order->status->value === 'onprocess')
    <form action="{{ route('preparist.orders.start', $order->id) }}" method="POST">
        @csrf
        <button type="submit" class="w-full h-12 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 transition-colors">
            Mulai Siapkan Pesanan
        </button>
    </form>
@elseif($order->status->value === 'onpreparation')
    <form id="finish-form" action="{{ route('preparist.orders.finish', $order->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-4 mb-4 space-y-3">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Foto Isi Pesanan (opsional)</label>
            <input type="file" name="photo_isi" accept="image/*" capture="environment" class="text-sm w-full">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Foto Pesanan Selesai Dikemas (opsional)</label>
            <input type="file" name="photo_final" accept="image/*" capture="environment" class="text-sm w-full">
        </div>
    </form>

    <div class="flex gap-3">
        <form action="{{ route('preparist.orders.cancel', $order->id) }}" method="POST" class="flex-1">
            @csrf
            <button type="submit" class="w-full h-12 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition-colors">
                Batalkan
            </button>
        </form>
        <button type="submit" form="finish-form" class="flex-1 h-12 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 transition-colors">
            Selesai Disiapkan
        </button>
    </div>
@endif
@endsection

@push('scripts')
<script>
    document.getElementById('finish-form')?.addEventListener('submit', (e) => {
        if (!confirm('Selesaikan penyiapan pesanan ini? Pastikan semua item sudah dicek.')) {
            e.preventDefault();
        }
    });
</script>
@endpush
