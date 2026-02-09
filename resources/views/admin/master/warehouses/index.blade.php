@extends('layouts.admin')

@section('header', 'Master Data: Gudang & Rak')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Warehouse List -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Gudang</h3>

            <form action="{{ route('admin.master.warehouses.store') }}" method="POST" class="mb-4 flex gap-2">
                @csrf
                <input type="text" name="name" placeholder="Nama Gudang"
                    class="flex-1 border border-gray-300 rounded px-3 py-2" required>
                <input type="text" name="address" placeholder="Alamat (Optional)"
                    class="flex-1 border border-gray-300 rounded px-3 py-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah</button>
            </form>

            <ul class="divide-y divide-gray-200">
                @foreach($warehouses as $warehouse)
                    <li class="py-3 flex justify-between items-center group">
                        <div>
                            <p class="font-medium text-gray-900">{{ $warehouse->name }}</p>
                            <p class="text-sm text-gray-500">{{ $warehouse->address }}</p>
                            <p class="text-xs text-blue-500">{{ $warehouse->racks_count }} Rak</p>
                        </div>
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <form action="{{ route('admin.master.warehouses.destroy', $warehouse->id) }}" method="POST"
                                onsubmit="return confirm('Hapus gudang ini?');">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Rack Management (Simplified: Just add to existing warehouses) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Kelola Rak</h3>

            <form action="{{ route('admin.master.racks.store') }}" method="POST" class="mb-6">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Pilih Gudang</label>
                    <select name="warehouse_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Nama Rak</label>
                    <input type="text" name="name" placeholder="Ex: Rak A1"
                        class="w-full border border-gray-300 rounded px-3 py-2" required>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded">Tambah Rak</button>
            </form>

            <h4 class="font-medium text-gray-700 mb-2">Daftar Rak per Gudang</h4>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @foreach($warehouses as $warehouse)
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="font-bold text-sm text-gray-800 mb-1">{{ $warehouse->name }}</p>
                        @if($warehouse->racks->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($warehouse->racks as $rack)
                                    <div class="bg-white border border-gray-200 px-2 py-1 rounded text-xs flex items-center gap-2">
                                        {{ $rack->name }}
                                        <form action="{{ route('admin.master.racks.destroy', $rack->id) }}" method="POST"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button class="text-red-500 hover:text-red-700 font-bold">&times;</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-500">Belum ada rak.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection