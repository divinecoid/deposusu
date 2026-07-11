@extends('layouts.admin')

@section('header', 'Detail Cabang: ' . $branch->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Back Button --}}
    <a href="{{ route('admin.master.branches.index') }}" class="inline-flex items-center gap-2 text-sm text-violet-600 hover:text-violet-800 font-semibold transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Cabang
    </a>

    {{-- Branch Detail Card --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between {{ $branch->is_active ? 'bg-gradient-to-r from-violet-50 to-indigo-50' : 'bg-slate-50' }}">
            <div>
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-violet-600 text-white flex items-center justify-center text-lg font-bold">
                        {{ strtoupper(substr($branch->name, 0, 1)) }}
                    </span>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">{{ $branch->name }}</h2>
                        <span class="text-xs text-slate-400 font-mono">{{ $branch->code }}</span>
                    </div>
                </div>
            </div>
            <span class="text-xs font-bold px-3 py-1.5 rounded-full {{ $branch->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                {{ $branch->is_active ? '● AKTIF' : '○ NON-AKTIF' }}
            </span>
        </div>

        <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Informasi Cabang</h4>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 mt-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <div>
                            <p class="text-xs text-slate-400">Alamat</p>
                            <p class="text-sm text-slate-700">{{ $branch->address ?: '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 mt-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <div>
                            <p class="text-xs text-slate-400">Telepon</p>
                            <p class="text-sm text-slate-700">{{ $branch->phone ?: '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 mt-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <div>
                            <p class="text-xs text-slate-400">PIC (Penanggung Jawab)</p>
                            <p class="text-sm text-slate-700">{{ $branch->pic_name ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Statistik</h4>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-violet-50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-violet-700">{{ $branch->areas->count() }}</p>
                        <p class="text-xs text-violet-500 mt-1">Total Area</p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-emerald-700">{{ $branch->areas->where('is_active', true)->count() }}</p>
                        <p class="text-xs text-emerald-500 mt-1">Area Aktif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Areas in this Branch --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Daftar Area di {{ $branch->name }}</h3>
        </div>

        @if($branch->areas->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">#</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Nama Area</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Koordinat</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($branch->areas as $i => $area)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-3 text-slate-400 font-mono text-xs">{{ $i + 1 }}</td>
                        <td class="px-6 py-3 font-medium text-slate-800">{{ $area->name }}</td>
                        <td class="px-6 py-3 text-slate-500 font-mono text-xs">{{ $area->code }}</td>
                        <td class="px-6 py-3 text-slate-500">{{ $area->description ?: '-' }}</td>
                        <td class="px-6 py-3 text-slate-500 font-mono text-xs">
                            @if($area->latitude && $area->longitude)
                                {{ $area->latitude }}, {{ $area->longitude }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full {{ $area->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $area->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $area->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <p class="text-slate-400 text-sm">Belum ada area di cabang ini</p>
        </div>
        @endif
    </div>

</div>
@endsection
