@extends('layouts.admin')

@section('header', 'Sales Order')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-blue-600 text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                Sales Order
            </h1>
            <p class="text-slate-500 text-sm mt-1">Order manual dari WhatsApp / kontak langsung</p>
        </div>
        <a href="{{ route('admin.sales-order.create') }}"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-blue-600/20 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Sales Order
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Semua', 'key' => 'all', 'color' => 'slate', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'Pending', 'key' => 'pending', 'color' => 'amber', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Diproses', 'key' => 'onprocess', 'color' => 'blue', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
            ['label' => 'Selesai', 'key' => 'done', 'color' => 'emerald', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as $stat)
        <a href="{{ route('admin.sales-order.index', ['status' => $stat['key']]) }}"
            class="bg-white rounded-2xl border {{ $status === $stat['key'] ? 'border-blue-400 ring-2 ring-blue-100' : 'border-slate-100' }} shadow-sm p-4 flex items-center gap-3 hover:shadow-md transition">
            <div class="w-10 h-10 rounded-xl bg-{{ $stat['color'] }}-100 text-{{ $stat['color'] }}-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/></svg>
            </div>
            <div>
                <div class="text-xl font-extrabold text-slate-800">{{ $counts[$stat['key']] }}</div>
                <div class="text-xs text-slate-500 font-medium">{{ $stat['label'] }}</div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Cari Customer</label>
                <input type="text" name="customer" value="{{ $customer }}" placeholder="Nama / nomor HP..."
                    class="w-full border border-slate-200 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Dari</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="border border-slate-200 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Sampai</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="border border-slate-200 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">Filter</button>
            <a href="{{ route('admin.sales-order.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-semibold px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 transition">Reset</a>
        </form>
    </div>

    {{-- Status filter tabs --}}
    <div class="flex flex-wrap gap-2">
        @foreach([
            ['key' => 'all', 'label' => 'Semua'],
            ['key' => 'pending', 'label' => 'Pending'],
            ['key' => 'onprocess', 'label' => 'Diproses'],
            ['key' => 'onpreparation', 'label' => 'Packing'],
            ['key' => 'ondelivery', 'label' => 'Dikirim'],
            ['key' => 'done', 'label' => 'Selesai'],
            ['key' => 'cancelled', 'label' => 'Dibatalkan'],
        ] as $tab)
        <a href="{{ route('admin.sales-order.index', array_merge(request()->query(), ['status' => $tab['key']])) }}"
            class="px-4 py-1.5 rounded-full text-sm font-semibold border transition
            {{ $status === $tab['key'] ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300 hover:text-blue-600' }}">
            {{ $tab['label'] }}
            @if(isset($counts[$tab['key']])) <span class="ml-1 text-xs opacity-75">({{ $counts[$tab['key']] ?? 0 }})</span> @endif
        </a>
        @endforeach
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if(session('success'))
        <div class="px-6 py-3 bg-emerald-50 border-b border-emerald-100 text-emerald-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wide">No. Order</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wide">Customer</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wide">Pengiriman</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wide">Pembayaran</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wide">Total</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="font-mono text-sm font-bold text-blue-700">{{ $order->order_number }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $order->created_at->format('d M Y · H:i') }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-slate-800 text-sm">{{ $order->customer_name }}</div>
                            @if($order->customer_phone)
                            <div class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $order->customer_phone }}
                            </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm">
                            @if($order->delivery_date)
                                <div class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}</div>
                                @if($order->delivery_slot)
                                <div class="text-xs text-slate-400 capitalize mt-0.5">
                                    {{ $order->delivery_slot === 'pagi' ? '🌅 Pagi' : ($order->delivery_slot === 'siang' ? '☀️ Siang' : '🌇 Sore') }}
                                </div>
                                @endif
                            @else
                                <span class="text-slate-400 text-xs">Belum dijadwalkan</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($order->payment_method)
                            @php $pm = ['transfer'=>'🏦 Transfer','cash'=>'💵 Cash','cod'=>'🚚 COD','wallet'=>'👜 Wallet','piutang'=>'📋 Piutang']; @endphp
                            <span class="text-sm text-slate-700 font-medium">{{ $pm[$order->payment_method] ?? $order->payment_method }}</span>
                            @endif
                            <div class="mt-0.5">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $order->payment_status === 'PAID' ? 'bg-emerald-100 text-emerald-700' : ($order->payment_status === 'CANCELLED' ? 'bg-slate-100 text-slate-500' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $order->payment_status }}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right font-bold text-slate-800 text-sm">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'onprocess' => 'bg-blue-100 text-blue-700',
                                    'onpreparation' => 'bg-violet-100 text-violet-700',
                                    'prepared' => 'bg-indigo-100 text-indigo-700',
                                    'ondelivery' => 'bg-cyan-100 text-cyan-700',
                                    'delivered' => 'bg-teal-100 text-teal-700',
                                    'done' => 'bg-emerald-100 text-emerald-700',
                                    'cancelled' => 'bg-slate-100 text-slate-500',
                                    'rejected' => 'bg-red-100 text-red-700',
                                ];
                                $statusVal = $order->status instanceof \App\Enums\OrderStatusEnum ? $order->status->value : $order->status;
                                $colorClass = $statusColors[strtolower($statusVal)] ?? 'bg-slate-100 text-slate-600';
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $colorClass }} capitalize">
                                {{ ucfirst($statusVal) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-slate-500 font-medium mb-1">Belum ada Sales Order</p>
                            <p class="text-slate-400 text-sm mb-4">Order dari WhatsApp / kontak langsung akan muncul di sini.</p>
                            <a href="{{ route('admin.sales-order.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Buat Sales Order Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
