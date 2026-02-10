<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Buat Akun Baru')" :description="__('Masukkan detail Anda di bawah ini untuk membuat akun')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input name="name" :label="__('Nama Lengkap')" :value="old('name')" type="text" required autofocus
                autocomplete="name" :placeholder="__('Nama lengkap Anda')" class="!bg-white" />

            <!-- Email Address -->
            <flux:input name="email" :label="__('Alamat Email')" :value="old('email')" type="email" required
                autocomplete="email" placeholder="email@contoh.com" class="!bg-white" />

            <!-- Password -->
            <flux:input name="password" :label="__('Kata Sandi')" type="password" required autocomplete="new-password"
                :placeholder="__('Kata Sandi')" viewable class="!bg-white" />

            <!-- Confirm Password -->
            <flux:input name="password_confirmation" :label="__('Konfirmasi Kata Sandi')" type="password" required
                autocomplete="new-password" :placeholder="__('Konfirmasi Kata Sandi')" viewable class="!bg-white" />

            <div class="flex items-center justify-end">
                <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 transform hover:scale-[1.02] active:scale-95 transition-all duration-300 text-base">
                    {{ __('Daftar Sekarang') }}
                </button>
            </div>
        </form>

        <div class="space-x-1 text-center text-sm text-gray-500">
            <span>{{ __('Sudah punya akun?') }}</span>
            <flux:link :href="route('login')" class="text-blue-500 hover:text-blue-600 font-bold" wire:navigate>
                {{ __('Masuk di Sini') }}</flux:link>
        </div>
    </div>
</x-layouts.auth>