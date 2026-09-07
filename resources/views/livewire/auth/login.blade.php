<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Masuk ke Akun Anda')" :description="__('Masukkan email dan kata sandi Anda untuk masuk')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

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
</x-layouts.auth>