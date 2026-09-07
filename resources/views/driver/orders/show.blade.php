@extends('layouts.ops')

@section('title', 'Pesanan #' . $order->order_number . ' - DEPOSUSU')

@php
$opsRoleLabel = 'Driver';
$opsNavItems = [
    ['url' => route('driver.dashboard'), 'icon' => 'home', 'label' => 'Beranda', 'active' => false],
    ['url' => route('driver.orders.index'), 'icon' => 'truck', 'label' => 'Antar', 'active' => true],
    ['url' => route('driver.cash.index'), 'icon' => 'cash', 'label' => 'Setoran', 'active' => false],
];
@endphp

@section('content')
<a href="{{ route('driver.orders.index') }}" class="text-sm text-brand-600 font-semibold mb-3 inline-block">&larr; Kembali</a>

<div class="bg-white rounded-2xl border border-slate-200 p-4 mb-4">
    <div class="flex items-center justify-between mb-2">
        <h1 class="font-extrabold text-slate-900">#{{ $order->order_number }}</h1>
        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ $order->status->label() }}</span>
    </div>
    <p class="text-sm text-slate-600">{{ $order->customer_name }}</p>
    <p class="text-xs text-slate-400 mt-1">{{ $order->customer_address }}</p>
    <p class="text-xs text-slate-500 mt-2 font-semibold">{{ $order->payment_method === 'COD' ? 'Bayar di Tempat (COD)' : $order->payment_method }} &middot; Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
</div>

<div class="bg-white rounded-2xl border border-slate-200 p-4 mb-4">
    <p class="text-xs font-bold text-slate-500 uppercase mb-3">Item Pesanan</p>
    <div class="space-y-2">
        @foreach($order->items as $item)
            <div class="flex items-center justify-between text-sm border-b border-slate-50 last:border-0 pb-2 last:pb-0">
                <p class="font-semibold text-slate-800">{{ $item->product->name ?? 'Produk' }}</p>
                <p class="text-xs text-slate-500">{{ (int) $item->quantity }} pcs</p>
            </div>
        @endforeach
    </div>
</div>

@if($order->delivery_proof_photo)
    <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-4">
        <p class="text-xs font-bold text-slate-500 uppercase mb-3">Bukti Pengantaran</p>

        <img src="{{ $order->delivery_proof_photo }}" alt="Foto bukti pengantaran"
            class="w-full rounded-xl border border-slate-200 mb-3">

        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-[11px] text-slate-400">Diterima oleh</p>
                <p class="text-sm font-semibold text-slate-800">{{ $order->recipient_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] text-slate-400">Waktu Tiba</p>
                <p class="text-sm font-semibold text-slate-800">{{ $order->delivered_at?->format('d M Y H:i') ?? '-' }}</p>
            </div>
        </div>

        @if($order->recipient_signature)
            <div class="mt-3">
                <p class="text-[11px] text-slate-400 mb-1">Tanda Tangan</p>
                <img src="{{ $order->recipient_signature }}" alt="Tanda tangan penerima"
                    class="w-full h-32 object-contain rounded-xl border border-slate-200 bg-slate-50">
            </div>
        @endif
    </div>
@endif

@if($order->status->value === 'prepared')
    <form action="{{ route('driver.orders.pickup', $order->id) }}" method="POST">
        @csrf
        <button type="submit" class="w-full h-12 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 transition-colors">
            Ambil Pesanan
        </button>
    </form>
@elseif($order->status->value === 'ondelivery')
    <form id="finish-form" action="{{ route('driver.orders.finish', $order->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-4 space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Penerima</label>
            <input type="text" name="recipient_name" required class="w-full h-11 px-3 rounded-lg border border-slate-200 text-sm">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Foto Bukti Pengantaran</label>
            <input type="file" name="photo" accept="image/*" capture="environment" required class="text-sm w-full">
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-semibold text-slate-600">Tanda Tangan Penerima</label>
                <button type="button" id="clear-signature" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Hapus &amp; Ulangi</button>
            </div>
            <canvas id="signature-pad" class="w-full h-40 rounded-lg border border-slate-200 bg-slate-50 touch-none mb-6"></canvas>
            <input type="hidden" name="recipient_signature" id="recipient_signature">
        </div>

        <button type="submit" id="finish-submit" class="w-full h-12 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 transition-colors disabled:opacity-50">
            Selesaikan Pengantaran
        </button>
    </form>
@endif
@endsection

@push('scripts')
<script>
    (function () {
        const canvas = document.getElementById('signature-pad');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const ratio = window.devicePixelRatio || 1;
        canvas.width = canvas.clientWidth * ratio;
        canvas.height = canvas.clientHeight * ratio;
        ctx.scale(ratio, ratio);
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#1e293b';

        let drawing = false;
        let hasSignature = false;

        function pos(e) {
            const rect = canvas.getBoundingClientRect();
            const point = e.touches ? e.touches[0] : e;
            return { x: point.clientX - rect.left, y: point.clientY - rect.top };
        }

        function start(e) {
            drawing = true;
            hasSignature = true;
            const p = pos(e);
            ctx.beginPath();
            ctx.moveTo(p.x, p.y);
            e.preventDefault();
        }

        function move(e) {
            if (!drawing) return;
            const p = pos(e);
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            e.preventDefault();
        }

        function end() { drawing = false; }

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', move);
        canvas.addEventListener('mouseup', end);
        canvas.addEventListener('touchstart', start);
        canvas.addEventListener('touchmove', move);
        canvas.addEventListener('touchend', end);

        document.getElementById('clear-signature').addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasSignature = false;
        });

        document.getElementById('finish-form').addEventListener('submit', (e) => {
            if (!hasSignature) {
                e.preventDefault();
                alert('Mohon minta tanda tangan penerima terlebih dahulu.');
                return;
            }

            if (!confirm('Selesaikan pengantaran pesanan ini? Pastikan foto dan tanda tangan sudah benar.')) {
                e.preventDefault();
                return;
            }

            document.getElementById('recipient_signature').value = canvas.toDataURL('image/png');
        });
    })();
</script>
@endpush
