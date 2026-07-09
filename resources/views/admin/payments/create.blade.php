@extends('layouts.admin')
@section('header', 'Catat Pembayaran')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('admin.payments.index') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali
    </a>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h2 class="font-bold text-slate-800 mb-6 text-lg">Catat Pembayaran Baru</h2>
        <form action="{{ route('admin.payments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Invoice <span class="text-red-500">*</span></label>
                <select name="invoice_id" required class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50">
                    <option value="">-- Pilih Invoice --</option>
                    @foreach($unpaidInvoices as $inv)
                    <option value="{{ $inv->id }}" {{ ($invoice && $invoice->id == $inv->id) ? 'selected' : '' }}>
                        {{ $inv->invoice_number }} — {{ $inv->order->customer_name ?? 'N/A' }} — Rp {{ number_format($inv->total_amount, 0, ',', '.') }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Metode <span class="text-red-500">*</span></label>
                    <select name="payment_method" required class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">
                        <option value="transfer">🏦 Transfer</option>
                        <option value="cash">💵 Cash</option>
                        <option value="qris">📱 QRIS</option>
                        <option value="wallet">👜 E-Wallet</option>
                        <option value="cod">🚚 COD</option>
                        <option value="piutang">📋 Piutang</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Nominal <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" required min="1" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50" placeholder="100000">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Tanggal Bayar <span class="text-red-500">*</span></label>
                    <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">No. Referensi</label>
                    <input type="text" name="reference_number" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50" placeholder="TRF-12345">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Bukti Transfer (foto)</label>
                <input type="file" name="proof_image" accept="image/*" class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm bg-slate-50">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Catatan</label>
                <textarea name="notes" rows="2" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50" placeholder="Opsional..."></textarea>
            </div>
            <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl transition shadow-sm">Simpan Pembayaran</button>
        </form>
    </div>
</div>
@endsection
