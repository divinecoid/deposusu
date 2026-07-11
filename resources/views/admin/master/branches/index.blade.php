@extends('layouts.admin')

@section('header', 'Master Data: Cabang & Area')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="branchArea()">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Title --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-violet-600 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
                Cabang, Area & Delivery Mapping
            </h1>
            <p class="text-slate-500 text-sm mt-1">Kelola cabang, area pengiriman, dan mapping kurir per jadwal</p>
        </div>
    </div>

    {{-- ══════════ TABS ══════════ --}}
    <div class="border-b border-slate-200">
        <nav class="flex gap-1 -mb-px">
            <button @click="activeTab = 'cabang'"
                :class="activeTab === 'cabang' ? 'border-violet-600 text-violet-700 bg-white' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="px-5 py-3 text-sm font-semibold border-b-2 rounded-t-lg transition">
                🏢 Cabang
            </button>
            <button @click="activeTab = 'area'"
                :class="activeTab === 'area' ? 'border-violet-600 text-violet-700 bg-white' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="px-5 py-3 text-sm font-semibold border-b-2 rounded-t-lg transition">
                📍 Area & Jadwal Pengiriman
            </button>
        </nav>
    </div>

    {{-- ══════════ TAB 1: CABANG ══════════ --}}
    <div x-show="activeTab === 'cabang'" x-transition>
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-800">Daftar Cabang</h2>
            <button @click="showAddBranch = true"
                class="bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Cabang
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse($branches as $branch)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="px-5 py-4 border-b border-slate-100 flex items-start justify-between {{ $branch->is_active ? 'bg-violet-50/30' : 'bg-slate-50' }}">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-800 text-sm truncate">{{ $branch->name }}</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ $branch->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                {{ $branch->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-400 mt-0.5 font-mono">{{ $branch->code }}</div>
                    </div>
                    <div class="flex items-center gap-1 ml-2">
                        <form action="{{ route('admin.master.branches.toggle-status', $branch->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-8 h-8 rounded-lg {{ $branch->is_active ? 'bg-amber-100 text-amber-600 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-600 hover:bg-emerald-200' }} flex items-center justify-center transition" title="{{ $branch->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $branch->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/></svg>
                            </button>
                        </form>
                        <button @click="editBranch({{ $branch->id }}, '{{ addslashes($branch->name) }}', '{{ $branch->code }}', '{{ addslashes($branch->address ?? '') }}', '{{ $branch->phone ?? '' }}', '{{ addslashes($branch->pic_name ?? '') }}')"
                            class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 flex items-center justify-center transition" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <form action="{{ route('admin.master.branches.destroy', $branch->id) }}" method="POST" onsubmit="return confirm('Hapus cabang {{ addslashes($branch->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 flex items-center justify-center transition" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="px-5 py-3 space-y-1.5 text-xs text-slate-500">
                    @if($branch->address)
                    <div class="flex items-start gap-2">
                        <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <span>{{ $branch->address }}</span>
                    </div>
                    @endif
                    @if($branch->phone)
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>{{ $branch->phone }}</span>
                    </div>
                    @endif
                    @if($branch->pic_name)
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>PIC: {{ $branch->pic_name }}</span>
                    </div>
                    @endif
                </div>
                <div class="px-5 pb-4">
                    <div class="flex items-center gap-3 text-xs">
                        <span class="bg-violet-50 text-violet-700 px-2.5 py-1 rounded-lg font-semibold">{{ $branch->areas->count() }} Area</span>
                        <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg font-semibold">{{ $branch->areas->flatMap->deliverySchedules->count() }} Jadwal</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                </div>
                <h3 class="font-bold text-slate-700 text-lg mb-2">Belum ada cabang</h3>
                <p class="text-slate-400 text-sm mb-5">Mulai tambahkan cabang Deposusu</p>
                <button @click="showAddBranch = true" class="bg-violet-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl hover:bg-violet-700 transition">+ Tambah Cabang Pertama</button>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ══════════ TAB 2: AREA & JADWAL PENGIRIMAN ══════════ --}}
    <div x-show="activeTab === 'area'" x-transition>
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-bold text-slate-800">Area & Jadwal Pengiriman</h2>
                {{-- Filter by Cabang --}}
                <select x-model="filterBranch" class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }} ({{ $branch->code }})</option>
                    @endforeach
                </select>
            </div>
            <button @click="showAddArea = true"
                class="bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Area
            </button>
        </div>

        {{-- Area Table --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left">
                            <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Kode Cabang</th>
                            <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Kode Area</th>
                            <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Nama Area</th>
                            <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Jadwal & Kurir</th>
                            <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $hasArea = false; @endphp
                        @foreach($branches as $branch)
                            @foreach($branch->areas as $area)
                            @php $hasArea = true; @endphp
                            <tr class="hover:bg-slate-50/50 transition"
                                x-show="!filterBranch || filterBranch == '{{ $branch->id }}'">
                                <td class="px-5 py-3">
                                    <span class="text-xs font-mono font-semibold text-violet-600 bg-violet-50 px-2 py-0.5 rounded">{{ $branch->code }}</span>
                                </td>
                                <td class="px-5 py-3 font-mono text-xs text-slate-600 font-semibold">{{ $area->code }}</td>
                                <td class="px-5 py-3 font-medium text-slate-800">{{ $area->name }}</td>
                                <td class="px-5 py-3">
                                    @if($area->deliverySchedules->count() > 0)
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($area->deliverySchedules->sortBy('day_of_week') as $schedule)
                                        <span class="inline-flex items-center gap-1 text-[11px] px-2 py-0.5 rounded-full {{ $schedule->is_active ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-400' }}">
                                            <span class="font-semibold">{{ $schedule->day_name }}</span>
                                            @if($schedule->driver)
                                            <span class="text-slate-400">→</span>
                                            <span>{{ $schedule->driver->name }}</span>
                                            @else
                                            <span class="text-slate-400">→ ?</span>
                                            @endif
                                        </span>
                                        @endforeach
                                    </div>
                                    @else
                                    <span class="text-xs text-slate-400 italic">Belum ada jadwal</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full {{ $area->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $area->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $area->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        {{-- Manage Schedule --}}
                                        <button @click="manageSchedule({{ $area->id }}, '{{ addslashes($area->name) }}', '{{ $area->code }}', {{ json_encode($area->deliverySchedules->map(fn($s) => ['id' => $s->id, 'day_of_week' => $s->day_of_week, 'day_name' => $s->day_name, 'driver_id' => $s->driver_id, 'driver_name' => $s->driver?->name ?? '-'])) }})"
                                            class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 hover:bg-indigo-200 flex items-center justify-center transition" title="Kelola Jadwal">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </button>
                                        {{-- Edit Area --}}
                                        <button @click="editArea({{ $area->id }}, '{{ addslashes($area->name) }}', '{{ $area->code }}', '{{ addslashes($area->description ?? '') }}', {{ $area->branch_id }}, '{{ $area->latitude ?? '' }}', '{{ $area->longitude ?? '' }}')"
                                            class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 flex items-center justify-center transition" title="Edit">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        {{-- Toggle --}}
                                        <form action="{{ route('admin.master.areas.toggle-status', $area->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-7 h-7 rounded-lg {{ $area->is_active ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600' }} flex items-center justify-center transition" title="{{ $area->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $area->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/></svg>
                                            </button>
                                        </form>
                                        {{-- Delete --}}
                                        <form action="{{ route('admin.master.areas.destroy', $area->id) }}" method="POST" onsubmit="return confirm('Hapus area {{ addslashes($area->name) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-7 h-7 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 flex items-center justify-center transition" title="Hapus">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                        @if(!$hasArea)
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400 text-sm">Belum ada area. Tambahkan area pertama.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         MODALS
         ═══════════════════════════════════════════════════════════ --}}

    {{-- MODAL: Tambah Cabang --}}
    <div x-show="showAddBranch" x-transition.opacity class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display:none">
        <div @click.outside="showAddBranch = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-lg">Tambah Cabang Baru</h3>
                <button @click="showAddBranch = false" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition">✕</button>
            </div>
            <form action="{{ route('admin.master.branches.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Cabang *</label>
                        <input type="text" name="name" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500" placeholder="Cabang Denpasar" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kode Cabang *</label>
                        <input type="text" name="code" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 font-mono uppercase" placeholder="DPS-01" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Alamat Cabang</label>
                    <textarea name="address" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 resize-none" placeholder="Jl. Raya ..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nomor Telepon</label>
                        <input type="text" name="phone" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500" placeholder="0361-123456">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">PIC</label>
                        <input type="text" name="pic_name" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500" placeholder="Nama PIC">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showAddBranch = false" class="flex-1 border border-slate-200 text-slate-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="flex-1 bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold py-2.5 rounded-xl transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Edit Cabang --}}
    <div x-show="showEditBranch" x-transition.opacity class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display:none">
        <div @click.outside="showEditBranch = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-lg">Edit Cabang</h3>
                <button @click="showEditBranch = false" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition">✕</button>
            </div>
            <form :action="'/admin/master/branches/' + editBranchId" method="POST" class="px-6 py-5 space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Cabang *</label>
                        <input type="text" name="name" x-model="editBranchName" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kode Cabang *</label>
                        <input type="text" name="code" x-model="editBranchCode" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 font-mono uppercase" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Alamat</label>
                    <textarea name="address" rows="2" x-model="editBranchAddress" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Telepon</label>
                        <input type="text" name="phone" x-model="editBranchPhone" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">PIC</label>
                        <input type="text" name="pic_name" x-model="editBranchPic" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showEditBranch = false" class="flex-1 border border-slate-200 text-slate-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 rounded-xl transition">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Tambah Area --}}
    <div x-show="showAddArea" x-transition.opacity class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display:none">
        <div @click.outside="showAddArea = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-lg">Tambah Area Baru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Area akan muncul di tab Area & Jadwal Pengiriman</p>
            </div>
            <form action="{{ route('admin.master.areas.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Cabang *</label>
                    <select name="branch_id" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500" required>
                        <option value="">-- Pilih Cabang --</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }} ({{ $branch->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kode Area *</label>
                        <input type="text" name="code" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 font-mono uppercase" placeholder="22" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Area *</label>
                        <input type="text" name="name" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500" placeholder="Puri Gardena" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Deskripsi</label>
                    <input type="text" name="description" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500" placeholder="Opsional">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Latitude</label>
                        <input type="number" step="any" name="latitude" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 font-mono" placeholder="-8.123">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Longitude</label>
                        <input type="number" step="any" name="longitude" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 font-mono" placeholder="115.123">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showAddArea = false" class="flex-1 border border-slate-200 text-slate-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="flex-1 bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold py-2.5 rounded-xl transition">Simpan Area</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Edit Area --}}
    <div x-show="showEditArea" x-transition.opacity class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display:none">
        <div @click.outside="showEditArea = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-lg">Edit Area</h3>
            </div>
            <form :action="'/admin/master/areas/' + editAreaId" method="POST" class="px-6 py-5 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Cabang *</label>
                    <select name="branch_id" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500" required x-model="editAreaBranchId">
                        <option value="">-- Pilih Cabang --</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kode Area *</label>
                        <input type="text" name="code" x-model="editAreaCode" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 font-mono uppercase" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Area *</label>
                        <input type="text" name="name" x-model="editAreaName" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Deskripsi</label>
                    <input type="text" name="description" x-model="editAreaDescription" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Latitude</label>
                        <input type="number" step="any" name="latitude" x-model="editAreaLat" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Longitude</label>
                        <input type="number" step="any" name="longitude" x-model="editAreaLng" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 font-mono">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showEditArea = false" class="flex-1 border border-slate-200 text-slate-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 rounded-xl transition">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Kelola Jadwal Pengiriman --}}
    <div x-show="showSchedule" x-transition.opacity class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display:none">
        <div @click.outside="showSchedule = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-lg">Jadwal Pengiriman</h3>
                <p class="text-sm text-slate-500 mt-0.5">Area: <span class="font-semibold text-violet-600" x-text="scheduleAreaName"></span> (<span class="font-mono text-xs" x-text="scheduleAreaCode"></span>)</p>
            </div>
            <div class="px-6 py-5 space-y-5">
                {{-- Existing Schedules --}}
                <div>
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Jadwal Aktif</h4>
                    <template x-if="scheduleList.length === 0">
                        <p class="text-sm text-slate-400 italic text-center py-3">Belum ada jadwal pengiriman</p>
                    </template>
                    <div class="space-y-2">
                        <template x-for="(sched, idx) in scheduleList" :key="sched.id">
                            <div class="flex items-center justify-between bg-slate-50 rounded-xl px-4 py-2.5">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold" x-text="sched.day_name.substring(0,3)"></span>
                                    <div>
                                        <p class="text-sm font-medium text-slate-800" x-text="sched.day_name"></p>
                                        <p class="text-xs text-slate-400">Kurir: <span class="font-medium text-slate-600" x-text="sched.driver_name"></span></p>
                                    </div>
                                </div>
                                <form :action="'/admin/master/areas/' + scheduleAreaId + '/schedules/' + sched.id" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 flex items-center justify-center transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Add Schedule --}}
                <div class="border-t border-slate-100 pt-4">
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Tambah Jadwal</h4>
                    <form :action="'/admin/master/areas/' + scheduleAreaId + '/schedules'" method="POST" class="flex items-end gap-3">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Hari</label>
                            <select name="day_of_week" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500" required>
                                <option value="">Pilih Hari</option>
                                <option value="1">Senin</option>
                                <option value="2">Selasa</option>
                                <option value="3">Rabu</option>
                                <option value="4">Kamis</option>
                                <option value="5">Jumat</option>
                                <option value="6">Sabtu</option>
                                <option value="7">Minggu</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kurir</label>
                            <select name="driver_id" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                                <option value="">-- Tanpa Kurir --</option>
                                @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition flex-shrink-0">+ Tambah</button>
                    </form>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end">
                <button @click="showSchedule = false" class="border border-slate-200 text-slate-600 text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-slate-50 transition">Tutup</button>
            </div>
        </div>
    </div>

</div>

<script>
function branchArea() {
    return {
        activeTab: 'area',
        filterBranch: '',

        // Cabang
        showAddBranch: false,
        showEditBranch: false,
        editBranchId: null,
        editBranchName: '',
        editBranchCode: '',
        editBranchAddress: '',
        editBranchPhone: '',
        editBranchPic: '',

        // Area
        showAddArea: false,
        showEditArea: false,
        editAreaId: null,
        editAreaName: '',
        editAreaCode: '',
        editAreaDescription: '',
        editAreaBranchId: '',
        editAreaLat: '',
        editAreaLng: '',

        // Schedule
        showSchedule: false,
        scheduleAreaId: null,
        scheduleAreaName: '',
        scheduleAreaCode: '',
        scheduleList: [],

        editBranch(id, name, code, address, phone, pic) {
            this.editBranchId = id;
            this.editBranchName = name;
            this.editBranchCode = code;
            this.editBranchAddress = address;
            this.editBranchPhone = phone;
            this.editBranchPic = pic;
            this.showEditBranch = true;
        },

        editArea(id, name, code, description, branchId, lat, lng) {
            this.editAreaId = id;
            this.editAreaName = name;
            this.editAreaCode = code;
            this.editAreaDescription = description;
            this.editAreaBranchId = String(branchId);
            this.editAreaLat = lat;
            this.editAreaLng = lng;
            this.showEditArea = true;
        },

        manageSchedule(areaId, areaName, areaCode, schedules) {
            this.scheduleAreaId = areaId;
            this.scheduleAreaName = areaName;
            this.scheduleAreaCode = areaCode;
            this.scheduleList = schedules;
            this.showSchedule = true;
        }
    }
}
</script>
@endsection