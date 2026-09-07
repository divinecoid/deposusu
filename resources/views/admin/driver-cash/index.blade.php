@extends('layouts.admin')

@section('header', 'Setoran Kurir')

@section('content')
<div class="max-w-5xl mx-auto">

    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs text-gray-500 uppercase font-semibold">Belum Disetor</p>
            <p class="text-2xl font-bold text-red-600">Rp {{ number_format($pendingTotal, 0, ',', '.') }}</p>
        </div>

        <form method="GET" class="flex items-center gap-2">
            <select name="driver_id" onchange="this.form.submit()"
                class="border border-gray-300 rounded-md shadow-sm py-1.5 px-3 text-sm focus:border-blue-500 focus:outline-none">
                <option value="">Semua Kurir</option>
                @foreach($drivers as $driver)
                    <option value="{{ $driver->id }}" {{ (string) $selectedDriverId === (string) $driver->id ? 'selected' : '' }}>
                        {{ $driver->name }}
                    </option>
                @endforeach
            </select>
        </form>

        @if($selectedDriverId && $pendingTotal > 0)
            <form action="{{ route('admin.driver-cash.confirm-all', $selectedDriverId) }}" method="POST"
                onsubmit="return confirm('Konfirmasi semua setoran kurir ini sudah diterima?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700 transition shadow-sm">
                    Konfirmasi Semua Setoran Kurir Ini
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kurir</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Ambil</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($collections as $collection)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $collection->driver->name ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $collection->order->order_number ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold">Rp {{ number_format($collection->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-[11px] text-gray-500">{{ $collection->collected_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($collection->is_deposited)
                                <span class="px-2 py-1 text-[10px] font-semibold rounded-full bg-green-100 text-green-800">
                                    Sudah Disetor
                                </span>
                            @else
                                <span class="px-2 py-1 text-[10px] font-semibold rounded-full bg-amber-100 text-amber-800">
                                    Belum Disetor
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-medium">
                            @unless($collection->is_deposited)
                                <form action="{{ route('admin.driver-cash.confirm', $collection->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900 font-semibold">
                                        Konfirmasi Terima
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-[11px]">{{ $collection->deposited_at?->format('d M Y H:i') }}</span>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 italic">Belum ada setoran tunai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $collections->links() }}
    </div>
</div>
@endsection
