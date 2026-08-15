<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Módulo de Encuestas y Consultas Comunitarias') }}
            </h2>
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('vocero') || auth()->user()->role === 'admin' || auth()->user()->role === 'vocero')
                <a href="{{ route('encuestas.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Crear Nueva Encuesta
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                @forelse($surveys as $survey)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start">
                                <h3 class="text-lg font-bold text-gray-900">{{ $survey->title }}</h3>
                                <span class="px-2.5 py-0.5 text-xs rounded-full {{ $survey->status === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($survey->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">{{ $survey->description }}</p>
                            
                            <div class="text-xs text-gray-500 mt-4 space-y-1">
                                <p><strong>Creado por:</strong> {{ $survey->creator->name ?? 'Sistema' }}</p>
                                <p><strong>Cierre:</strong> {{ \Carbon\Carbon::parse($survey->end_date)->format('d/m/Y H:i') }}</p>
                            </div>

                            @php
                                $userVoted = $survey->votes()->where('user_id', auth()->id())->exists();
                            @endphp

                            @if(!$userVoted && $survey->status === 'activo' && now()->isBefore($survey->end_date))
                                <form action="{{ route('encuestas.vote', $survey->id) }}" method="POST" class="mt-4 space-y-3">
                                    @csrf
                                    <div class="space-y-2">
                                        @foreach($survey->options as $option)
                                            <label class="flex items-center space-x-3 text-sm text-gray-700">
                                                <input type="radio" name="option_id" value="{{ $option->id }}" class="text-indigo-600 focus:ring-indigo-500" required>
                                                <span>{{ $option->option_text }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <button type="submit" class="w-full mt-3 inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                        Emitir Voto
                                    </button>
                                </form>
                            @else
                                <div class="mt-4 p-3 bg-gray-50 rounded-md text-sm text-gray-600 text-center font-medium">
                                    @if($userVoted)
                                        <span class="text-indigo-600">✓ Ya has registrado tu voto en esta encuesta.</span>
                                    @else
                                        <span>Encuesta finalizada o cerrada.</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                            <a href="{{ route('encuestas.results', $survey->id) }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-semibold">
                                Ver Resultados y Estadísticas →
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                        No hay encuestas o consultas comunitarias registradas en este momento.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>