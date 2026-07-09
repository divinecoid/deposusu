@extends('layouts.admin')

@section('header', 'Tambah Supplier')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('admin.suppliers.index') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali
    </a>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h2 class="font-bold text-slate-800 mb-6 text-lg">Tambah Supplier Baru</h2>
        <form action="{{ route('admin.suppliers.store') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Nama Supplier <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="CV. Maju Jaya">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Kode Supplier (opsional)</label>
                    <input type="text" name="code" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Auto-generated jika kosong">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Contact Person</label>
                    <input type="text" name="contact_person" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50" placeholder="Budi">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Telepon</label>
                    <input type="text" name="phone" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50" placeholder="08123xxx">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Email</label>
                    <input type="email" name="email" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50" placeholder="supplier@mail.com">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Nama Bank</label>
                    <input type="text" name="bank_name" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50" placeholder="BCA">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">No. Rekening</label>
                    <input type="text" name="bank_account" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50" placeholder="123456789">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Nama Pemilik Rekening</label>
                    <input type="text" name="bank_account_name" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50" placeholder="Budi Santoso">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Kota</label>
                    <input type="text" name="city" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50" placeholder="Jakarta">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Limit Kredit Supplier (Rp)</label>
                    <input type="number" name="credit_limit" value="0" min="0" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50" placeholder="0">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Alamat</label>
                <textarea name="address" rows="2" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50" placeholder="Alamat lengkap supplier..."></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Catatan</label>
                <textarea name="notes" rows="2" class="w-full border border-slate-200 rounded-xl py-2.5 px-3 text-sm bg-slate-50" placeholder="Catatan tambahan..."></textarea>
            </div>

            <button type="submit" class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition shadow-sm">Simpan Supplier</button>
        </form>
    </div>
</div>
@endsection
