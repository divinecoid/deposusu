@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-slate-800">Order Synchronization</h1>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Pull Latest Orders
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-sm text-slate-500 mb-1">Orders Today</div>
            <div class="text-2xl font-bold text-slate-800">0</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-orange-200 shadow-sm">
            <div class="text-sm text-orange-600 mb-1">Shopee Orders</div>
            <div class="text-2xl font-bold text-slate-800">0</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-green-200 shadow-sm">
            <div class="text-sm text-green-600 mb-1">Tokopedia Orders</div>
            <div class="text-2xl font-bold text-slate-800">0</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-sm text-black mb-1">TikTok Orders</div>
            <div class="text-2xl font-bold text-slate-800">0</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
            <div class="flex gap-2">
                <input type="text" placeholder="Search Order ID..." class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm w-64 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none">
                <select class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none">
                    <option value="">All Marketplaces</option>
                    <option value="shopee">Shopee</option>
                    <option value="tokopedia">Tokopedia</option>
                    <option value="tiktok">TikTok Shop</option>
                </select>
                <select class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none">
                    <option value="">All Statuses</option>
                    <option value="new">New Order</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                </select>
            </div>
        </div>
        
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600 font-medium border-b border-slate-200">
                <tr>
                    <th class="py-3 px-4">Marketplace</th>
                    <th class="py-3 px-4">Order ID</th>
                    <th class="py-3 px-4">Local SO Ref</th>
                    <th class="py-3 px-4">Customer</th>
                    <th class="py-3 px-4">Total</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-center">
                @if($orders->count() > 0)
                    @foreach($orders as $order)
                        <tr>
                            <!-- TODO: Populate Data -->
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <p>No marketplace orders found.</p>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <div class="p-4 border-t border-slate-200">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
