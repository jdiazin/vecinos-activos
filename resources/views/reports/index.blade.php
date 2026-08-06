<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Reportes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Mensajes de éxito o error --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            @php
                $userRole = strtolower(trim(auth()->user()->role ?? ''));
                // Solo administradores y voceros pueden gestionar/resolver
                $puedeGestionar = in_array($userRole, ['admin', 'vocero']);
            @endphp

            {{-- Formulario para crear reporte (Oculto para el Auditor) --}}
            @if($userRole !== 'auditor')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8 p-6 border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">¿Encontraste un problema en el barrio?</h3>
                    
                    <form action="{{ route('reports.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="issue_type" class="block text-sm font-semibold text-slate-700 mb-1">Tipo de problema:</label>
                            <select id="issue_type" name="issue_type" class="w-full p-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-800 focus:border-slate-800 outline-none transition" required>
                                <option value="">Selecciona una opción</option>
                                <option value="alumbrado">Alumbrado público</option>
                                <option value="basura">Recolección de basura</option>
                                <option value="baches">Baches en calles</option>
                                <option value="parques">Mantenimiento de parques</option>
                                <option value="seguridad">Problemas de seguridad</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="location" class="block text-sm font-semibold text-slate-700 mb-1">Ubicación:</label>
                            <input type="text" id="location" name="location" class="w-full p-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-800 focus:border-slate-800 outline-none transition" placeholder="Ej: Calle la cortada de Catia" required />
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">Descripción:</label>
                            <textarea id="description" name="description" rows="4" class="w-full p-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-800 focus:border-slate-800 outline-none transition" placeholder="Describe a detalle el problema..." required></textarea>
                        </div>

                        <button type="submit" class="w-full bg-slate-800 hover:bg-slate-950 text-white font-semibold py-2.5 px-4 rounded-lg shadow transition duration-200">
                            Enviar Reporte
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 text-blue-700">
                    <p class="font-bold">Modo Auditoría</p>
                    <p>Estás visualizando los reportes en modo de solo lectura. No tienes permisos para crear o resolver incidencias.</p>
                </div>
            @endif

            {{-- Listado de Reportes --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Listado de Reportes</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                @if($puedeGestionar)
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reports ?? [] as $report)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">{{ $report->issue_type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $report->location }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $report->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if(strtolower($report->status) === 'resuelto')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 capitalize">
                                                {{ $report->status }}
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 capitalize">
                                                {{ $report->status }}
                                            </span>
                                        @endif
                                    </td>
                                    @if($puedeGestionar)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if(strtolower($report->status) !== 'resuelto')
                                                <form action="{{ route('reports.resolve', $report->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-green-600 hover:text-green-900 font-semibold">Resolver</button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 text-xs italic">Completado</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $puedeGestionar ? 5 : 4 }}" class="px-6 py-4 text-center text-sm text-gray-500">No hay reportes registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>