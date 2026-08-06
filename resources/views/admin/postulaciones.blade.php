<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-200">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Panel de Control: Postulaciones</h3>
                        <p class="text-sm text-slate-500">Listado de vecinos postulados a las vocerías comunitarias.</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-200 transition">
    ← Volver al Panel
</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">Nombre</th>
                                <th class="px-4 py-3 text-left">Vocería</th>
                                <th class="px-4 py-3 text-left">Propuesta</th>
                                <th class="px-4 py-3 text-center">Total Votos</th>
                                <th class="px-4 py-3 text-left">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($postulaciones as $postulacion)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $postulacion->nombre }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $postulacion->voceria }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $postulacion->propuesta }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-3 py-1 bg-blue-50 text-blue-700 font-bold rounded-full text-xs">
                                            {{ $postulacion->votos_count }} votos
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $postulacion->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-400 italic">No hay postulaciones registradas todavía.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>