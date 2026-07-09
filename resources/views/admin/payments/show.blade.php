@extends('layouts.admin')
@section('header', 'Detail Pembayaran')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <a href="{{ route('admin.payments.index') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Payment Info --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h2 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $payment->status_color }}-100 text-{{ $payment->status_color }}-700 capitalize">{{ $payment->status }}</span>
                    Detail Pembayaran
                </h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-slate-500">Invoice</span><div class="font-mono font-bold text-blue-700">{{ $payment->invoice->invoice_number ?? '-' }}</div></div>
                    <div><span class="text-slate-500">Customer</span><div class="font-semibold text-slate-800">{{ $payment->invoice->order->customer_name ?? '-' }}</div></div>
                    <div><span class="text-slate-500">Metode</span><div class="font-medium">{{ $payment->payment_method_label }}</div></div>
                    <div><span class="text-slate-500">Tanggal Bayar</span><div class="font-medium">{{ $payment->payment_date->format('d M Y') }}</div></div>
                    <div><span class="text-slate-500">Nominal</span><div class="text-xl font-extrabold text-slate-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div></div>
                    <div><span class="text-slate-500">Referensi</span><div class="font-mono">{{ $payment->reference_number ?: '-' }}</div></div>
                </div>
                @if($payment->notes)
                <div class="mt-4 p-3 bg-slate-50 rounded-xl text-sm text-slate-600">
                    <strong>Catatan:</strong> {{ $payment->notes }}
                </div>
                @endif
                @if($payment->proof_image)
                <div class="mt-4">
                    <span class="text-xs font-semibold text-slate-500 uppercase">Bukti Transfer</span>
                    <img src="{{ asset('storage/' . $payment->proof_image) }}" alt="Bukti" class="mt-2 rounded-xl border max-h-64 object-contain">
                </div>
                @endif
            </div>

            {{-- Order Items --}}
            @if($payment->invoice && $payment->invoice->order)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-bold text-slate-800">Item Pesanan</h3></div>
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Produk</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Qty</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($payment->invoice->order->items as $item)
                        <tr>
                            <td class="px-5 py-3 text-sm font-medium text-slate-800">{{ $item->product->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-sm text-center">{{ $item->quantity }}</td>
                            <td class="px-5 py-3 text-sm text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Actions Sidebar --}}
        <div class="space-y-4">
            @if($payment->status === 'pending')
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-3">
                <h3 class="font-bold text-slate-800">Konfirmasi Pembayaran</h3>
                <form action="{{ route('admin.payments.approve', $payment->id) }}" method="POST">
                    @csrf
                    <button class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition text-sm">✅ Approve</button>
                </form>
                <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST" class="space-y-2">
                    @csrf
                    <textarea name="notes" rows="2" placeholder="Alasan reject..." class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm"></textarea>
                    <button class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition text-sm">❌ Reject</button>
                </form>
            </div>
            @endif

            {{-- Kuitansi --}}
            @if($payment->status === 'approved')
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-3">
                <h3 class="font-bold text-slate-800">Kuitansi</h3>
                @if($payment->receipt_number)
                <a href="{{ route('admin.payments.receipt', $payment->id) }}" class="block w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition text-sm text-center">📄 Download Kuitansi PDF</a>
                @else
                <a href="{{ route('admin.payments.receipt-form', $payment->id) }}" class="block w-full py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl transition text-sm text-center">📝 Generate Kuitansi</a>
                @endif
            </div>
            @endif

            {{-- Confirmed info --}}
            @if($payment->confirmed_at)
            <div class="bg-slate-50 rounded-2xl p-4 text-sm text-slate-600 space-y-1">
                <div><strong>Dikonfirmasi:</strong> {{ $payment->confirmed_at->format('d M Y H:i') }}</div>
                <div><strong>Oleh:</strong> {{ $payment->confirmedBy->name ?? '-' }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
