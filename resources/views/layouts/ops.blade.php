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

    @if(isset($opsNavItems))
        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-40">
            <div class="max-w-2xl mx-auto grid grid-cols-{{ count($opsNavItems) }}">
                @foreach($opsNavItems as $item)
                    <a href="{{ $item['url'] }}" class="flex flex-col items-center justify-center py-2.5 gap-0.5 {{ $item['active'] ? 'text-brand-600' : 'text-slate-400' }}">
                        <span class="text-lg">{{ $item['icon'] }}</span>
                        <span class="text-[11px] font-semibold">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </nav>
    @endif

    @stack('scripts')
</body>
</html>
