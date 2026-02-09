@extends('layouts.admin')

@section('header', 'Stock Opname & History')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Stock Adjustment Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Hasuransi Stok (Stock Adjustment)</h3>
                    <p class="text-sm text-gray-500">Sesuaikan stok produk secara manual.</p>
                </div>

                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('info'))
                    <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('info') }}</span>
                    </div>
                @endif

                <form action="{{ route('admin.stock.adjust') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Produk</label>
                        <select name="product_id" id="product_select"
                            class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:border-blue-500"
                            required>
                            <option value="">-- Cari Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">
                                    {{ $product->name }} (Stok: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stok Saat Ini</label>
                        <input type="text" id="current_stock"
                            class="block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm py-2 px-3 text-gray-500"
                            disabled>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stok Baru (Real)</label>
                        <input type="number" name="new_stock"
                            class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:border-blue-500"
                            required min="0">
                        <p class="text-xs text-gray-500 mt-1">Masukkan jumlah stok fisik yang sebenarnya.</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan / Alasan</label>
                        <textarea name="note" rows="2"
                            class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:border-blue-500"
                            placeholder="Contoh: Barang rusak, salah hitung, dll."></textarea>
                    </div>

                    <button type="submit" class="w-full bg-red-600 text-white font-bold py-2 px-4 rounded hover:bg-red-700">
                        Update Stok
                    </button>
                </form>
            </div>
        </div>

        <!-- Stock History Log -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Riwayat Perubahan Stok</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Perubahan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ref</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($histories as $history)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $history->created_at->format('d M H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $history->product->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($history->difference > 0)
                                            <span class="text-green-600">+{{ $history->difference }}</span>
                                        @elseif($history->difference < 0)
                                            <span class="text-red-600">{{ $history->difference }}</span>
                                        @else
                                            <span class="text-gray-500">0</span>
                                        @endif
                                        <span class="text-xs text-gray-400">({{ $history->old_stock }} &to;
                                            {{ $history->new_stock }})</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 w-40 truncate">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($history->type) }}
                                        </span>
                                        <br>
                                        <span class="text-xs">{{ Str::limit($history->reference, 20) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $history->user->name ?? 'System' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada riwayat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $histories->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('product_select').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const stock = selectedOption.getAttribute('data-stock');
            document.getElementById('current_stock').value = stock ? stock : '-';
        });
    </script>
@endsection