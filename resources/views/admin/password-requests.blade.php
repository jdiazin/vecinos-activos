<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Solicitudes de Recuperación de Credenciales') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200 p-6">
                <table class="w-full text-sm text-left text-slate-500">
                    <thead class="bg-slate-50 text-slate-700 uppercase text-xs border-b">
                        <tr>
                            <th class="px-4 py-3">Correo Solicitante</th>
                            <th class="px-4 py-3">Cédula</th>
                            <th class="px-4 py-3">Motivo</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-center">Acción / Asignar Nuevos Datos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $sol)
                            <tr class="border-b hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $sol->email }}</td>
                                <td class="px-4 py-3">{{ $sol->cedula ?? 'No indicada' }}</td>
                                <td class="px-4 py-3">{{ $sol->motivo }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $sol->status === 'pendiente' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ ucfirst($sol->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($sol->status === 'pendiente')
                                        <form action="{{ route('admin.password.requests.update', $sol->id) }}" method="POST" class="flex flex-col gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="email" name="new_email" placeholder="Nuevo correo" class="text-xs rounded border-slate-300" required>
                                            <input type="text" name="new_password" placeholder="Nueva contraseña temporal" class="text-xs rounded border-slate-300" required>
                                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-1.5 px-3 rounded shadow">
                                                Restablecer y Aprobar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium">Atendido</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-slate-400">No hay solicitudes pendientes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>