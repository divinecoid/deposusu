@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-slate-800">Inventory Synchronization</h1>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Force Sync All Stock
        </button>
    </div>

    <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-lg flex gap-3 text-sm">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <strong>Important:</strong> Inventory sync automatically pushes stock updates from Deposusu to all connected marketplaces whenever a local stock movement occurs (Sales, Receive, Transfer).
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
            <h2 class="font-bold text-slate-800">Real-Time Sync Logs</h2>
            <div class="flex gap-2 text-sm">
                <select class="border border-slate-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none">
                    <option value="">All Marketplaces</option>
                    <option value="shopee">Shopee</option>
                    <option value="tokopedia">Tokopedia</option>
                </select>
            </div>
        </div>

        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600 font-medium border-b border-slate-200">
                <tr>
                    <th class="py-3 px-4">Time</th>
                    <th class="py-3 px-4">Product</th>
                    <th class="py-3 px-4">Marketplace</th>
                    <th class="py-3 px-4 text-center">Before</th>
                    <th class="py-3 px-4 text-center">After (Pushed)</th>
                    <th class="py-3 px-4">Trigger</th>
                    <th class="py-3 px-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs ?? [] as $log)
                <tr class="hover:bg-slate-50">
                    <td class="py-3 px-4 text-slate-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td class="py-3 px-4 font-medium text-slate-800">{{ $log->product->name ?? 'Unknown Product' }}</td>
                    <td class="py-3 px-4 capitalize">{{ $log->store->platform_name ?? 'Unknown' }}</td>
                    <td class="py-3 px-4 text-center text-slate-500">{{ $log->stock_before }}</td>
                    <td class="py-3 px-4 text-center font-bold {{ $log->stock_after == 0 ? 'text-red-600' : 'text-slate-800' }}">
                        {{ $log->stock_after }}
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-600 border border-slate-200 capitalize">
                            {{ $log->sync_type }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        @if($log->status == 'success')
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-green-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Success
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Failed
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center text-slate-500">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        <p>No inventory sync activities recorded yet.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if(isset($logs) && $logs->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
