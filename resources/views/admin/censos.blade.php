<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Encabezado -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Control General de Censos Socioeconómicos</h2>
                    <p class="text-sm text-slate-500">Resumen analítico y registros de la comunidad de la Parroquia Sucre.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.censos.export') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition">
                        <i class="fas fa-file-excel mr-2"></i> Descargar Reporte General (CSV/Excel)
                    </a>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-200 transition">
    ← Volver al Panel
</a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TARJETAS DE INDICADORES RÁPIDOS (KPIs) -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-semibold">Total Hogares</p>
                        <h4 class="text-2xl font-bold text-slate-800">{{ $totalCensos ?? 0 }}</h4>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                        <i class="fas fa-home text-lg"></i>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-semibold">Población Total Registrada</p>
                        <h4 class="text-2xl font-bold text-slate-800">{{ $totalPoblacion ?? 0 }}</h4>
                    </div>
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">
                        <i class="fas fa-users text-lg"></i>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-semibold">Casos Embarazos</p>
                        <h4 class="text-2xl font-bold text-amber-600">{{ $vulnerabilidadEmbarazadas ?? 0 }}</h4>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-600 rounded-lg">
                        <i class="fas fa-child text-lg"></i>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-semibold">Casos Encamados</p>
                        <h4 class="text-2xl font-bold text-rose-600">{{ $vulnerabilidadEncamados ?? 0 }}</h4>
                    </div>
                    <div class="p-3 bg-rose-50 text-rose-600 rounded-lg">
                        <i class="fas fa-procedures text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECCIÓN DE GRÁFICOS ESTADÍSTICOS -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Tipos de Vivienda -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h4 class="font-bold text-slate-900 mb-4 text-sm uppercase tracking-wider">Tipos de Vivienda en la Comunidad</h4>
                    <div class="relative h-64 flex justify-center">
                        <canvas id="viviendaChart"></canvas>
                    </div>
                </div>

                <!-- Niveles de Ingreso Familiar -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h4 class="font-bold text-slate-900 mb-4 text-sm uppercase tracking-wider">Niveles de Ingreso Familiar</h4>
                    <div class="relative h-64">
                        <canvas id="ingresoChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Fila de Gráficos (Agua y CLAP) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Abastecimiento de Agua -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h4 class="font-bold text-slate-900 mb-4 text-sm uppercase tracking-wider">Método de Abastecimiento de Agua</h4>
                    <div class="relative h-64">
                        <canvas id="aguaChart"></canvas>
                    </div>
                </div>

                <!-- Beneficiarios CLAP -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h4 class="font-bold text-slate-900 mb-4 text-sm uppercase tracking-wider">Beneficiarios del Comité CLAP</h4>
                    <div class="relative h-64">
                        <canvas id="clapChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Fila de Gráficos de Infraestructura y Servicios -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Tipo de Paredes -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h4 class="font-bold text-slate-900 mb-4 text-sm uppercase tracking-wider">Material de Paredes</h4>
                    <div class="relative h-64">
                        <canvas id="paredesChart"></canvas>
                    </div>
                </div>

                <!-- Tipo de Techos -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h4 class="font-bold text-slate-900 mb-4 text-sm uppercase tracking-wider">Material de Techos</h4>
                    <div class="relative h-64">
                        <canvas id="techosChart"></canvas>
                    </div>
                </div>

                <!-- Servicio de Gas -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h4 class="font-bold text-slate-900 mb-4 text-sm uppercase tracking-wider">Tipo de Servicio de Gas</h4>
                    <div class="relative h-64">
                        <canvas id="gasChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- LISTADO DETALLADO DE VECINOS -->
            <!-- ========================================== -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Listado Detallado de Vecinos</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                                <th class="py-3 px-6">Vecino (Usuario)</th>
                                <th class="py-3 px-6">Cédula / Jefe</th>
                                <th class="py-3 px-6">Ubicación (Calle / Nro)</th>
                                <th class="py-3 px-6 text-center">Miembros</th>
                                <th class="py-3 px-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                            @forelse($censos as $census)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-900">{{ $census->user->name ?? 'N/D' }}</div>
                                        <div class="text-xs text-slate-400">{{ $census->user->email ?? 'N/D' }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-slate-800">{{ $census->jefe_nombre }}</div>
                                        <div class="text-xs text-slate-400">C.I: {{ $census->jefe_cedula }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        {{ $census->sector_calle }} (Nro: {{ $census->numero_vivienda_dir }})
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700">
                                            {{ $census->integrantes->count() }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <a href="{{ route('census.pdf', $census->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                                            Ver PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-slate-400 italic">
                                        No hay censos registrados en la comunidad todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts de Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. Tipos de Vivienda (Doughnut)
        const ctxVivienda = document.getElementById('viviendaChart').getContext('2d');
        new Chart(ctxVivienda, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($tiposVivienda->keys()) !!},
                datasets: [{
                    data: {!! json_encode($tiposVivienda->values()) !!},
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return ` ${label}: ${value} familias`;
                            }
                        }
                    }
                }
            }
        });

        // 2. Niveles de Ingreso Familiar (Bar)
        const ctxIngreso = document.getElementById('ingresoChart').getContext('2d');
        new Chart(ctxIngreso, {
            type: 'bar',
            data: {
                labels: {!! json_encode($ingresosFamiliar->keys()) !!},
                datasets: [{
                    label: 'Familias por Nivel de Ingreso',
                    data: {!! json_encode($ingresosFamiliar->values()) !!},
                    backgroundColor: '#3b82f6',
                    borderRadius: 4
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { autoSkip: false } }
                }
            }
        });

        // 3. Abastecimiento de Agua (Bar)
        const ctxAgua = document.getElementById('aguaChart').getContext('2d');
        new Chart(ctxAgua, {
            type: 'bar',
            data: {
                labels: {!! json_encode($abastecimientoAgua->keys() ?? []) !!},
                datasets: [{
                    label: 'Familias por Tipo de Abastecimiento',
                    data: {!! json_encode($abastecimientoAgua->values() ?? []) !!},
                    backgroundColor: '#0ea5e9',
                    borderRadius: 4
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { autoSkip: false } }
                }
            }
        });

        // 4. Beneficiarios CLAP (Doughnut)
        const ctxClap = document.getElementById('clapChart').getContext('2d');
        new Chart(ctxClap, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($recibeClap->keys() ?? []) !!},
                datasets: [{
                    data: {!! json_encode($recibeClap->values() ?? []) !!},
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#6366f1']
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return ` ${label}: ${value} familias`;
                            }
                        }
                    }
                }
            }
        });

        // 5. Material de Paredes (Bar)
        const ctxParedes = document.getElementById('paredesChart').getContext('2d');
        new Chart(ctxParedes, {
            type: 'bar',
            data: {
                labels: {!! json_encode($tipoParedes->keys() ?? []) !!},
                datasets: [{
                    label: 'Familias por Material de Paredes',
                    data: {!! json_encode($tipoParedes->values() ?? []) !!},
                    backgroundColor: '#8b5cf6',
                    borderRadius: 4
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { autoSkip: false } } 
                } 
            }
        });

        // 6. Material de Techos (Bar)
        const ctxTechos = document.getElementById('techosChart').getContext('2d');
        new Chart(ctxTechos, {
            type: 'bar',
            data: {
                labels: {!! json_encode($tipoTechos->keys() ?? []) !!},
                datasets: [{
                    label: 'Familias por Material de Techos',
                    data: {!! json_encode($tipoTechos->values() ?? []) !!},
                    backgroundColor: '#6366f1',
                    borderRadius: 4
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { autoSkip: false } } 
                } 
            }
        });

        // 7. Servicio de Gas (Doughnut)
        const ctxGas = document.getElementById('gasChart').getContext('2d');
        new Chart(ctxGas, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($servicioGas->keys() ?? []) !!},
                datasets: [{
                    data: {!! json_encode($servicioGas->values() ?? []) !!},
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444']
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return ` ${label}: ${value} familias`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>