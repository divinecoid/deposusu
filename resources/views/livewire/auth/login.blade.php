<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Masuk ke Akun Anda')" :description="__('Masukkan email dan kata sandi Anda untuk masuk')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        @if(app()->environment(['local', 'staging']))
            @php
                // Verified demo accounts (see database/seeders/*.php). Only accounts
                // that actually work on THIS web login are listed here — the driver,
                // preparist and cashier mobile apps have their own login screens.
                $demoAccounts = [
                    ['label' => 'Admin', 'email' => 'admin@deposusu.com', 'password' => 'password123', 'note' => 'Masuk ke dashboard admin'],
                    ['label' => 'Customer', 'email' => 'customer@deposusu.com', 'password' => 'password123', 'note' => 'Masuk sebagai pelanggan'],
                    ['label' => 'Kasir', 'email' => 'kasir@deposusu.com', 'password' => 'password', 'note' => 'Akun kasir (login web ini akan masuk sebagai pelanggan)'],
                    ['label' => 'Gudang/Preparist', 'email' => 'preparist@deposusu.com', 'password' => 'password123', 'note' => 'Akun gudang (login web ini akan masuk sebagai pelanggan)'],
                    ['label' => 'Driver', 'email' => 'driver@deposusu.com', 'password' => 'password123', 'note' => 'Akun kurir (login web ini akan masuk sebagai pelanggan)'],
                ];
            @endphp
            <div class="rounded-2xl border border-blue-100 bg-blue-50/60 p-4">
                <p class="text-xs font-bold text-blue-700 uppercase tracking-wide mb-3">Akun Demo</p>
                <div class="space-y-2">
                    @foreach($demoAccounts as $account)
                        <div class="flex items-center justify-between gap-3 bg-white rounded-xl border border-blue-100 px-3 py-2">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-gray-800">{{ $account['label'] }}</p>
                                <p class="text-xs text-gray-500 truncate">
                                    <span class="font-mono">{{ $account['email'] }}</span>
                                    <span class="text-gray-300 mx-1">/</span>
                                    <span class="font-mono">{{ $account['password'] }}</span>
                                </p>
                            </div>
                            <button type="button"
                                onclick="fillDemoLogin('{{ $account['email'] }}', '{{ $account['password'] }}')"
                                class="shrink-0 px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-200 text-blue-600 text-xs font-bold hover:bg-blue-100 transition-colors">
                                Pakai
                            </button>
                        </div>
                    @endforeach
                </div>
                <p class="text-[11px] text-gray-400 mt-3 leading-relaxed">
                    Catatan: hanya <strong>Admin</strong> yang diarahkan ke dashboard admin. Peran lain (Kasir, Gudang, Driver) punya aplikasi masing-masing dan lewat login web ini akan masuk sebagai halaman pelanggan.
                </p>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input name="email" :label="__('Alamat Email')" :value="old('email')" type="email" required autofocus
                autocomplete="email" placeholder="email@contoh.com" class="!bg-white" />

            <!-- Password -->
            <div class="relative">
                <flux:input name="password" :label="__('Kata Sandi')" type="password" required
                    autocomplete="current-password" :placeholder="__('Kata Sandi')" viewable class="!bg-white" />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-xs end-0 text-blue-500 hover:text-blue-600 font-semibold"
                        :href="route('password.request')" wire:navigate>
                        {{ __('Lupa kata sandi?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Ingat saya')" :checked="old('remember')" class="text-gray-600" />

            <div class="flex items-center justify-end">
                <button type="submit"
                    class="w-full py-3 bg-blue-600 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 transform hover:scale-[1.02] active:scale-95 transition-all duration-300 text-base">
                    {{ __('Masuk Sekarang') }}
                </button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center text-gray-500">
                <span>{{ __('Belum punya akun?') }}</span>
                <flux:link :href="route('register')" class="text-blue-500 hover:text-blue-600 font-bold" wire:navigate>
                    {{ __('Daftar Sekarang') }}
                </flux:link>
            </div>
        @endif
    </div>

    @if(app()->environment(['local', 'staging']))
        <script>
            function fillDemoLogin(email, password) {
                const emailField = document.querySelector('input[name="email"]');
                const passwordField = document.querySelector('input[name="password"]');
                if (emailField) emailField.value = email;
                if (passwordField) passwordField.value = password;
            }
        </script>
    @endif
</x-layouts.auth>