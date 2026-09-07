<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DEPOSUSU')</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/images/logo-512.png" type="image/png">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-brand-50:  #eff6ff;
            --color-brand-100: #dbeafe;
            --color-brand-500: #3b82f6;
            --color-brand-600: #2563eb;
            --color-brand-700: #1d4ed8;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900 min-h-screen pb-20">

    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-2xl mx-auto px-4 h-14 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Deposusu" class="w-8 h-8 rounded-lg object-cover">
                <span class="font-bold text-brand-600">{{ $opsRoleLabel ?? 'DEPOSUSU' }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-500 hidden sm:inline">{{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-rose-500 hover:text-rose-600">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-5">
        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    @php
        $opsIconPaths = [
            'home' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
            'archive' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
            'truck' => 'M8.25 18.75a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm8.25 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm-12-3H4.5m0-13.5h9v13.5m-9 0h9m0 0h3.75m0 0V9.348c0-.334-.148-.65-.405-.864L18.564 4.86A1.125 1.125 0 0017.797 4.5h-2.297m0 13.5V4.5',
            'cash' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z',
        ];
    @endphp

    @if(isset($opsNavItems))
        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-40">
            <div class="max-w-2xl mx-auto grid grid-cols-{{ count($opsNavItems) }}">
                @foreach($opsNavItems as $item)
                    <a href="{{ $item['url'] }}" class="flex flex-col items-center justify-center py-2.5 gap-1 {{ $item['active'] ? 'text-brand-600' : 'text-slate-400' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $item['active'] ? 2 : 1.5 }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $opsIconPaths[$item['icon']] ?? $opsIconPaths['home'] }}" />
                        </svg>
                        <span class="text-[11px] font-semibold">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </nav>
    @endif

    @stack('scripts')
</body>
</html>
