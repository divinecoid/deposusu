@extends('layouts.admin')

@section('header', 'Invoice Detail')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('admin.invoices.index') }}" class="text-sm text-slate-600 hover:text-blue-600 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Invoice
        </a>
        <div class="flex flex-wrap gap-2">
            {{-- WhatsApp Share --}}
            @php
                $waText = "Halo, berikut adalah tagihan Invoice *{$invoice->invoice_number}* sebesar *Rp " . number_format($invoice->total_amount, 0, ',', '.') . "*.\nSilakan lakukan pembayaran dan konfirmasi melalui link berikut atau balas chat ini.";
                $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $invoice->order->customer_phone ?? '') . "?text=" . urlencode($waText);
            @endphp
            @if($invoice->order->customer_phone)
            <a href="{{ $waUrl }}" target="_blank" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-xl shadow-xs transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.733-1.455L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.42 9.864-9.864.002-2.637-1.023-5.117-2.884-6.979C16.592 1.9 14.117.872 11.48.872c-5.441 0-9.866 4.42-9.87 9.865-.001 1.777.464 3.506 1.346 5.04L1.936 21.54l6.002-1.573c-1.56-.008-1.29.02-1.29-.813zm10.742-7.447c-.29-.145-1.72-.848-1.986-.944-.266-.096-.46-.145-.654.145-.193.29-.75.944-.919 1.138-.17.194-.339.218-.63.073-.29-.145-1.229-.453-2.34-1.444-.863-.77-1.447-1.721-1.616-2.011-.17-.29-.018-.447.127-.591.13-.13.29-.339.435-.508.145-.17.193-.29.29-.484.097-.193.048-.363-.024-.508-.073-.145-.654-1.573-.896-2.153-.236-.569-.475-.49-.654-.5l-.558-.01c-.193 0-.508.073-.774.363-.266.29-1.016.992-1.016 2.42 0 1.427 1.04 2.807 1.185 3.002.145.193 2.049 3.129 4.963 4.387.693.3 1.234.479 1.655.613.697.221 1.33.19 1.83.115.558-.084 1.72-.702 1.962-1.38.242-.678.242-1.258.17-1.38-.072-.12-.266-.193-.556-.339z"/></svg>
                WhatsApp Ke Customer
            </a>
            @endif

            {{-- Download PDF --}}
            <a href="{{ route('admin.invoices.pdf', $invoice->id) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-xl shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Invoice Main Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div class="flex justify-between items-start pb-4 border-b border-slate-100">
                    <div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Nomor Invoice</div>
                        <h2 class="text-xl font-bold text-slate-800 font-mono">{{ $invoice->invoice_number }}</h2>
                        <div class="text-xs text-slate-400 mt-1">Tanggal Terbit: {{ $invoice->issue_date->format('d M Y') }}</div>
                    </div>
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold tracking-wide uppercase
                            {{ $invoice->status === 'PAID' ? 'bg-emerald-100 text-emerald-700' : ($invoice->status === 'CANCELLED' ? 'bg-slate-100 text-slate-500' : 'bg-amber-100 text-amber-700') }}">
                            {{ $invoice->status }}
                        </span>
                    </div>
                </div>

                {{-- Billing Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <h4 class="font-bold text-slate-500 uppercase tracking-wider text-xs mb-2">Ditagih Ke:</h4>
                        <div class="font-bold text-slate-800">{{ $invoice->order->customer_name }}</div>
                        @if($invoice->order->customer_phone)
                        <div class="text-slate-500 mt-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $invoice->order->customer_phone }}
                        </div>
                        @endif
                        @if($invoice->order->customer_address)
                        <div class="text-slate-500 mt-1 flex items-start gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>{{ $invoice->order->customer_address }}</span>
                        </div>
                        @endif
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-500 uppercase tracking-wider text-xs mb-2">Informasi Pengiriman:</h4>
                        @if($invoice->order->delivery_date)
                        <div class="text-slate-700 font-medium">Tanggal Kirim: {{ \Carbon\Carbon::parse($invoice->order->delivery_date)->format('d M Y') }}</div>
                        @if($invoice->order->delivery_slot)
                        <div class="text-slate-500 text-xs mt-0.5 capitalize">Sesi: {{ $invoice->order->delivery_slot }}</div>
                        @endif
                        @else
                        <div class="text-slate-400">Belum dijadwalkan</div>
                        @endif
                    </div>
                </div>

                {{-- Products Table --}}
                <div class="border border-slate-100 rounded-xl overflow-hidden mt-6">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Produk</th>
                                <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Harga</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase w-20">Qty</th>
                                <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @foreach($invoice->order->items as $item)
                            <tr>
                                <td class="px-5 py-3 text-sm font-semibold text-slate-800">{{ $item->product->name ?? 'N/A' }}</td>
                                <td class="px-5 py-3 text-sm text-right text-slate-500">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-sm text-center text-slate-600">{{ $item->quantity }}</td>
                                <td class="px-5 py-3 text-sm text-right font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50/50 text-sm font-semibold divide-y divide-slate-100 border-t border-slate-100">
                            <tr>
                                <td colspan="3" class="px-5 py-2.5 text-right text-slate-500">Subtotal:</td>
                                <td class="px-5 py-2.5 text-right text-slate-800">Rp {{ number_format($invoice->subtotal_amount, 0, ',', '.') }}</td>
                            </tr>
                            @if($invoice->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="px-5 py-2.5 text-right text-slate-500">Diskon:</td>
                                <td class="px-5 py-2.5 text-right text-red-500">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr class="text-base font-bold bg-slate-50">
                                <td colspan="3" class="px-5 py-3 text-right text-slate-800">Grand Total:</td>
                                <td class="px-5 py-3 text-right text-blue-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- History Pembayaran --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                <h3 class="font-bold text-slate-800">Riwayat Konfirmasi Pembayaran</h3>
                <div class="divide-y divide-slate-50">
                    @forelse($invoice->payments as $payment)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold text-slate-700">{{ $payment->payment_method_label }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">
                                {{ $payment->payment_date->format('d M Y') }}
                                @if($payment->reference_number) · Ref: {{ $payment->reference_number }} @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-slate-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-{{ $payment->status_color }}-100 text-{{ $payment->status_color }}-700 capitalize">
                                {{ $payment->status }}
                            </span>
                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="text-xs text-blue-600 hover:underline">Detail</a>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 py-2">Belum ada pembayaran dicatat</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right Column: Actions --}}
        <div class="space-y-4">
            {{-- Update Status form --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                <h3 class="font-bold text-slate-800">Status Pembayaran</h3>
                <form action="{{ route('admin.invoices.updateStatus', $invoice->id) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <select name="status" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="UNPAID" {{ $invoice->status === 'UNPAID' ? 'selected' : '' }}>UNPAID (Belum Bayar)</option>
                        <option value="PAID" {{ $invoice->status === 'PAID' ? 'selected' : '' }}>PAID (Lunas)</option>
                        <option value="CANCELLED" {{ $invoice->status === 'CANCELLED' ? 'selected' : '' }}>CANCELLED (Dibatalkan)</option>
                    </select>
                    <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition text-sm">Update Status</button>
                </form>
            </div>

            @if($invoice->status !== 'PAID')
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 text-center">
                <p class="text-sm text-slate-500 mb-4">Ingin mencatat pembayaran masuk untuk invoice ini?</p>
                <a href="{{ route('admin.payments.create', ['invoice_id' => $invoice->id]) }}" class="block w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl transition text-sm">
                    Catat Pembayaran Masuk
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
