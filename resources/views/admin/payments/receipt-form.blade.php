@extends('layouts.admin')
@section('header', 'Generate Kuitansi')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <a href="{{ route('admin.payments.show', $payment->id) }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali ke Detail
    </a>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h2 class="font-bold text-slate-800 mb-2 text-lg">📋 Generate Kuitansi</h2>
        <p class="text-sm text-slate-500 mb-6">Isi data perusahaan untuk kuitansi resmi (untuk customer PT/Corporate).</p>

        <div class="bg-slate-50 rounded-xl p-4 mb-6 text-sm space-y-1">
            <div><strong>Invoice:</strong> {{ $payment->invoice->invoice_number ?? '-' }}</div>
            <div><strong>Customer:</strong> {{ $payment->invoice->order->customer_name ?? '-' }}</div>
            <div><strong>Nominal:</strong> Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
        </div>

        <form action="{{ route('admin.payments.receipt-store', $payment->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Nama Perusahaan <span class="text-red-500">*</span></label>
                <input type="text" name="company_name" required value="{{ $payment->company_name }}" placeholder="PT. XYZ Indonesia"
                    class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Untuk Pembayaran <span class="text-red-500">*</span></label>
                <textarea name="payment_purpose" required rows="3" placeholder="Pembayaran invoice #INV-... untuk pembelian susu dan dairy products periode Juli 2026"
                    class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $payment->payment_purpose }}</textarea>
            </div>
            <button type="submit" class="w-full py-3 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl transition shadow-sm">Generate & Download PDF</button>
        </form>
    </div>
</div>
@endsection
