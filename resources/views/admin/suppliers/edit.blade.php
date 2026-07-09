@extends('layouts.admin')

@section('header', 'Edit Supplier')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('admin.suppliers.show', $supplier->id) }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali
    </a>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h2 class="font-bold text-slate-800 mb-6 text-lg">Edit Supplier — {{ $supplier->name }}</h2>
        <form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Nama Supplier <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="{{ $supplier->name }}" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Kode Supplier</label>
                    <input type="text" name="code" value="{{ $supplier->code }}" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ $supplier->contact_person }}" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Telepon</label>
                    <input type="text" name="phone" value="{{ $supplier->phone }}" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ $supplier->email }}" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Nama Bank</label>
                    <input type="text" name="bank_name" value="{{ $supplier->bank_name }}" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">No. Rekening</label>
                    <input type="text" name="bank_account" value="{{ $supplier->bank_account }}" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Nama Pemilik Rekening</label>
                    <input type="text" name="bank_account_name" value="{{ $supplier->bank_account_name }}" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Kota</label>
                    <input type="text" name="city" value="{{ $supplier->city }}" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Limit Kredit Supplier (Rp)</label>
                    <input type="number" name="credit_limit" value="{{ $supplier->credit_limit }}" min="0" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Alamat</label>
                <textarea name="address" rows="2" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">{{ $supplier->address }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Catatan</label>
                <textarea name="notes" rows="2" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50">{{ $supplier->notes }}</textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ $supplier->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                <label for="is_active" class="text-sm font-semibold text-slate-700">Supplier Aktif</label>
            </div>

            <button type="submit" class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition shadow-sm">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection
