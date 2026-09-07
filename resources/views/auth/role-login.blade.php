<x-layouts.auth.simple>
    <div class="flex flex-col gap-6">
        <div class="text-center">
            <h1 class="text-xl font-bold text-gray-900">Login {{ $meta['label'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Masuk ke akun {{ $meta['label'] }} Anda</p>
        </div>

        @if(session('status'))
            <div class="text-center text-sm text-emerald-600 font-medium">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
                {!! $errors->first() !!}
            </div>
        @endif

        <form method="POST" action="{{ route($roleKey . '.login.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input name="email" :label="__('Alamat Email')" :value="old('email')" type="email" required autofocus
                autocomplete="email" placeholder="email@contoh.com" class="!bg-white" />

            <flux:input name="password" :label="__('Kata Sandi')" type="password" required
                autocomplete="current-password" :placeholder="__('Kata Sandi')" viewable class="!bg-white" />

            <flux:checkbox name="remember" :label="__('Ingat saya')" class="text-gray-600" />

            <button type="submit"
                class="w-full py-3 bg-blue-600 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 transform hover:scale-[1.02] active:scale-95 transition-all duration-300 text-base">
                Masuk Sebagai {{ $meta['label'] }}
            </button>
        </form>

        <div class="text-center text-xs text-gray-400 space-y-1">
            <p>Bukan akun {{ $meta['label'] }}?</p>
            <p class="flex flex-wrap justify-center gap-x-3 gap-y-1">
                <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-600 font-semibold">Customer</a>
                @foreach(['admin' => 'Admin', 'kasir' => 'Kasir', 'driver' => 'Driver', 'preparist' => 'Preparist'] as $key => $label)
                    @if($key !== $roleKey)
                        <a href="{{ route($key . '.login') }}" class="text-blue-500 hover:text-blue-600 font-semibold">{{ $label }}</a>
                    @endif
                @endforeach
            </p>
        </div>
    </div>
</x-layouts.auth.simple>
