<x-guest-layout>
    <!-- Contenedor del formulario -->
    <div class="w-full">
        
        
        <div class="flex justify-center mb-6">
            <svg class="w-16 h-16 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
        </div>

        <div class="flex flex-col items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Vecinos Activos</h1>
            <p class="text-gray-500 mt-2">Bienvenido a tu plataforma comunitaria</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Correo Electrónico')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Contraseña')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Recordarme') }}</span>
                </label>
            </div>

            <!-- Botones de Acción Modificados -->
            <div class="flex items-center justify-between mt-6">
                <!-- Botón Volver con estilo azul clásico de tu página principal -->
                <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Volver') }}
                </a>

                <!-- Botón Ingresar -->
                <x-primary-button class="ms-3 bg-gray-800 hover:bg-gray-900">
                    {{ __('Ingresar') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>