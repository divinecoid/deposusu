@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-slate-800">Product Synchronization</h1>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Sync All Products
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
            <div class="flex gap-2">
                <input type="text" placeholder="Search SKU or Name..." class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm w-64 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none">
                <select class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none">
                    <option value="">All Marketplaces</option>
                    <option value="shopee">Shopee</option>
                    <option value="tokopedia">Tokopedia</option>
                    <option value="tiktok">TikTok Shop</option>
                </select>
            </div>
            <div class="flex items-center gap-4 text-sm text-slate-600">
                <label class="flex items-center gap-2">
                    <input type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" checked>
                    Auto Sync Price
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" checked>
                    Auto Sync Stock
                </label>
            </div>
        </div>
        
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600 font-medium border-b border-slate-200">
                <tr>
                    <th class="py-3 px-4">Local SKU</th>
                    <th class="py-3 px-4">Product Name</th>
                    <th class="py-3 px-4">Shopee Status</th>
                    <th class="py-3 px-4">Tokopedia Status</th>
                    <th class="py-3 px-4">TikTok Status</th>
                    <th class="py-3 px-4">Safety Stock</th>
                    <th class="py-3 px-4">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($products as $product)
                <tr class="hover:bg-slate-50">
                    <td class="py-3 px-4 font-medium text-slate-800">{{ $product->code ?? 'N/A' }}</td>
                    <td class="py-3 px-4">{{ $product->name }}</td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Mapped
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> Unmapped
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> Unmapped
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <input type="number" value="10" class="w-16 border border-slate-300 rounded px-2 py-1 text-xs focus:ring-blue-500 focus:border-blue-500 text-center">
                    </td>
                    <td class="py-3 px-4">
                        <button class="text-blue-600 hover:text-blue-800 font-medium text-xs">Edit Mapping</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="p-4 border-t border-slate-200">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
