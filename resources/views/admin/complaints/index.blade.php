@extends('layouts.admin')

@section('header', 'Komplain Customer')

@section('content')
<div class="max-w-5xl mx-auto" x-data="{ resolvingId: null }">

    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex gap-2 mb-6">
        @foreach(['open' => 'Menunggu', 'in_progress' => 'Diproses', 'resolved' => 'Selesai', 'all' => 'Semua'] as $key => $label)
            <a href="{{ route('admin.complaints.index', ['status' => $key]) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium {{ $status === $key ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-200' }}">
                {{ $label }}
                @if(isset($counts[$key]))
                    <span class="ml-1 opacity-75">({{ $counts[$key] }})</span>
                @endif
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($complaints as $complaint)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $complaint->customer->name ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $complaint->order->order_number ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $complaint->categoryLabel() }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 max-w-xs">{{ \Illuminate\Support\Str::limit($complaint->description, 80) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-[11px] text-gray-500">{{ $complaint->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $badge = ['open' => 'bg-amber-100 text-amber-800', 'in_progress' => 'bg-blue-100 text-blue-800', 'resolved' => 'bg-green-100 text-green-800'][$complaint->status] ?? 'bg-gray-100 text-gray-800';
                                $label = ['open' => 'Menunggu', 'in_progress' => 'Diproses', 'resolved' => 'Selesai'][$complaint->status] ?? $complaint->status;
                            @endphp
                            <span class="px-2 py-1 text-[10px] font-semibold rounded-full {{ $badge }}">{{ $label }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-medium">
                            <button type="button" @click="resolvingId = resolvingId === {{ $complaint->id }} ? null : {{ $complaint->id }}" class="text-blue-600 hover:text-blue-900">
                                Tanggapi
                            </button>
                        </td>
                    </tr>
                    <tr x-show="resolvingId === {{ $complaint->id }}" x-cloak>
                        <td colspan="7" class="px-4 py-4 bg-gray-50">
                            <form action="{{ route('admin.complaints.update', $complaint->id) }}" method="POST" class="flex flex-wrap items-end gap-3">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 uppercase">Status</label>
                                    <select name="status" class="mt-1 border border-gray-300 rounded-md shadow-sm py-1.5 px-3 text-sm">
                                        <option value="open" {{ $complaint->status === 'open' ? 'selected' : '' }}>Menunggu</option>
                                        <option value="in_progress" {{ $complaint->status === 'in_progress' ? 'selected' : '' }}>Diproses</option>
                                        <option value="resolved" {{ $complaint->status === 'resolved' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </div>
                                <div class="flex-1 min-w-[240px]">
                                    <label class="block text-xs font-medium text-gray-700 uppercase">Tanggapan (dikirim ke customer)</label>
                                    <input type="text" name="resolution_note" value="{{ $complaint->resolution_note }}"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 text-sm">
                                </div>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                                    Simpan
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 italic">Tidak ada komplain.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $complaints->links() }}
    </div>
</div>
@endsection
