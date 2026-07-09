@extends('layouts.admin')

@section('header', 'Detail Supplier')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.suppliers.index') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali ke Daftar
        </a>
        <div class="flex gap-2">
            <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2 rounded-xl border border-slate-200 transition">
                ✏️ Edit Supplier
            </a>
            <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold px-4 py-2 rounded-xl border border-red-100 transition">
                    🗑️ Hapus
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Details card --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <span class="text-xs font-mono font-bold text-orange-600 uppercase tracking-wide">{{ $supplier->code }}</span>
                        <h2 class="text-xl font-bold text-slate-800">{{ $supplier->name }}</h2>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $supplier->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                        {{ $supplier->is_active ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <h4 class="font-bold text-slate-400 uppercase tracking-wider text-xs mb-2">Kontak Info:</h4>
                        <div class="space-y-1.5 text-slate-700">
                            <div><strong>CP:</strong> {{ $supplier->contact_person ?: '-' }}</div>
                            <div><strong>Telepon:</strong> {{ $supplier->phone ?: '-' }}</div>
                            <div><strong>Email:</strong> {{ $supplier->email ?: '-' }}</div>
                            <div><strong>Kota:</strong> {{ $supplier->city ?: '-' }}</div>
                            <div><strong>Alamat:</strong> {{ $supplier->address ?: '-' }}</div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-400 uppercase tracking-wider text-xs mb-2">Informasi Keuangan:</h4>
                        <div class="space-y-1.5 text-slate-700">
                            <div><strong>Bank:</strong> {{ $supplier->bank_name ?: '-' }}</div>
                            <div><strong>No. Rekening:</strong> {{ $supplier->bank_account ?: '-' }}</div>
                            <div><strong>A/N Rekening:</strong> {{ $supplier->bank_account_name ?: '-' }}</div>
                            <div><strong>Limit Kredit:</strong> Rp {{ number_format($supplier->credit_limit, 0, ',', '.') }}</div>
                            <div class="text-red-600 font-bold"><strong>Total Hutang:</strong> Rp {{ number_format($supplier->total_hutang, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                @if($supplier->notes)
                <div class="mt-6 p-4 bg-slate-50 rounded-xl text-sm text-slate-600 border-l-4 border-orange-500">
                    <strong>Catatan Internal:</strong><br>
                    {{ $supplier->notes }}
                </div>
                @endif
            </div>

            {{-- PO History --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">Purchase Orders (PO) Terakhir</h3>
                    <a href="{{ route('admin.suppliers.po.index', ['supplier_id' => $supplier->id]) }}" class="text-xs text-blue-600 font-semibold hover:underline">Lihat Semua →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">PO Number</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Order Date</th>
                                <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Total Amount</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($supplier->purchaseOrders as $po)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3 text-sm">
                                    <a href="{{ route('admin.suppliers.po.show', $po->id) }}" class="font-mono font-bold text-blue-600 hover:underline">{{ $po->po_number }}</a>
                                </td>
                                <td class="px-5 py-3 text-sm text-slate-600">{{ $po->order_date->format('d M Y') }}</td>
                                <td class="px-5 py-3 text-sm text-right font-bold">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-{{ $po->status_color }}-100 text-{{ $po->status_color }}-700 capitalize">
                                        {{ $po->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-5 py-6 text-center text-slate-400 text-sm">Belum ada history PO</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar Actions --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 text-center">
                <p class="text-sm text-slate-500 mb-4">Ingin membeli stock produk dari supplier ini?</p>
                <a href="{{ route('admin.suppliers.po.create', ['supplier_id' => $supplier->id]) }}" class="block w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition text-sm shadow-xs">
                    📝 Buat Purchase Order (PO)
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
