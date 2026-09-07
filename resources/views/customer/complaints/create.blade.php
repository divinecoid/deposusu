@extends('layouts.customer')

@section('title', 'Ajukan Komplain - DEPOSUSU')

@section('content')
<div class="min-h-screen bg-slate-50 py-6 md:py-10">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Ajukan Komplain</h1>
            <p class="text-sm text-slate-500 mt-1">Pesanan #{{ $order->order_number }}</p>
        </div>

        <form action="{{ route('complaints.store', $order->id) }}" method="POST" class="bg-white rounded-3xl border border-slate-200/80 p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori</label>
                <select name="category" required
                    class="w-full h-11 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 focus:outline-none">
                    <option value="">Pilih kategori</option>
                    @foreach(\App\Models\TrxComplaint::CATEGORIES as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('category') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="5" required minlength="10" placeholder="Ceritakan kendala yang Anda alami dengan pesanan ini..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 focus:outline-none">{{ old('description') }}</textarea>
                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full h-12 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 transition-colors">
                Kirim Komplain
            </button>
        </form>
    </div>
</div>
@endsection
