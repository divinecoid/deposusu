@extends('layouts.admin')

@section('header', 'Promo Kategori')

@section('content')
<div class="max-w-5xl mx-auto">

    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-blue-50 rounded-lg p-6 mb-6">
        <h4 class="text-sm font-semibold text-blue-800 mb-4">Tambah Promo Kategori Baru</h4>
        <p class="text-xs text-blue-600 mb-4">Diskon ini berlaku untuk semua produk dalam kategori yang dipilih. Diskon per-produk (dari halaman Produk) tetap diprioritaskan di atas promo kategori jika keduanya aktif bersamaan.</p>
        <form action="{{ route('admin.master.promos.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-700 uppercase">Nama Promo (opsional)</label>
                    <input type="text" name="label" placeholder="Misal: Promo Kemerdekaan"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 text-sm focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase">Kategori</label>
                    <select name="category_id" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 text-sm focus:border-blue-500 focus:outline-none">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase">Tipe</label>
                    <select name="discount_type" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 text-sm focus:border-blue-500 focus:outline-none">
                        <option value="PERCENTAGE">Persentase (%)</option>
                        <option value="FIXED">Nominal (Rp)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase">Nilai</label>
                    <input type="number" name="discount_value" min="0" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 text-sm focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase">Mulai</label>
                    <input type="date" name="start_date" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 text-sm focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase">Berakhir</label>
                    <input type="date" name="end_date" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 text-sm focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 uppercase">Min. Beli (opsional)</label>
                    <input type="number" name="minimum_quantity" min="1" placeholder="Tanpa minimum"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 text-sm focus:border-blue-500 focus:outline-none">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition shadow-sm">
                    Simpan Promo
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Promo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min. Beli</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($promos as $promo)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $promo->label ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $promo->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold">
                            {{ $promo->discount_type === 'PERCENTAGE' ? $promo->discount_value . '%' : 'Rp ' . number_format($promo->discount_value, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                            {{ $promo->minimum_quantity ? $promo->minimum_quantity . ' pcs' : '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-[10px] text-gray-500">
                            {{ $promo->start_date->format('d M Y') }} - {{ $promo->end_date->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $today = now()->startOfDay();
                                $statusClass = 'bg-gray-100 text-gray-800';
                                $statusText = 'Expired';
                                if (!$promo->is_active) {
                                    $statusClass = 'bg-red-100 text-red-800';
                                    $statusText = 'Disabled';
                                } elseif ($promo->start_date->isFuture()) {
                                    $statusClass = 'bg-blue-100 text-blue-800';
                                    $statusText = 'Upcoming';
                                } elseif ($promo->end_date->greaterThanOrEqualTo($today)) {
                                    $statusClass = 'bg-green-100 text-green-800';
                                    $statusText = 'Active';
                                }
                            @endphp
                            <span class="px-2 py-1 text-[10px] font-semibold rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-medium flex gap-2">
                            <form action="{{ route('admin.master.promos.toggle', $promo->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="{{ $promo->is_active ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900' }}">
                                    {{ $promo->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form> |
                            <form action="{{ route('admin.master.promos.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Hapus promo ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 italic">Belum ada promo kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
