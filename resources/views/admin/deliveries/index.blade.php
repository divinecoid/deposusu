@extends('layouts.admin')

@section('header', 'Delivery Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="deliveryApp()">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-violet-600 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </span>
                Delivery Management
            </h1>
            <p class="text-slate-500 text-sm mt-1">Kelola distribusi pengiriman pesanan dan kurir</p>
        </div>
    </div>

    {{-- Tabs & Filters --}}
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <div class="flex flex-wrap gap-2">
            @foreach([
                ['k' => 'pending', 'l' => 'Siap Dikirim', 'c' => $counts['pending']],
                ['k' => 'shipping', 'l' => 'Sedang Dikirim', 'c' => $counts['shipping']],
                ['k' => 'completed', 'l' => 'Selesai Kirim', 'c' => $counts['completed']],
            ] as $tab)
            <a href="{{ route('admin.deliveries.index', array_merge(request()->query(), ['status' => $tab['k']])) }}"
                class="px-4 py-1.5 rounded-full text-sm font-semibold border transition
                {{ $status === $tab['k'] ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-slate-600 border-slate-200 hover:border-violet-300' }}">
                {{ $tab['l'] }} <span class="ml-1 text-xs opacity-75">({{ $tab['c'] }})</span>
            </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.deliveries.index') }}" class="flex gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <select name="driver_id" onchange="this.form.submit()" class="border border-slate-200 rounded-xl py-1.5 px-3 text-sm bg-white focus:outline-none">
                <option value="all">Semua Kurir</option>
                @foreach($drivers as $d)
                <option value="{{ $d->id }}" {{ $driverId == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    {{-- Bulk Assignment Bar --}}
    <div x-show="selectedOrders.length > 0" x-cloak class="bg-violet-50 border border-violet-200 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-violet-800 font-semibold">
            <span x-text="selectedOrders.length"></span> pesanan terpilih untuk pengiriman.
        </div>
        <form action="{{ route('admin.deliveries.assign-bulk') }}" method="POST" class="flex gap-2 w-full sm:w-auto">
            @csrf
            <template x-for="id in selectedOrders" :key="id">
                <input type="hidden" name="order_ids[]" :value="id">
            </template>
            <select name="driver_id" required class="flex-1 sm:w-48 border border-slate-200 rounded-xl py-2 px-3 text-sm bg-white focus:ring-2 focus:ring-violet-500">
                <option value="">-- Pilih Kurir --</option>
                @foreach($drivers as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition">Assign & Kirim</button>
        </form>
    </div>

    {{-- Deliveries Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        @if($status === 'pending')
                        <th class="px-5 py-3 w-10">
                            <input type="checkbox" @change="toggleAll($event)" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        </th>
                        @endif
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Order / Invoice</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Customer & Alamat</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Jadwal Kirim</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Kurir</th>
                        @if($status === 'shipping')
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Konfirmasi Pengiriman</th>
                        @endif
                        @if($status === 'completed')
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Bukti Delivery</th>
                        @endif
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($deliveries as $del)
                    <tr class="hover:bg-slate-50/50">
                        @if($status === 'pending')
                        <td class="px-5 py-4">
                            <input type="checkbox" :value="{{ $del->id }}" x-model="selectedOrders" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        </td>
                        @endif
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="font-mono font-bold text-blue-700">{{ $del->order_number }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $del->source === 'admin' ? '📋 WhatsApp/Manual' : '📱 App' }}</div>
                        </td>
                        <td class="px-5 py-4 text-sm max-w-xs">
                            <div class="font-semibold text-slate-800">{{ $del->customer_name }}</div>
                            <div class="text-xs text-slate-500 truncate mt-0.5">{{ $del->customer_address ?: 'Alamat tidak diisi' }}</div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-700">
                            {{ $del->delivery_date ? $del->delivery_date->format('d M Y') : 'Belum dijadwalkan' }}
                            @if($del->delivery_slot)
                            <div class="text-xs text-slate-400 capitalize mt-0.5">{{ $del->delivery_slot }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-800">
                            {{ $del->driver->name ?? 'Belum ada kurir' }}
                        </td>
                        @if($status === 'shipping')
                        <td class="px-5 py-4 max-w-sm">
                            <form action="{{ route('admin.deliveries.update', $del->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2 items-center">
                                @csrf
                                <div class="flex gap-2 w-full">
                                    <input type="text" name="recipient_name" placeholder="Penerima..." class="border border-slate-200 rounded-lg p-1 text-xs flex-1">
                                    <select name="status" required class="border border-slate-200 rounded-lg p-1 text-xs">
                                        <option value="delivered">✅ Delivered / Selesai</option>
                                        <option value="failed">❌ Failed / Reschedule</option>
                                    </select>
                                </div>
                                <div class="flex gap-2 w-full items-center">
                                    <input type="file" name="delivery_proof_photo" accept="image/*" class="text-[10px] flex-1">
                                    <button type="submit" class="bg-violet-600 hover:bg-violet-700 text-white text-[10px] font-bold px-2 py-1.5 rounded-lg transition">Submit</button>
                                </div>
                            </form>
                        </td>
                        @endif
                        @if($status === 'completed')
                        <td class="px-5 py-4 text-center">
                            @if($del->delivery_proof_photo)
                            <a href="{{ asset('storage/' . $del->delivery_proof_photo) }}" target="_blank" class="text-xs font-semibold text-violet-600 hover:underline flex items-center justify-center gap-1">
                                🖼️ Lihat Foto
                            </a>
                            @else
                            <span class="text-slate-400 text-xs">Tidak ada foto</span>
                            @endif
                            @if($del->recipient_name)
                            <div class="text-[10px] text-slate-500 mt-1">Penerima: {{ $del->recipient_name }}</div>
                            @endif
                        </td>
                        @endif
                        <td class="px-5 py-4 text-center whitespace-nowrap">
                            <a href="{{ route('admin.orders.show', $del->id) }}" class="text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">Detail Order</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-slate-400">Tidak ada pengiriman dalam status ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($deliveries->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $deliveries->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>

<script>
function deliveryApp() {
    return {
        selectedOrders: [],
        toggleAll(event) {
            if (event.target.checked) {
                this.selectedOrders = Array.from(document.querySelectorAll('input[type="checkbox"][value]')).map(cb => parseInt(cb.value));
            } else {
                this.selectedOrders = [];
            }
        }
    };
}
</script>
@endsection
