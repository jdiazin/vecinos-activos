<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @php
                $role = strtolower(trim(Auth::user()->role ?? ''));
            @endphp

            <h1 class="text-2xl font-bold text-slate-800 mb-8">
                {{ $role === 'admin' ? 'Panel de Control Administrativo' : 'Panel de Auditoría y Consulta' }}
            </h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Gestión de Eventos (Visible para ambos) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-900 mb-2">Eventos</h3>
                    <p class="text-sm text-slate-500 mb-4">Ver avisos comunitarios.</p>
                    <a href="{{ route('eventos.index') }}" class="text-blue-600 font-medium text-sm hover:underline">Ver eventos →</a>
                </div>

                <!-- Ver Postulaciones -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-900 mb-2">Postulaciones</h3>
                    <p class="text-sm text-slate-500 mb-4">Ver candidatos a vocerías.</p>
                    <a href="{{ route('admin.postulaciones') }}" class="text-blue-600 font-medium text-sm hover:underline">Ver lista →</a>
                </div>

                <!-- Resultados de Votación -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-900 mb-2">Resultados</h3>
                    <p class="text-sm text-slate-500 mb-4">Consultar resultados electorales.</p>
                    <a href="{{ route('admin.resultados') }}" class="text-blue-600 font-medium text-sm hover:underline">Ver resultados →</a>
                </div>

                <!-- Gestionar Usuarios (EXCLUSIVO ADMIN) -->
                @if($role === 'admin')
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-900 mb-2">Usuarios</h3>
                    <p class="text-sm text-slate-500 mb-4">Roles y estados de vecinos.</p>
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600 font-medium text-sm hover:underline">Administrar →</a>
                </div>
                @endif

                <!-- Reportes -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-900 mb-2">Reportes</h3>
                    <p class="text-sm text-slate-500 mb-4">Revisar reportes de comunidad.</p>
                    <a href="{{ route('home') }}" class="text-blue-600 font-medium text-sm hover:underline">Ver pendientes →</a>
                </div>

                <!-- Censos Familiares -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition">
                    <h4 class="font-bold text-slate-900 text-lg mb-1">Censos Familiares</h4>
                    <p class="text-slate-500 text-sm mb-4">Revisar caracterización y datos socioeconómicos de los vecinos.</p>
                    <a href="{{ route('admin.gestion.censos') }}" class="inline-flex items-center text-sm text-blue-600 font-medium hover:underline">
                        Ver respuestas →
                    </a>
                </div>

                <!-- Configuración del Sitio (EXCLUSIVO ADMIN) -->
                @if($role === 'admin')
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Configuración del Sitio</h3>
                    <p class="text-sm text-slate-600 mb-4">Ocultar o mostrar módulos dinámicamente.</p>
                    <a href="{{ route('admin.settings') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold flex items-center gap-1">
                        Configurar &rarr;
                    </a>
                </div>
                @endif
                
                <!-- Auditoría del Sistema -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Auditoría del Sistema</h3>
                    <p class="text-sm text-slate-600 mb-4">Historial completo de accesos y eventos.</p>
                    <a href="{{ route('admin.audits.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold flex items-center gap-1">
                        Ver registros &rarr;
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>