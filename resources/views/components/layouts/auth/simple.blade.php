<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" translate="no" class="notranslate">

<head>
    @include('partials.head')
    <script>
        // This auth flow (login/register/forgot-password/2FA) has no dark-mode
        // styling of its own. Flux's Alpine runtime re-applies the `dark`
        // class asynchronously (on init, on livewire:navigated, and whenever
        // the OS color-scheme changes) based on the visitor's system
        // preference, so a single one-off `classList.remove` here is not
        // enough — it gets overwritten once Alpine boots. A MutationObserver
        // keeps stripping it for the lifetime of the page instead, so Flux's
        // dark: input/label classes never kick in and wash out text against
        // this page's light gradient background.
        (function () {
            const root = document.documentElement;
            const stripDark = () => root.classList.contains('dark') && root.classList.remove('dark');
            stripDark();
            new MutationObserver(stripDark).observe(root, { attributes: true, attributeFilter: ['class'] });
        })();
    </script>
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 antialiased">
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-md flex-col gap-6">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium mb-4" wire:navigate>
                <span class="text-3xl font-extrabold text-blue-600 tracking-tight">DEPOSUSU</span>
            </a>

            <div
                class="glass-effect rounded-3xl shadow-xl shadow-blue-500/10 border border-white/50 p-8 md:p-10 animate-fade-in-up">
                {{ $slot }}
            </div>

            <div class="text-center">
                <p class="text-xs text-gray-400">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>