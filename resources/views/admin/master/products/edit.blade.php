@extends('layouts.admin')

@section('header', 'Edit Produk')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.master.products.update', $product->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                    <input type="text" name="name" value="{{ $product->name }}"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none"
                        required>
                </div>

                <!-- SKU, Barcode & Category -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">SKU</label>
                        <input type="text" name="sku" value="{{ $product->sku }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Barcode</label>
                        <input type="text" name="barcode" value="{{ $product->barcode }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori (Ctrl+Click untuk pilih
                            banyak)</label>
                        <select name="categories[]" multiple
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none h-32"
                            required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ in_array($category->id, $product->categories->pluck('id')->toArray()) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Price & Stock -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                        <input type="number" name="price" value="{{ intval($product->price) }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stok</label>
                        <input type="number" name="stock" value="{{ $product->stock }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" value="{{ $product->low_stock_threshold }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none">
                    </div>
                </div>

                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Gambar Produk</label>
                    @if($product->image)
                        <div class="mb-2">
                            <img src="{{ $product->image && str_starts_with($product->image, 'storage/') ? asset($product->image) : $product->image }}"
                                alt="Current Image" class="h-20 w-20 object-cover rounded border border-gray-200"
                                onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=No+Image';">
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, GIF. Max: 2MB. Biarkan kosong jika tidak ingin
                        mengganti.</p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none">{{ $product->description }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.master.products.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Update Produk
                </button>
            </div>
        </form>

        <!-- Discount Management Section -->
        <hr class="my-8">

        <div class="mt-8" x-data="{ 
                editModalMode: false, 
                editDiscount: { id: '', type: '', value: '', start: '', end: '', url: '' },
                openEdit(discount) {
                    this.editDiscount = discount;
                    this.editModalMode = true;
                }
            }">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Manajemen Diskon</h3>

            <!-- Add Discount Form -->
            <div class="bg-blue-50 rounded-lg p-6 mb-6">
                <h4 class="text-sm font-semibold text-blue-800 mb-4">Tambah Diskon Baru</h4>
                <form action="{{ route('admin.master.products.discount.store', $product->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 uppercase">Tipe</label>
                            <select name="discount_type"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 text-sm focus:border-blue-500 focus:outline-none"
                                required>
                                <option value="PERCENTAGE">Persentase (%)</option>
                                <option value="FIXED">Nominal (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 uppercase">Nilai</label>
                            <input type="number" name="discount_value"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 text-sm focus:border-blue-500 focus:outline-none"
                                required min="0">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 uppercase">Mulai</label>
                            <input type="date" name="start_date"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 text-sm focus:border-blue-500 focus:outline-none"
                                required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 uppercase">Berakhir</label>
                            <input type="date" name="end_date"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 text-sm focus:border-blue-500 focus:outline-none"
                                required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition shadow-sm">
                            Simpan Diskon
                        </button>
                    </div>
                </form>
            </div>

            <!-- Discount History -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Periode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($product->discounts()->latest()->get() as $discount)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $discount->discount_type === 'PERCENTAGE' ? 'Persentase' : 'Nominal' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-bold">
                                    {{ $discount->discount_type === 'PERCENTAGE' ? $discount->discount_value . '%' : 'Rp ' . number_format($discount->discount_value, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-[10px] text-gray-500">
                                    {{ $discount->start_date->format('d M Y') }} -
                                    {{ $discount->end_date->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $today = now()->startOfDay();
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        $statusText = 'Expired';

                                        if (!$discount->is_active) {
                                            $statusClass = 'bg-red-100 text-red-800';
                                            $statusText = 'Disabled';
                                        } elseif ($discount->start_date->isFuture()) {
                                            $statusClass = 'bg-blue-100 text-blue-800';
                                            $statusText = 'Upcoming';
                                        } elseif ($discount->end_date->greaterThanOrEqualTo($today)) {
                                            $statusClass = 'bg-green-100 text-green-800';
                                            $statusText = 'Active';
                                        }
                                    @endphp
                                    <span class="px-2 py-1 text-[10px] font-semibold rounded-full {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs font-medium flex gap-2">
                                    <button type="button" @click="openEdit({ 
                                                    id: '{{ $discount->id }}', 
                                                    type: '{{ $discount->discount_type }}', 
                                                    value: '{{ $discount->discount_value }}',
                                                    start: '{{ $discount->start_date->format('Y-m-d') }}',
                                                    end: '{{ $discount->end_date->format('Y-m-d') }}',
                                                    url: '{{ route('admin.master.products.discount.update', $discount->id) }}'
                                                })" class="text-blue-600 hover:text-blue-900">
                                        Edit
                                    </button> |
                                    <form action="{{ route('admin.master.products.discount.toggle', $discount->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="{{ $discount->is_active ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900' }}">
                                            {{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form> |
                                    <form action="{{ route('admin.master.products.discount.destroy', $discount->id) }}"
                                        method="POST" onsubmit="return confirm('Hapus riwayat diskon ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 italic">
                                    Belum ada riwayat diskon.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Edit Modal Overlay -->
            <div x-show="editModalMode"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4" x-cloak>
                <div @click.away="editModalMode = false" class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-bold text-gray-900">Edit Diskon</h4>
                        <button @click="editModalMode = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="editDiscount.url" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 uppercase">Tipe</label>
                                <select name="discount_type" x-model="editDiscount.type"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none"
                                    required>
                                    <option value="PERCENTAGE">Persentase (%)</option>
                                    <option value="FIXED">Nominal (Rp)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 uppercase">Nilai</label>
                                <input type="number" name="discount_value" x-model="editDiscount.value"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none"
                                    required min="0">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 uppercase">Mulai</label>
                                    <input type="date" name="start_date" x-model="editDiscount.start"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 uppercase">Berakhir</label>
                                    <input type="date" name="end_date" x-model="editDiscount.end"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="editModalMode = false"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    [x-cloak] {
        display: none !important;
    }
</style>