@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-slate-800">Marketplace Connection</h1>
        <div class="text-sm text-slate-500">
            Terakhir sinkronisasi: <span class="font-medium text-slate-800">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($stores as $store)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center 
                        {{ $store->platform_name == 'shopee' ? 'bg-orange-100 text-orange-600' : 
                          ($store->platform_name == 'tokopedia' ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-black') }}">
                        @if($store->platform_name == 'shopee')
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M16.5,5.5h-9c-1.1,0-2,0.9-2,2v9c0,1.1,0.9,2,2,2h9c1.1,0,2-0.9,2-2v-9C18.5,6.4,17.6,5.5,16.5,5.5z M14,14h-4v-4h4V14z"/></svg>
                        @elseif($store->platform_name == 'tokopedia')
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12,2C6.48,2,2,6.48,2,12s4.48,10,10,10s10-4.48,10-10S17.52,2,12,2z M12,20c-4.41,0-8-3.59-8-8s3.59-8,8-8s8,3.59,8,8 S16.41,20,12,20z"/></svg>
                        @else
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 15.68a6.34 6.34 0 0012.67-1.39v-4.14a8.28 8.28 0 004.14 1.15V7.81a4.91 4.91 0 01-2.22-1.12z"/></svg>
                        @endif
                    </div>
                    @if($store->status == 'connected')
                        <span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full border border-green-200">Connected</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-medium bg-slate-100 text-slate-600 rounded-full border border-slate-200">Disconnected</span>
                    @endif
                </div>
                
                <h3 class="font-bold text-lg text-slate-800 capitalize mb-1">{{ $store->platform_name }}</h3>
                <p class="text-sm text-slate-500 mb-4">{{ $store->store_name }}</p>

                <div class="space-y-2 text-sm text-slate-600 mb-6">
                    <div class="flex justify-between">
                        <span>Last Sync:</span>
                        <span class="font-medium">{{ $store->last_sync ? $store->last_sync->diffForHumans() : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>API Status:</span>
                        <span class="font-medium {{ $store->status == 'connected' ? 'text-green-600' : 'text-slate-400' }}">
                            {{ $store->status == 'connected' ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                @if($store->status == 'connected')
                    <form action="{{ route('admin.marketplace.disconnect', $store->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2 px-4 border border-red-200 text-red-600 hover:bg-red-50 rounded-lg text-sm font-medium transition">
                            Disconnect
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.marketplace.connect', $store->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2 px-4 bg-slate-800 text-white hover:bg-slate-700 rounded-lg text-sm font-medium transition">
                            Connect Account
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
