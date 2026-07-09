@extends('layouts.admin')

@section('header', 'Supplier Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </span>
                Supplier Management
            </h1>
            <p class="text-slate-500 text-sm mt-1">Daftar rekanan supplier Deposusu</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.suppliers.po.index') }}" class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl border border-slate-200 transition">
                📋 Purchase Orders (PO)
            </a>
            <a href="{{ route('admin.suppliers.create') }}" class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-4 py-2.5 rounded-xl shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Supplier
            </a>
        </div>
    </div>

    {{-- Search Filter --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.suppliers.index') }}" class="flex gap-2">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama, kota, telepon..." class="flex-1 border border-slate-200 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-slate-50">
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">Cari</button>
            @if($q)
            <a href="{{ route('admin.suppliers.index') }}" class="border border-slate-200 hover:bg-slate-50 text-slate-500 text-sm font-semibold px-4 py-2 rounded-xl transition">Reset</a>
            @endif
        </form>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    {{-- Supplier List --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Code</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Nama Supplier</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Contact Person</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Telepon</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Total Hutang</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($suppliers as $sup)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-5 py-4 text-sm font-mono font-bold text-orange-600">{{ $sup->code }}</td>
                        <td class="px-5 py-4 text-sm font-semibold text-slate-800">{{ $sup->name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $sup->contact_person ?: '-' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $sup->phone ?: '-' }}</td>
                        <td class="px-5 py-4 text-sm text-right font-bold text-slate-800">Rp {{ number_format($sup->total_hutang, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $sup->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                {{ $sup->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center text-sm font-medium space-x-2">
                            <a href="{{ route('admin.suppliers.show', $sup->id) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                            <a href="{{ route('admin.suppliers.edit', $sup->id) }}" class="text-slate-600 hover:text-slate-800">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-slate-400">Belum ada data supplier</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($suppliers->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $suppliers->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection
