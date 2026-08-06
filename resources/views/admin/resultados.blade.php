<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Resultados de las Votaciones
        </h2>
    </x-slot> 

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            
            <div class="flex justify-end mb-4">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-200 transition">
                    ← Volver al Panel
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if($resultados->isEmpty())
                        <div class="text-center py-10 text-gray-500">
                            No hay votos registrados actualmente.
                        </div>
                    @else
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-100 text-slate-700 uppercase text-sm leading-normal">
                                        <th class="py-3 px-6 rounded-tl-lg">Vocería</th>
                                        <th class="py-3 px-6">Postulado</th>
                                        <th class="py-3 px-6 rounded-tr-lg text-center">Total de Votos</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 text-sm font-light">
                                    @foreach($resultados as $resultado)
                                    <tr class="border-b border-gray-200 hover:bg-slate-50 transition">
                                        <td class="py-4 px-6 font-medium text-slate-900 capitalize">{{ $resultado->voceria_name }}</td>
                                        <td class="py-4 px-6">{{ $resultado->nombre }}</td>
                                        <td class="py-4 px-6 text-center">
                                            <span class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full font-bold">
                                                {{ $resultado->total_votos }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>