<x-guest-layout>
    <div class="mb-4 text-sm text-slate-600">
        {{ __('Si olvidaste tu contraseña o correo electrónico, completa los datos a continuación para enviar una solicitud formal a los administradores de la comunidad.') }}
    </div>

    <form method="POST" action="{{ route('password.request.store') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico (Actual o el último recordado)')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Cédula Opcional -->
        <div class="mt-4">
            <x-input-label for="cedula" :value="__('Cédula de Identidad (Opcional)')" />
            <x-text-input id="cedula" class="block mt-1 w-full" type="text" name="cedula" />
            <x-input-error :messages="$errors->get('cedula')" class="mt-2" />
        </div>

        <!-- Motivo / Detalles -->
        <div class="mt-4">
            <x-input-label for="motivo" :value="__('Detalla tu situación (¿Qué datos perdiste?)')" />
            <textarea id="motivo" name="motivo" class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" required></textarea>
            <x-input-error :messages="$errors->get('motivo')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-slate-600 hover:text-slate-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3" href="{{ route('login') }}">
                {{ __('Volver') }}
            </a>

            <x-primary-button>
                {{ __('Enviar Solicitud') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>