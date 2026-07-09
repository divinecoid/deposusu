@extends('layouts.admin')

@section('header', 'Detail Purchase Order')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.suppliers.po.index') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali ke Daftar PO
        </a>
        <div>
            {{-- Print action or custom options --}}
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- PO Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="flex justify-between items-start pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <span class="text-xs font-mono font-bold text-orange-600 uppercase tracking-wide">Purchase Order</span>
                        <h2 class="text-xl font-bold text-slate-800 font-mono">{{ $purchaseOrder->po_number }}</h2>
                        <div class="text-xs text-slate-400 mt-1">Dibuat Oleh: {{ $purchaseOrder->createdBy->name ?? 'Admin' }}</div>
                    </div>
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold tracking-wide uppercase bg-{{ $purchaseOrder->status_color }}-100 text-{{ $purchaseOrder->status_color }}-850">
                            {{ $purchaseOrder->status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm text-slate-700">
                    <div>
                        <span class="text-slate-400 font-semibold block text-xs uppercase mb-1">Supplier:</span>
                        <a href="{{ route('admin.suppliers.show', $purchaseOrder->supplier_id) }}" class="font-bold text-blue-600 hover:underline">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</a>
                        <div class="text-xs text-slate-500 mt-1">{{ $purchaseOrder->supplier->address }}</div>
                    </div>
                    <div class="space-y-1">
                        <div><span class="text-slate-400 font-semibold text-xs uppercase mr-1">Tanggal Order:</span> {{ $purchaseOrder->order_date->format('d M Y') }}</div>
                        <div><span class="text-slate-400 font-semibold text-xs uppercase mr-1">Estimasi Tiba:</span> {{ $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('d M Y') : '-' }}</div>
                        <div><span class="text-slate-400 font-semibold text-xs uppercase mr-1">Tgl Diterima:</span> {{ $purchaseOrder->received_date ? $purchaseOrder->received_date->format('d M Y') : '-' }}</div>
                        <div><span class="text-slate-400 font-semibold text-xs uppercase mr-1">Jatuh Tempo:</span> {{ $purchaseOrder->payment_due_date ? $purchaseOrder->payment_due_date->format('d M Y') : '-' }}</div>
                    </div>
                </div>

                @if($purchaseOrder->notes)
                <div class="mt-4 p-3 bg-slate-50 rounded-xl text-sm text-slate-600">
                    <strong>Catatan:</strong> {{ $purchaseOrder->notes }}
                </div>
                @endif
            </div>

            {{-- PO items table --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-bold text-slate-800">Daftar Barang yang Dipesan</h3></div>
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Nama Barang</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Unit</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Qty</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Harga Satuan</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($purchaseOrder->items as $item)
                        <tr>
                            <td class="px-5 py-3 text-sm font-medium text-slate-800">
                                {{ $item->product_name }}
                                @if($item->product_id)
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">ID: {{ $item->product_id }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-sm text-center text-slate-600">{{ $item->unit }}</td>
                            <td class="px-5 py-3 text-sm text-center font-semibold text-slate-700">{{ number_format($item->quantity, 0) }}</td>
                            <td class="px-5 py-3 text-sm text-right text-slate-500">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-sm text-right font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 font-semibold border-t border-slate-100 text-sm">
                        <tr>
                            <td colspan="4" class="px-5 py-3 text-right text-slate-500">Total Purchase Order:</td>
                            <td class="px-5 py-3 text-right text-orange-600 font-bold">Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Sidebar Actions --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Status Alur PO</h3>
                
                <form action="{{ route('admin.suppliers.po.updateStatus', $purchaseOrder->id) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Ubah Status:</label>
                        <select name="status" class="w-full border border-slate-200 rounded-xl py-2 px-3 text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="draft" {{ $purchaseOrder->status === 'draft' ? 'selected' : '' }}>Draft (Pengajuan)</option>
                            <option value="ordered" {{ $purchaseOrder->status === 'ordered' ? 'selected' : '' }}>Ordered (Sudah Dipesan)</option>
                            <option value="received" {{ $purchaseOrder->status === 'received' ? 'selected' : '' }}>Received (Telah Diterima)</option>
                            <option value="cancelled" {{ $purchaseOrder->status === 'cancelled' ? 'selected' : '' }}>Cancelled (Batal)</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition text-xs">Update Status PO</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
