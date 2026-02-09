@extends('layouts.admin')

@section('header', 'Invoice Management')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                @foreach(['all', 'unpaid', 'paid', 'cancelled'] as $tabStatus)
                        <a href="{{ route('admin.invoices.index', ['status' => $tabStatus]) }}" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm
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

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Ref
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                {{ $invoice->invoice_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <a href="{{ route('admin.orders.show', $invoice->order_id) }}" class="hover:underline">
                                    #{{ $invoice->order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $invoice->order->customer_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $invoice->issue_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($invoice->status == 'PAID') bg-green-100 text-green-800 
                                            @elseif($invoice->status == 'CANCELLED') bg-red-100 text-red-800 
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <form action="{{ route('admin.invoices.updateStatus', $invoice->id) }}" method="POST"
                                    class="inline-flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="status"
                                        class="text-xs border-gray-300 rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        onchange="this.form.submit()">
                                        <option value="UNPAID" {{ $invoice->status == 'UNPAID' ? 'selected' : '' }}>UNPAID
                                        </option>
                                        <option value="PAID" {{ $invoice->status == 'PAID' ? 'selected' : '' }}>PAID</option>
                                        <option value="CANCELLED" {{ $invoice->status == 'CANCELLED' ? 'selected' : '' }}>
                                            CANCELLED</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $invoices->appends(['status' => $status])->links() }}
        </div>
    </div>
@endsection