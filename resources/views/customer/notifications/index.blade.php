@extends('layouts.customer')

@section('title', 'Notifikasi - DEPOSUSU')

@section('content')
<div class="min-h-screen bg-slate-50 py-6 md:py-10">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Notifikasi</h1>
                <p class="text-sm text-slate-500 mt-1">Riwayat notifikasi pesanan dan pembayaran Anda.</p>
            </div>
            @if($notifications->where('read_at', null)->count() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center h-10 px-4 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:border-brand-500 hover:text-brand-600 transition-colors">
                        Tandai semua sudah dibaca
                    </button>
                </form>
            @endif
        </div>

        @if($notifications->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200/80 p-10 md:p-16 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-900 mb-2">Belum ada notifikasi</h2>
                <p class="text-slate-500 max-w-sm mx-auto text-sm">Kabar tentang pesanan dan pembayaran Anda akan muncul di sini.</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($notifications as $notification)
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 flex items-start justify-between gap-3 {{ $notification->read_at ? '' : 'border-brand-200 bg-brand-50/40' }}">
                        <div>
                            <p class="font-bold text-slate-900 text-sm">{{ $notification->data['title'] ?? 'Notifikasi' }}</p>
                            <p class="text-sm text-slate-600 mt-0.5">{{ $notification->data['body'] ?? '' }}</p>
                            <p class="text-xs text-slate-400 mt-1.5">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @if(!$notification->read_at)
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="shrink-0 text-[11px] font-bold text-brand-600 hover:text-brand-700">Tandai dibaca</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
