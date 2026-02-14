@extends('layouts.admin')

@section('header', 'Master Data: Branch & Area')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Branch List -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Branch / Cabang</h3>

            <form action="{{ route('admin.master.branches.store') }}" method="POST" class="mb-4 flex gap-2">
                @csrf
                <input type="text" name="name" placeholder="Nama Branch"
                    class="flex-1 border border-gray-300 rounded px-3 py-2" required>
                <input type="text" name="code" placeholder="Kode" class="w-24 border border-gray-300 rounded px-3 py-2"
                    required>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah</button>
            </form>

            <ul class="divide-y divide-gray-200">
                @foreach($branches as $branch)
                    <li class="py-3 flex justify-between items-center group">
                        <div>
                            <p class="font-medium text-gray-900">{{ $branch->name }} ({{ $branch->code }})</p>
                            <p class="text-sm text-gray-500">{{ $branch->address }}</p>
                            <p class="text-xs text-blue-500">{{ $branch->areas_count }} Area</p>
                        </div>
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <form action="{{ route('admin.master.branches.destroy', $branch->id) }}" method="POST"
                                onsubmit="return confirm('Hapus branch ini?');">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Area Management (Per Branch) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Kelola Area</h3>

            <form action="{{ route('admin.master.areas.store') }}" method="POST" class="mb-6">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Pilih Branch</label>
                    <select name="branch_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Area</label>
                        <input type="text" name="name" placeholder="Ex: Area 1"
                            class="w-full border border-gray-300 rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kode Area</label>
                        <input type="text" name="code" placeholder="Ex: A1"
                            class="w-full border border-gray-300 rounded px-3 py-2" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Latitude</label>
                        <input type="number" step="any" name="latitude" placeholder="Ex: -6.123456"
                            class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Longitude</label>
                        <input type="number" step="any" name="longitude" placeholder="Ex: 106.123456"
                            class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" class="w-full border border-gray-300 rounded px-3 py-2"
                        rows="2"></textarea>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded">Tambah Area</button>
            </form>

            <!-- Search Filter -->
            <div class="mb-4">
                <input type="text" id="searchArea" placeholder="Cari area atau branch..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <h4 class="font-medium text-gray-700 mb-3">Daftar Area per Branch</h4>
            <div class="space-y-3 max-h-96 overflow-y-auto" id="areaList">
                @foreach($branches as $branch)
                    <div class="branch-section border border-gray-200 rounded-lg overflow-hidden bg-white"
                        data-branch-name="{{ strtolower($branch->name . ' ' . $branch->code) }}">
                        <!-- Branch Header -->
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-3 cursor-pointer hover:from-blue-100 hover:to-blue-200 transition-colors"
                            onclick="toggleBranchAreas('branch-{{ $branch->id }}')">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600 transform transition-transform branch-toggle-icon"
                                        id="icon-branch-{{ $branch->id }}" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                    <p class="font-bold text-sm text-gray-800">{{ $branch->name }} <span
                                            class="text-gray-600">({{ $branch->code }})</span></p>
                                </div>
                                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                                    {{ $branch->areas->count() }} Area
                                </span>
                            </div>
                        </div>

                        <!-- Areas List -->
                        <div id="branch-{{ $branch->id }}" class="branch-areas p-4 bg-white">
                            @if($branch->areas->count() > 0)
                                <div class="space-y-2">
                                    @foreach($branch->areas as $area)
                                        <div class="area-item border border-gray-200 rounded-lg p-3 hover:shadow-md transition-shadow"
                                            data-area-name="{{ strtolower($area->name . ' ' . $area->code) }}"
                                            data-area-id="{{ $area->id }}">

                                            <!-- View Mode -->
                                            <div class="view-mode-{{ $area->id }}">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <h5 class="font-semibold text-gray-900">{{ $area->name }}</h5>
                                                            <span
                                                                class="bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded">{{ $area->code }}</span>
                                                        </div>

                                                        @if($area->description)
                                                            <p class="text-xs text-gray-600 mb-2">{{ $area->description }}</p>
                                                        @endif

                                                        @if($area->latitude || $area->longitude)
                                                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                                                <div class="flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd"
                                                                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                                                            clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    <span>{{ $area->latitude ?? '-' }}, {{ $area->longitude ?? '-' }}</span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="flex items-center gap-2">
                                                        <button onclick="editArea({{ $area->id }})"
                                                            class="text-blue-600 hover:text-blue-800 text-sm px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                        <form action="{{ route('admin.master.areas.destroy', $area->id) }}"
                                                            method="POST" class="inline">
                                                            @csrf @method('DELETE')
                                                            <button type="button" onclick="confirmDelete(this)"
                                                                class="text-red-600 hover:text-red-800 text-sm px-2 py-1 rounded hover:bg-red-50 transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                    </path>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Edit Mode (Hidden by default) -->
                                            <div class="edit-mode-{{ $area->id }} hidden">
                                                <form action="{{ route('admin.master.areas.update', $area->id) }}" method="POST"
                                                    class="space-y-2">
                                                    @csrf @method('PUT')
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Nama
                                                                Area</label>
                                                            <input type="text" name="name" value="{{ $area->name }}"
                                                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm"
                                                                required>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Kode
                                                                Area</label>
                                                            <input type="text" name="code" value="{{ $area->code }}"
                                                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm"
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Latitude</label>
                                                            <input type="number" step="any" name="latitude"
                                                                value="{{ $area->latitude }}"
                                                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="block text-xs font-medium text-gray-700 mb-1">Longitude</label>
                                                            <input type="number" step="any" name="longitude"
                                                                value="{{ $area->longitude }}"
                                                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi</label>
                                                        <textarea name="description" rows="2"
                                                            class="w-full border border-gray-300 rounded px-2 py-1 text-sm">{{ $area->description }}</textarea>
                                                    </div>
                                                    <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                                    <div class="flex gap-2 pt-2">
                                                        <button type="submit"
                                                            class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition-colors">
                                                            Simpan
                                                        </button>
                                                        <button type="button" onclick="cancelEdit({{ $area->id }})"
                                                            class="bg-gray-300 text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-400 transition-colors">
                                                            Batal
                                                        </button>
                                                    </div>
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
                                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                                        </path>
                                    </svg>
                                    <p class="text-sm text-gray-500">Belum ada area di branch ini.</p>
                                    <p class="text-xs text-gray-400 mt-1">Tambahkan area baru menggunakan form di atas.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        // Toggle branch areas visibility
        function toggleBranchAreas(branchId) {
            const areasDiv = document.getElementById(branchId);
            const icon = document.getElementById('icon-' + branchId);

            if (areasDiv.classList.contains('hidden')) {
                areasDiv.classList.remove('hidden');
                icon.classList.remove('rotate-180');
            } else {
                areasDiv.classList.add('hidden');
                icon.classList.add('rotate-180');
            }
        }

        // Edit area
        function editArea(areaId) {
            document.querySelector('.view-mode-' + areaId).classList.add('hidden');
            document.querySelector('.edit-mode-' + areaId).classList.remove('hidden');
        }

        // Cancel edit
        function cancelEdit(areaId) {
            document.querySelector('.view-mode-' + areaId).classList.remove('hidden');
            document.querySelector('.edit-mode-' + areaId).classList.add('hidden');
        }

        // Confirm delete with better UX
        function confirmDelete(button) {
            if (confirm('Apakah Anda yakin ingin menghapus area ini?')) {
                button.closest('form').submit();
            }
        }

        // Search functionality
        document.getElementById('searchArea').addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            const branches = document.querySelectorAll('.branch-section');

            branches.forEach(branch => {
                const branchName = branch.dataset.branchName;
                const areas = branch.querySelectorAll('.area-item');
                let branchHasMatch = false;

                // Check if branch name matches
                if (branchName.includes(searchTerm)) {
                    branchHasMatch = true;
                    areas.forEach(area => area.style.display = '');
                } else {
                    // Check each area
                    areas.forEach(area => {
                        const areaName = area.dataset.areaName;
                        if (areaName.includes(searchTerm)) {
                            area.style.display = '';
                            branchHasMatch = true;
                        } else {
                            area.style.display = 'none';
                        }
                    });
                }

                // Show/hide entire branch section
                branch.style.display = branchHasMatch ? '' : 'none';
            });
        });

        // Auto-expand all branches on page load for better UX
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.branch-areas').forEach(area => {
                // All branches start expanded
            });
        });
    </script>
    </div>
    </div>
@endsection