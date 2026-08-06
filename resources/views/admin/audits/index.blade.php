<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Auditoría del Sistema') }}
            </h2>
            <a href="{{ route('admin.audits.export') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                📥 Exportar CSV
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                
                <!-- Filtros de Búsqueda -->
                <form method="GET" action="{{ route('admin.audits.index') }}" class="mb-6 flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por usuario, evento, descripción o IP..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="w-full sm:w-48">
                        <select name="component" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                            <option value="">Todos los componentes</option>
                            @foreach($components as $comp)
                                <option value="{{ $comp }}" {{ request('component') == $comp ? 'selected' : '' }}>{{ $comp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-indigo-500">Filtrar</button>
                        <a href="{{ route('admin.audits.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-400">Limpiar</a>
                    </div>
                </form>

                <!-- Tabla de Logs -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider text-left">
                            <tr>
                                <th class="px-4 py-3">Fecha / Hora</th>
                                <th class="px-4 py-3">Usuario</th>
                                <th class="px-4 py-3">Componente</th>
                                <th class="px-4 py-3">Evento</th>
                                <th class="px-4 py-3">Descripción</th>
                                <th class="px-4 py-3">IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm">
                            @forelse($logs as $log)
                                @php
                                    $userName = $log->user_name ?? 'Sistema';
                                    $eventoUpper = strtoupper(trim($log->event_name));
                                    
                                    // 1. Traducir eventos técnicos a verbos naturales
                                    $eventoAmigable = match(true) {
                                        str_contains($eventoUpper, 'POST')   => 'Creó',
                                        str_contains($eventoUpper, 'PATCH')  => 'Actualizó',
                                        str_contains($eventoUpper, 'PUT')    => 'Actualizó',
                                        str_contains($eventoUpper, 'DELETE') => 'Eliminó',
                                        default                              => 'Consultó'
                                    };

                                    // Colores dinámicos para los eventos
                                    $badgeColor = match($eventoAmigable) {
                                        'Creó'      => 'bg-green-100 text-green-800',
                                        'Actualizó' => 'bg-blue-100 text-blue-800',
                                        'Eliminó'   => 'bg-red-100 text-red-800',
                                        default     => 'bg-gray-100 text-gray-800'
                                    };

                                    // 2. Traducir descripciones técnicas y rutas a oraciones claras
                                    $descOriginal = $log->description;
                                    $descripcionAmigable = $descOriginal;

                                    if (str_contains($descOriginal, '/reports/resolve')) {
                                        $descripcionAmigable = "El usuario {$userName} marcó un reporte como resuelto.";
                                    } elseif (str_contains($descOriginal, '/reports')) {
                                        $descripcionAmigable = "El usuario {$userName} envió o registró un nuevo reporte.";
                                    } elseif (str_contains($descOriginal, '/admin/usuarios')) {
                                        $descripcionAmigable = "El usuario {$userName} modificó los permisos o accesos de un usuario.";
                                    } elseif (str_contains($descOriginal, '/mi-censo')) {
                                        $descripcionAmigable = "El usuario {$userName} actualizó su censo familiar.";
                                    }
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900">{{ $userName }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $log->component }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                                            {{ $eventoAmigable }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $descripcionAmigable }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-500 text-xs">{{ $log->ip_address }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">No se encontraron registros de auditoría.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-4">
                    {{ $logs->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>