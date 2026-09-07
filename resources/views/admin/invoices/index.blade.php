@extends('layouts.admin')

@section('header', 'Invoice Management')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                @foreach(['all', 'unpaid', 'paid', 'cancelled'] as $tabStatus)
                        <a href="{{ route('admin.invoices.index', array_merge(request()->query(), ['status' => $tabStatus])) }}" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm
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
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="bg-gray-50 border-b border-gray-200 p-6 flex flex-wrap gap-4 items-end">

            <div class="w-full sm:w-48">
                <label for="status" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Status Pembayaran</label>
                <select id="status" name="status" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @foreach(['all' => 'Semua', 'unpaid' => 'Unpaid', 'paid' => 'Paid', 'cancelled' => 'Cancelled'] as $value => $label)
                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-56">
                <label for="customer" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Customer</label>
                <input type="text" id="customer" name="customer" value="{{ $customer }}" placeholder="Cari nama customer..."
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <div class="w-full sm:w-44">
                <label for="date_from" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Dari Tanggal</label>
                <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}"
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <div class="w-full sm:w-44">
                <label for="date_to" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Sampai Tanggal</label>
                <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}"
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <div class="w-full sm:w-64">
                <label for="delivery_status" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Status Pengiriman</label>
                <select id="delivery_status" name="delivery_status" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="all" {{ $deliveryStatus === 'all' ? 'selected' : '' }}>Semua Pengiriman</option>
                    <option value="shipped" {{ $deliveryStatus === 'shipped' ? 'selected' : '' }}>Sudah Terkirim (Delivered/Done)</option>
                    <option value="pending" {{ $deliveryStatus === 'pending' ? 'selected' : '' }}>Belum Terkirim</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition duration-200 shadow-sm">
                Terapkan Filter
            </button>

            <a href="{{ route('admin.invoices.index', ['status' => $status]) }}" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl text-sm transition duration-200 shadow-sm">
                Reset Filter
            </a>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Ref</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengiriman</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="hover:underline">
                                    {{ $invoice->invoice_number }}
                                </a>
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
                                @php
                                    $orderStatus = $invoice->order->status->value;
                                    $isShipped = in_array($orderStatus, ['delivered', 'partialdelivered', 'done']);
                                @endphp
                                @if($isShipped)
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-green-100 text-green-800 shadow-sm border border-green-200">
                                        Sudah Terkirim
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-yellow-100 text-yellow-800 shadow-sm border border-yellow-200">
                                        Belum Terkirim ({{ $invoice->order->status->label() }})
                                    </span>
                                @endif
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
                                        data-invoice-number="{{ $invoice->invoice_number }}"
                                        data-customer-name="{{ $invoice->order->customer_name }}"
                                        data-total-amount="Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}"
                                        data-delivery-status="{{ $invoice->order->status->label() }}"
                                        class="invoice-status-select text-xs border-gray-300 rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="UNPAID" {{ $invoice->status == 'UNPAID' ? 'selected' : '' }}>UNPAID</option>
                                        <option value="PAID" {{ $invoice->status == 'PAID' ? 'selected' : '' }}>PAID</option>
                                        <option value="CANCELLED" {{ $invoice->status == 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $invoices->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Beautiful Tailwind Modal -->
    <div id="confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform scale-95 transition-transform duration-300 border border-gray-100">
            <!-- Modal Header -->
            <div class="px-6 py-5 bg-gradient-to-r from-blue-600 to-blue-500 text-white flex justify-between items-center">
                <h3 class="text-lg font-bold">Konfirmasi Ubah Status</h3>
                <button type="button" class="modal-close-trigger text-white/80 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-500">Apakah Anda yakin ingin mengubah status pembayaran invoice ini?</p>
                
                <div class="bg-gray-50 rounded-xl p-4 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">No. Invoice:</span>
                        <span id="modal-invoice-number" class="font-semibold text-gray-800"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Customer:</span>
                        <span id="modal-customer" class="font-semibold text-gray-800"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Tagihan:</span>
                        <span id="modal-amount" class="font-semibold text-blue-600"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Pengiriman:</span>
                        <span id="modal-delivery" class="font-semibold text-gray-800"></span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                        <span class="text-gray-500">Status Baru:</span>
                        <span id="modal-new-status"></span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                <button type="button" id="modal-cancel-btn" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl text-sm transition shadow-xs">
                    Batal
                </button>
                <button type="button" id="modal-confirm-btn" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow">
                    Ya, Ubah Status
                </button>
            </div>
        </div>
    </div>

    <!-- Script Block -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let activeSelectForm = null;
            let previousStatusValue = null;

            document.querySelectorAll('.invoice-status-select').forEach(select => {
                select.addEventListener('focus', function() {
                    previousStatusValue = this.value;
                });

                select.addEventListener('change', function(e) {
                    const newStatus = this.value;
                    const invoiceNumber = this.getAttribute('data-invoice-number');
                    const customerName = this.getAttribute('data-customer-name');
                    const totalAmount = this.getAttribute('data-total-amount');
                    const deliveryStatus = this.getAttribute('data-delivery-status');
                    
                    activeSelectForm = this.form;
                    
                    // Populate modal fields
                    document.getElementById('modal-invoice-number').textContent = invoiceNumber;
                    document.getElementById('modal-customer').textContent = customerName;
                    document.getElementById('modal-amount').textContent = totalAmount;
                    document.getElementById('modal-delivery').textContent = deliveryStatus;
                    
                    const statusBadge = document.getElementById('modal-new-status');
                    statusBadge.textContent = newStatus;
                    
                    // Style new status badge
                    statusBadge.className = 'px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full ';
                    if (newStatus === 'PAID') {
                        statusBadge.classList.add('bg-green-100', 'text-green-800', 'border', 'border-green-200');
                    } else if (newStatus === 'CANCELLED') {
                        statusBadge.classList.add('bg-red-100', 'text-red-800', 'border', 'border-red-200');
                    } else {
                        statusBadge.classList.add('bg-yellow-100', 'text-yellow-800', 'border', 'border-yellow-200');
                    }
                    
                    openConfirmationModal();
                });
            });

            function openConfirmationModal() {
                const modal = document.getElementById('confirm-modal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeConfirmationModal(revert = false) {
                const modal = document.getElementById('confirm-modal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                
                if (revert && activeSelectForm && previousStatusValue) {
                    activeSelectForm.querySelector('.invoice-status-select').value = previousStatusValue;
                }
                activeSelectForm = null;
            }

            document.getElementById('modal-confirm-btn').addEventListener('click', function() {
                if (activeSelectForm) {
                    activeSelectForm.submit();
                }
            });

            document.getElementById('modal-cancel-btn').addEventListener('click', function() {
                closeConfirmationModal(true);
            });

            document.querySelectorAll('.modal-close-trigger').forEach(trigger => {
                trigger.addEventListener('click', function() {
                    closeConfirmationModal(true);
                });
            });
        });
    </script>
@endsection