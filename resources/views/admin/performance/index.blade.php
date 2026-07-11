@extends('layouts.admin')

@section('header', 'Employee Performance')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ showManualPoint: false }">

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Title --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </span>
                Employee Performance Point
            </h1>
            <p class="text-slate-500 text-sm mt-1">Internal tracking performa Preparist & Kurir — hanya Owner/Admin</p>
        </div>
        <button @click="showManualPoint = true"
            class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Poin Manual
        </button>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Periode</label>
            <select name="period" class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Semua</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Role</label>
            <select name="role" class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">Semua Role</option>
                <option value="preparist" {{ $role === 'preparist' ? 'selected' : '' }}>Preparist</option>
                <option value="driver" {{ $role === 'driver' ? 'selected' : '' }}>Kurir</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Cari Nama</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari karyawan..." class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
        <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">Filter</button>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-extrabold text-amber-600">{{ number_format($totalPoints) }}</p>
            <p class="text-xs text-slate-500 mt-1 font-semibold">Total Poin</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-extrabold text-slate-800">{{ number_format($totalActivities) }}</p>
            <p class="text-xs text-slate-500 mt-1 font-semibold">Total Aktivitas</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-extrabold text-indigo-600">{{ number_format($packingPoints) }}</p>
            <p class="text-xs text-slate-500 mt-1 font-semibold">Poin Packing</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-extrabold text-blue-600">{{ number_format($deliveryPoints) }}</p>
            <p class="text-xs text-slate-500 mt-1 font-semibold">Poin Delivery</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-extrabold text-emerald-600">{{ $activeEmployees }}</p>
            <p class="text-xs text-slate-500 mt-1 font-semibold">Karyawan Aktif</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Leaderboard --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    🏆 Ranking Performa Karyawan
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left">
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase w-12">#</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase text-center">Aktivitas</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase text-center">Total Poin</th>
                            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($leaderboard as $i => $entry)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-3">
                                @if($i === 0)
                                <span class="w-7 h-7 rounded-full bg-amber-400 text-white flex items-center justify-center text-xs font-bold">🥇</span>
                                @elseif($i === 1)
                                <span class="w-7 h-7 rounded-full bg-slate-300 text-white flex items-center justify-center text-xs font-bold">🥈</span>
                                @elseif($i === 2)
                                <span class="w-7 h-7 rounded-full bg-amber-700 text-white flex items-center justify-center text-xs font-bold">🥉</span>
                                @else
                                <span class="text-slate-400 font-mono text-xs">{{ $i + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-xl bg-gradient-to-br {{ $entry->user->role === 'driver' ? 'from-blue-500 to-blue-600' : 'from-indigo-500 to-indigo-600' }} text-white flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($entry->user->name, 0, 1)) }}
                                    </span>
                                    <span class="font-semibold text-slate-800">{{ $entry->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $entry->user->role === 'driver' ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700' }}">
                                    {{ $entry->user->role === 'driver' ? 'Kurir' : 'Preparist' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center font-mono text-slate-600">{{ $entry->total_activities }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="text-lg font-extrabold text-amber-600">{{ number_format($entry->total_points) }}</span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <a href="{{ route('admin.performance.show', $entry->user->id) }}?period={{ $period }}"
                                    class="text-violet-600 hover:text-violet-800 font-semibold text-xs transition">
                                    Lihat →
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                Belum ada data poin karyawan untuk periode ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">⚡ Aktivitas Terbaru</h3>
            </div>
            <div class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">
                @forelse($recentActivities as $activity)
                <div class="px-5 py-3 hover:bg-slate-50/50 transition">
                    <div class="flex items-start gap-3">
                        <span class="w-7 h-7 rounded-lg flex-shrink-0 flex items-center justify-center text-xs {{ $activity->activity_type === 'packing_completed' ? 'bg-indigo-100 text-indigo-600' : 'bg-blue-100 text-blue-600' }}">
                            @if($activity->activity_type === 'packing_completed')
                            📦
                            @else
                            🚚
                            @endif
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800">{{ $activity->user->name ?? '-' }}</p>
                            <p class="text-xs text-slate-500">{{ $activity->description }}</p>
                            @if($activity->order)
                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">Order #{{ $activity->order->order_number ?? $activity->order_id }}</p>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-sm font-bold text-amber-600">+{{ $activity->points }}</span>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $activity->activity_date->format('d/m H:i') }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-12 text-center text-slate-400 text-sm">Belum ada aktivitas</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL: Tambah Poin Manual --}}
    <div x-show="showManualPoint" x-transition.opacity class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display:none">
        <div @click.outside="showManualPoint = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Tambah Poin Manual</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Bonus/penalti dari Owner/Admin</p>
                </div>
                <button @click="showManualPoint = false" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition">✕</button>
            </div>
            <form action="{{ route('admin.performance.store-manual') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Karyawan *</label>
                    <select name="user_id" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @php
                            $employees = \App\Models\User::whereIn('role', ['driver', 'preparist'])->orderBy('name')->get();
                        @endphp
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->role === 'driver' ? 'Kurir' : 'Preparist' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tipe *</label>
                        <select name="activity_type" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                            <option value="bonus">Bonus</option>
                            <option value="packing_completed">Packing</option>
                            <option value="delivery_completed">Delivery</option>
                            <option value="penalty">Penalti</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Poin *</label>
                        <input type="number" name="points" value="1" min="-100" max="100" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Keterangan *</label>
                    <input type="text" name="description" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Ex: Bonus performa bulan Juli" required>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showManualPoint = false" class="flex-1 border border-slate-200 text-slate-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold py-2.5 rounded-xl transition">Simpan Poin</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
