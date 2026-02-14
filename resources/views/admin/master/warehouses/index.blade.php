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

            <!-- Search Filter -->
            <div class="mb-4">
                <input type="text" id="searchRack" placeholder="Cari rak atau gudang..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <h4 class="font-medium text-gray-700 mb-3">Daftar Rak per Gudang</h4>
            <div class="space-y-3 max-h-96 overflow-y-auto" id="rackList">
                @foreach($warehouses as $warehouse)
                    <div class="warehouse-section border border-gray-200 rounded-lg overflow-hidden bg-white"
                        data-warehouse-name="{{ strtolower($warehouse->name) }}">
                        <!-- Warehouse Header -->
                        <div class="bg-gradient-to-r from-green-50 to-green-100 p-3 cursor-pointer hover:from-green-100 hover:to-green-200 transition-colors"
                            onclick="toggleWarehouseRacks('warehouse-{{ $warehouse->id }}')">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600 transform transition-transform warehouse-toggle-icon"
                                        id="icon-warehouse-{{ $warehouse->id }}" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                    <p class="font-bold text-sm text-gray-800">{{ $warehouse->name }}</p>
                                </div>
                                <span class="bg-green-600 text-white text-xs px-2 py-1 rounded-full">
                                    {{ $warehouse->racks->count() }} Rak
                                </span>
                            </div>
                            @if($warehouse->address)
                                <p class="text-xs text-gray-600 ml-7 mt-1">{{ $warehouse->address }}</p>
                            @endif
                        </div>

                        <!-- Racks List -->
                        <div id="warehouse-{{ $warehouse->id }}" class="warehouse-racks p-4 bg-white">
                            @if($warehouse->racks->count() > 0)
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($warehouse->racks as $rack)
                                        <div class="rack-item border border-gray-200 rounded-lg p-3 hover:shadow-md transition-shadow bg-gradient-to-br from-white to-gray-50"
                                            data-rack-name="{{ strtolower($rack->name) }}">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                                                        </path>
                                                    </svg>
                                                    <span class="font-semibold text-sm text-gray-900">{{ $rack->name }}</span>
                                                </div>
                                                <form action="{{ route('admin.master.racks.destroy', $rack->id) }}" method="POST"
                                                    class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmDeleteRack(this)"
                                                        class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <p class="text-sm text-gray-500">Belum ada rak di gudang ini.</p>
                                    <p class="text-xs text-gray-400 mt-1">Tambahkan rak baru menggunakan form di atas.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        // Toggle warehouse racks visibility
        function toggleWarehouseRacks(warehouseId) {
            const racksDiv = document.getElementById(warehouseId);
            const icon = document.getElementById('icon-' + warehouseId);

            if (racksDiv.classList.contains('hidden')) {
                racksDiv.classList.remove('hidden');
                icon.classList.remove('rotate-180');
            } else {
                racksDiv.classList.add('hidden');
                icon.classList.add('rotate-180');
            }
        }

        // Confirm delete with better UX
        function confirmDeleteRack(button) {
            if (confirm('Apakah Anda yakin ingin menghapus rak ini?')) {
                button.closest('form').submit();
            }
        }

        // Search functionality
        document.getElementById('searchRack').addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            const warehouses = document.querySelectorAll('.warehouse-section');

            warehouses.forEach(warehouse => {
                const warehouseName = warehouse.dataset.warehouseName;
                const racks = warehouse.querySelectorAll('.rack-item');
                let warehouseHasMatch = false;

                // Check if warehouse name matches
                if (warehouseName.includes(searchTerm)) {
                    warehouseHasMatch = true;
                    racks.forEach(rack => rack.style.display = '');
                } else {
                    // Check each rack
                    racks.forEach(rack => {
                        const rackName = rack.dataset.rackName;
                        if (rackName.includes(searchTerm)) {
                            rack.style.display = '';
                            warehouseHasMatch = true;
                        } else {
                            rack.style.display = 'none';
                        }
                    });
                }

                // Show/hide entire warehouse section
                warehouse.style.display = warehouseHasMatch ? '' : 'none';
            });
        });

        // Auto-expand all warehouses on page load for better UX
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.warehouse-racks').forEach(rack => {
                // All warehouses start expanded
            });
        });
    </script>
@endsection