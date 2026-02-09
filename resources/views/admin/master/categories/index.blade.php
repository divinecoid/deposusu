@extends('layouts.admin')

@section('header', 'Master Data: Kategori Produk')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Tambah Kategori Baru</h3>
            <form action="{{ route('admin.master.categories.store') }}" method="POST" class="flex gap-4">
                @csrf
                <div class="flex-1">
                    <input type="text" name="name" placeholder="Nama Kategori (contoh: Susu Segar)"
                        class="w-full px-4 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none"
                        required>
                </div>
                <!-- Optional Icon Input -->
                <div class="flex-1">
                    <input type="text" name="icon" placeholder="Icon URL / Class (Optional)"
                        class="w-full px-4 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Tambah
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah
                            Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $category->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->slug }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->products_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <!-- Edit Trigger (Simple modal logic or inline would be better, keeping it simple for now) -->
                                    <button onclick="promptEdit({{ $category->id }}, '{{ $category->name }}')"
                                        class="text-indigo-600 hover:text-indigo-900">Edit</button>

                                    <form action="{{ route('admin.master.categories.destroy', $category->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Simple Edit Script -->
    <script>
        function promptEdit(id, oldName) {
            const newName = prompt("Edit Nama Kategori:", oldName);
            if (newName && newName !== oldName) {
                // Create a dynamic form to submit update
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/master/categories/${id}`;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';

                const nameInput = document.createElement('input');
                nameInput.type = 'hidden';
                nameInput.name = 'name';
                nameInput.value = newName;

                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                form.appendChild(nameInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection