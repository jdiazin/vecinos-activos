<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4">
            
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-6">
                <h3 class="font-bold text-slate-900 mb-6 border-b border-slate-100 pb-2">Mi Historial de Reportes</h3>
                
                @forelse($misReportes as $reporte)
                    <div class="bg-white border border-slate-200 rounded-lg p-4 mb-4 hover:shadow-sm transition">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex gap-2">
                                <!-- Categoría -->
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-700 uppercase tracking-wider">
                                    {{ $reporte->issue_type }}
                                </span>
                                <!-- Estado -->
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full 
                                    {{ $reporte->status === 'solucionado' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ ucfirst($reporte->status) }}
                                </span>
                            </div>
                            <span class="text-xs text-slate-400">{{ $reporte->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <h4 class="font-bold text-slate-900 mb-1">{{ $reporte->title ?? 'Reporte' }}</h4>
                        <p class="text-slate-600 text-sm mb-2">{{ $reporte->description }}</p>
                        
                        <div class="flex items-center text-xs text-slate-500">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            {{ $reporte->location }}
                        </div>
                    </div>
                @empty
                    <p class="text-slate-400 italic text-center py-4">No has realizado reportes aún.</p>
                @endforelse
            </div>

            <!-- Sección de Censo Vecinal -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Estado de mi Censo Vecinal</h3>

                @if(session('success'))
                    <div class="bg-emerald-50 border-l-4 border-emerald-400 p-4 mb-4 text-emerald-700 text-sm font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                @if(isset($census))
                    <!-- Si ya llenó el censo, mostramos un resumen y las opciones -->
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                        <p class="text-green-700 font-semibold">¡Ya has completado tu registro de censo!</p>
                        <p class="text-sm text-green-600 mt-1">Ubicación: <strong>{{ $census->sector_calle ?? 'N/D' }}</strong> | Cédula Jefe: <strong>{{ $census->jefe_cedula ?? 'N/D' }}</strong></p>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('census.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Actualizar Mis Datos
                        </a>
                        
                        <!-- Descarga directa del PDF en pestaña nueva -->
                        <a href="{{ route('census.pdf', $census->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                            Descargar Mi PDF
                        </a>
                    </div>
                @else
                    <!-- Si todavía no lo ha llenado -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <p class="text-yellow-700 font-semibold">Aún no has registrado tu censo familiar.</p>
                        <p class="text-sm text-yellow-600 mt-1">Es importante para la caracterización de nuestra comunidad en "Vecinos Activos".</p>
                    </div>

                    <a href="{{ route('census.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        Completar Censo Ahora
                    </a>
                @endif
            </div>

        </div>
    </div>

    <!-- Script que abre el PDF automáticamente en una pestaña nueva al registrar o actualizar -->
    @if(session('pdf_id'))
        <script>
            window.open("{{ route('census.pdf', session('pdf_id')) }}", "_blank");
        </script>
    @endif
</x-app-layout>