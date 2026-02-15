@extends('layouts.admin')

@section('header', 'Detail Order #' . $order->order_number)

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info: Items & Customer -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Customer</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Nama Customer</p>
                        <p class="font-medium">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Order</p>
                        <p class="font-medium">{{ $order->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <!-- Add more customer details here if relationship to User/Profile exists -->
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Item Pesanan</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->product->name ?? 'Produk Dihapus' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-700">Total Amount</td>
                            <td class="px-6 py-4 text-right font-bold text-blue-600 text-lg">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Invoice Info -->
            @if($order->invoice)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Invoice</h3>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($order->invoice->status == 'PAID') bg-green-100 text-green-800 
                                        @elseif($order->invoice->status == 'CANCELLED') bg-red-100 text-red-800 
                                        @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $order->invoice->status }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Invoice Number</p>
                            <p class="font-medium">{{ $order->invoice->invoice_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Due Date</p>
                            <p class="font-medium">{{ $order->invoice->due_date->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar: Status & Action -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Update Status Order</h3>
                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Saat Ini</label>
                        <select name="status"
                            class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:border-blue-500"
                            onchange="toggleDriverSelect(this.value)">
                            <option value="pending" {{ $order->status->value == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="onprocess" {{ $order->status->value == 'onprocess' ? 'selected' : '' }}>On Process
                                (Confirm)</option>
                            <option value="onpreparation" {{ $order->status->value == 'onpreparation' ? 'selected' : '' }}>On
                                Preparation</option>
                            <option value="prepared" {{ $order->status->value == 'prepared' ? 'selected' : '' }}>Prepared
                            </option>
                            <option value="ondelivery" {{ $order->status->value == 'ondelivery' ? 'selected' : '' }}>On
                                Delivery
                            </option>
                            <option value="delivered" {{ $order->status->value == 'delivered' ? 'selected' : '' }}>Delivered
                            </option>
                            <option value="partialdelivered" {{ $order->status->value == 'partialdelivered' ? 'selected' : '' }}>
                                Partial Delivered</option>
                            <option value="done" {{ $order->status->value == 'done' ? 'selected' : '' }}>Done (Complete)
                            </option>
                            <option value="cancelled" {{ $order->status->value == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                            <option value="rejected" {{ $order->status->value == 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>
                    </div>

                    <div id="driver-select-container"
                        class="mb-6 {{ in_array($order->status->value, ['onprocess', 'onpreparation', 'prepared', 'ondelivery']) ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign Driver</label>
                        <select name="driver_id"
                            class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:border-blue-500">
                            <option value="">-- Pilih Driver --</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ $order->driver_id == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pilih driver jika status On Delivery.</p>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                        Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleDriverSelect(status) {
            const container = document.getElementById('driver-select-container');
            // Show driver select if status is related to delivery preparation or delivery itself
            if (['onprocess', 'onpreparation', 'prepared', 'ondelivery'].includes(status)) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
    </script>
@endsection