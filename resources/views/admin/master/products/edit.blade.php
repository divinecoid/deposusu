@extends('layouts.admin')

@section('header', 'Edit Produk')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.master.products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                    <input type="text" name="name" value="{{ $product->name }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none" required>
                </div>

                <!-- SKU & Category -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">SKU</label>
                        <input type="text" name="sku" value="{{ $product->sku }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select name="category_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
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
                        <input type="number" name="price" value="{{ intval($product->price) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stok</label>
                        <input type="number" name="stock" value="{{ $product->stock }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" value="{{ $product->low_stock_threshold }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none">
                    </div>
                </div>

                <!-- Image URL -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Image URL</label>
                    <input type="url" name="image" value="{{ $product->image }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none">
                    <p class="mt-1 text-xs text-gray-500">Masukkan URL gambar (contoh: /images/produk-a.jpg atau https://...)</p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:border-blue-500 focus:outline-none">{{ $product->description }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.master.products.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Update Produk
                </button>
            </div>
        </form>
    </div>
@endsection
