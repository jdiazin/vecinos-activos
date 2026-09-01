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
                    <p>Estás visualizando los reportes en modo de solo lectura. No tienes permisos para crear o resolver incidencias, pero puedes auditar las evidencias de cierre.</p>
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
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evidencia / Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reports ?? [] as $report)
                                @php
                                    $statusLower = strtolower($report->status ?? 'pendiente');
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">{{ $report->issue_type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $report->location }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $report->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($statusLower === 'resuelto')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 capitalize">
                                                Resuelto
                                            </span>
                                        @elseif($statusLower === 'en_proceso')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-sky-100 text-sky-800 capitalize">
                                                En Proceso
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 capitalize">
                                                Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($statusLower !== 'resuelto')
                                            @if($puedeGestionar)
                                                <div class="flex items-center space-x-2">
                                                    {{-- Si está pendiente, permitimos marcarlo En Proceso --}}
                                                    @if($statusLower === 'pendiente')
                                                        <form action="{{ route('reports.markInProcess', $report->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="text-sky-700 hover:text-sky-900 font-semibold bg-sky-50 px-3 py-1 rounded-md text-xs transition">
                                                                Marcar En Proceso
                                                            </button>
                                                        </form>
                                                    @endif

                                                    {{-- Botón que activa el modal para solventar con evidencia obligatoria --}}
                                                    <button type="button" onclick="openResolveModal({{ $report->id }})" class="text-indigo-600 hover:text-indigo-900 font-semibold bg-indigo-50 px-3 py-1 rounded-md text-xs transition">
                                                        Resolver
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-xs italic">
                                                    {{ $statusLower === 'en_proceso' ? 'En proceso de atención' : 'Pendiente de revisión' }}
                                                </span>
                                            @endif
                                        @else
                                            {{-- Visible para Admin, Voceros y Auditores: Permite ver la prueba adjunta --}}
                                            @if($report->evidence_path)
                                                <div class="space-y-1">
                                                    <a href="{{ Storage::url($report->evidence_path) }}" target="_blank" class="text-blue-600 hover:underline text-xs font-semibold block">
                                                        📄 Ver Evidencia
                                                    </a>
                                                    @if($report->solution_notes)
                                                        <p class="text-xs text-gray-500 italic max-w-xs truncate" title="{{ $report->solution_notes }}">
                                                            Nota: {{ $report->solution_notes }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-xs">Sin evidencia</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No hay reportes registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal flotante para adjuntar la evidencia de solución (Obligatorio para Administradores y Voceros) --}}
    <div id="resolveModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md border border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 mb-2">Finalizar y Registrar Solución</h3>
            <p class="text-xs text-gray-500 mb-4">Es obligatorio adjuntar una evidencia (foto o documento) y una breve descripción de cómo se resolvió la incidencia para que quede constancia ante los auditores.</p>
            
            <form id="resolveForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Notas de la Solución:</label>
                    <textarea name="solution_notes" rows="3" required class="w-full p-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-800 focus:border-slate-800 outline-none transition text-sm" placeholder="Ej: Se reparó el cableado de la fase principal..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Evidencia (Imagen o PDF, Máx. 5MB):</label>
                    <input type="file" name="evidence" accept=".jpg,.jpeg,.png,.pdf" required class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-slate-800 file:text-white hover:file:bg-slate-950 cursor-pointer"/>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeResolveModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg text-sm transition">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg text-sm transition">Guardar y Resolver</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Scripts para controlar el modal --}}
    <script>
        function openResolveModal(reportId) {
            const modal = document.getElementById('resolveModal');
            const form = document.getElementById('resolveForm');
            // Asigna dinámicamente la ruta de la acción utilizando el ID del reporte
            form.action = `/reportes/${reportId}/resolver`;
            modal.classList.remove('hidden');
        }

        function closeResolveModal() {
            document.getElementById('resolveModal').classList.add('hidden');
        }
    </script>
</x-app-layout>