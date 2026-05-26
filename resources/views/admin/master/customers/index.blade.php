@extends('layouts.admin')

@section('header', 'Master Data: Customer')

@section('content')
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Customer</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Telp
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $customer)
                        <tr>
                            <td class="px-6 py-4" colspan="7">
                                <!-- View Mode -->
                                <div class="view-mode-{{ $customer->id }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 grid grid-cols-7 gap-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $customer->name }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">{{ $customer->email }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    {{ $customer->customerProfile->phone ?? '-' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500 max-w-xs truncate">
                                                    {{ $customer->customerProfile->address ?? '-' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    {{ $customer->customerProfile->area->name ?? '-' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    {{ $customer->created_at->format('d M Y') }}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2 justify-end">
                                                <button onclick="editCustomer({{ $customer->id }})"
                                                    class="text-blue-600 hover:text-blue-800 text-sm px-3 py-1 rounded hover:bg-blue-50 transition-colors flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.master.customers.destroy', $customer->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmDeleteCustomer(this)"
                                                        class="text-red-600 hover:text-red-800 text-sm px-3 py-1 rounded hover:bg-red-50 transition-colors flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Mode (Hidden by default) -->
                                <div class="edit-mode-{{ $customer->id }} hidden">
                                    <form action="{{ route('admin.master.customers.update', $customer->id) }}" method="POST"
                                        class="space-y-3">
                                        @csrf @method('PUT')
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Nama</label>
                                                <input type="text" name="name" value="{{ $customer->name }}"
                                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                                                <input type="email" name="email" value="{{ $customer->email }}"
                                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">No. Telepon</label>
                                                <input type="text" name="phone"
                                                    value="{{ $customer->customerProfile->phone ?? '' }}"
                                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Area</label>
                                                <select name="area_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                                    <option value="">-- Pilih Area --</option>
                                                    @foreach($areas as $area)
                                                        <option value="{{ $area->id }}" {{ ($customer->customerProfile && $customer->customerProfile->area_id == $area->id) ? 'selected' : '' }}>
                                                            {{ $area->name }} ({{ $area->code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Alamat</label>
                                                <textarea name="address" rows="2"
                                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm">{{ $customer->customerProfile->address ?? '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="flex gap-2 pt-2">
                                            <button type="submit"
                                                class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 transition-colors">
                                                Simpan
                                            </button>
                                            <button type="button" onclick="cancelEditCustomer({{ $customer->id }})"
                                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-400 transition-colors">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada customer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $customers->links() }}
        </div>
    </div>

    <script>
        // Edit customer
        function editCustomer(customerId) {
            document.querySelector('.view-mode-' + customerId).classList.add('hidden');
            document.querySelector('.edit-mode-' + customerId).classList.remove('hidden');
        }

        // Cancel edit
        function cancelEditCustomer(customerId) {
            document.querySelector('.view-mode-' + customerId).classList.remove('hidden');
            document.querySelector('.edit-mode-' + customerId).classList.add('hidden');
        }

        // Confirm delete
        function confirmDeleteCustomer(button) {
            if (confirm('Apakah Anda yakin ingin menghapus customer ini?')) {
                button.closest('form').submit();
            }
        }
    </script>
@endsection