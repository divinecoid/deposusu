@extends('layouts.admin')

@section('header', 'Master Data: Produk')

@section('content')
    <div class="bg-white rounded-lg shadow p-6" x-data="{
                search: {
                    name: '',
                    category: '',
                    sku: '',
                    barcode: ''
                },
                isVisible(row) {
                    return (this.search.name === '' || row.name.toLowerCase().includes(this.search.name.toLowerCase())) &&
                           (this.search.category === '' || row.category.toLowerCase().includes(this.search.category.toLowerCase())) &&
                           (this.search.sku === '' || row.sku.toLowerCase().includes(this.search.sku.toLowerCase())) &&
                           (this.search.barcode === '' || row.barcode.toLowerCase().includes(this.search.barcode.toLowerCase()));
                }
            }">
        <div class="flex justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Daftar Produk</h2>
            <a href="{{ route('admin.master.products.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Tambah Produk
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                            Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                    <!-- Search Row -->
                    <tr class="bg-gray-100 border-b">
                        <td class="px-6 py-2"></td>
                        <td class="px-6 py-2">
                            <input type="text" x-model="search.name" placeholder="Cari nama..."
                                class="w-full text-xs border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-1 px-2">
                        </td>
                        <td class="px-6 py-2">
                            <input type="text" x-model="search.category" placeholder="Cari kategori..."
                                class="w-full text-xs border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-1 px-2">
                        </td>
                        <td class="px-6 py-2"></td>
                        <td class="px-6 py-2">
                            <div class="flex flex-col gap-1">
                                <input type="text" x-model="search.sku" placeholder="Cari SKU..."
                                    class="w-full text-[10px] border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-0.5 px-2">
                                <input type="text" x-model="search.barcode" placeholder="Cari Barcode..."
                                    class="w-full text-[10px] border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 py-0.5 px-2">
                            </div>
                        </td>
                        <td class="px-6 py-2"></td>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                        <tr x-show="isVisible({
                                            name: '{{ addslashes($product->name) }}',
                                            category: '{{ addslashes($product->categories->pluck('name')->implode(', ') ?: 'N/A') }}',
                                            sku: '{{ addslashes($product->sku ?? '') }}',
                                            barcode: '{{ addslashes($product->barcode ?? '') }}'
                                        })" x-transition>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->image)
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                                        class="h-10 w-10 object-cover rounded">
                                @else
                                    <span class="text-gray-400">No Image</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $product->name }}
                                <div class="text-xs text-gray-500">
                                    SKU: {{ $product->sku ?? '-' }} | Barcode: {{ $product->barcode ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $product->categories->pluck('name')->implode(', ') ?: 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($product->active_discount)
                                    <div class="text-xs text-gray-400 line-through">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </div>
                                    <div class="font-bold text-blue-600">
                                        Rp {{ number_format($product->discounted_price, 0, ',', '.') }}
                                    </div>
                                    <div class="text-[10px] text-red-500">
                                        @if($product->active_discount->discount_type === 'PERCENTAGE')
                                            -{{ number_format($product->active_discount->discount_value, 0) }}%
                                        @else
                                            Disc: Rp {{ number_format($product->active_discount->discount_value, 0, ',', '.') }}
                                        @endif
                                    </div>
                                @else
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $product->stock }}
                                @if($product->stock <= $product->low_stock_threshold)
                                    <span
                                        class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Low
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.master.products.edit', $product->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <form action="{{ route('admin.master.products.destroy', $product->id) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection