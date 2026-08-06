<nav x-data="{ open: false }" class="bg-white border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo Unificado -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 text-slate-900">
                        <svg class="h-9 w-9 text-slate-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <span class="font-bold text-xl tracking-tight hidden sm:block">Vecinos Activos</span>
                    </a>
                </div>

                <!-- Navegación interna -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Mi Panel</x-nav-link>
                    
            </div>

            <!-- Menú de usuario -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-slate-600 bg-white hover:text-slate-900 transition">
                            <div>@auth {{ Auth::user()->name }} @else Invitado @endauth</div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        @auth
                            <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Salir</x-dropdown-link>
                            </form>
                        @else
                            <x-dropdown-link :href="route('login')">Iniciar Sesión</x-dropdown-link>
                        @endauth
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>