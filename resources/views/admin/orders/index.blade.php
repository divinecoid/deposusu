@extends('layouts.admin')

@section('header', 'Manajemen Order')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Daftar Transaksi</h3>
            <p class="text-sm text-gray-500 mt-1">Kelola dan input pesanan toko secara manual.</p>
        </div>
        <a href="{{ route('admin.sales-order.create') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow transition duration-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Order Manual</span>
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex overflow-x-auto" aria-label="Tabs">
                @foreach(['all', 'pending', 'onprocess', 'onpreparation', 'prepared', 'ondelivery', 'delivered', 'done', 'cancelled', 'rejected'] as $tabStatus)
                        <a href="{{ route('admin.orders.index', array_merge(request()->query(), ['status' => $tabStatus])) }}" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm
                                                {{ $status === $tabStatus
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            {{ ucfirst($tabStatus) }}
                            <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium bg-gray-100 text-gray-900">
                                {{ $counts[$tabStatus] ?? 0 }}
                            </span>
                        </a>
                @endforeach
            </nav>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.orders.index') }}" class="bg-gray-50 border-b border-gray-200 p-6">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div>
                    <label for="customer" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Customer</label>
                    <input type="text" id="customer" name="customer" value="{{ $customer }}" placeholder="Cari nama customer..." class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div>
                    <label for="start_date" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div>
                    <label for="end_date" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tanggal Akhir</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div>
                    <label for="source" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Sumber</label>
                    <select id="source" name="source" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="all" {{ $source === 'all' ? 'selected' : '' }}>Semua Sumber</option>
                        <option value="customer" {{ $source === 'customer' ? 'selected' : '' }}>Customer (Aplikasi)</option>
                        <option value="admin" {{ $source === 'admin' ? 'selected' : '' }}>Admin Toko</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition duration-200 flex justify-center items-center gap-1.5 shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Cari
                    </button>
                    <a href="{{ route('admin.orders.index', ['status' => $status]) }}" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl text-sm transition duration-200 flex justify-center items-center shadow-sm">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order
                            Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                <a href="{{ route('admin.orders.show', $order->id) }}">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $order->customer_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(($order->source ?? 'customer') === 'admin')
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-purple-100 text-purple-800 shadow-sm border border-purple-200">
                                        Admin Toko
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-blue-100 text-blue-800 shadow-sm border border-blue-200">
                                        Customer
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($order->status->value === 'pending') bg-yellow-100 text-yellow-800 
                                                    @elseif($order->status->value === 'done') bg-green-100 text-green-800 
                                                    @elseif($order->status->value === 'cancelled') bg-red-100 text-red-800 
                                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->driver ? $order->driver->name : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                             <td colspan="8" class="px-6 py-4 text-center text-gray-500">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>
@endsection