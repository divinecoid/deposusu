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
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" class="w-full border border-gray-300 rounded px-3 py-2"
                        rows="2"></textarea>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded">Tambah Area</button>
            </form>

            <h4 class="font-medium text-gray-700 mb-2">Daftar Area per Branch</h4>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @foreach($branches as $branch)
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="font-bold text-sm text-gray-800 mb-1">{{ $branch->name }} ({{ $branch->code }})</p>
                        @if($branch->areas->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($branch->areas as $area)
                                    <div class="bg-white border border-gray-200 px-2 py-1 rounded text-xs flex items-center gap-2">
                                        {{ $area->name }} ({{ $area->code }})
                                        <form action="{{ route('admin.master.areas.destroy', $area->id) }}" method="POST"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button class="text-red-500 hover:text-red-700 font-bold">&times;</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-500">Belum ada area.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection