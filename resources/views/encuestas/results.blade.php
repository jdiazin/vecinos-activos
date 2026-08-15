<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Resultados de la Consulta: ') }} {{ $survey->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-6">
                    <p class="text-gray-600">{{ $survey->description }}</p>
                    <div class="text-xs text-gray-500 mt-2">
                        <span><strong>Estado:</strong> {{ ucfirst($survey->status) }}</span> | 
                        <span><strong>Total de Votos Emitidos:</strong> {{ $survey->options->sum(fn($opt) => $opt->votes->count()) }}</span>
                    </div>
                </div>

                @php
                    $totalVotes = $survey->options->sum(fn($opt) => $opt->votes->count());
                @endphp

                <div class="space-y-6">
                    @foreach($survey->options as $option)
                        @php
                            $voteCount = $option->votes->count();
                            $percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 2) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm font-medium text-gray-700 mb-1">
                                <span>{{ $option->option_text }}</span>
                                <span>{{ $voteCount }} votos ({{ $percentage }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-indigo-600 h-4 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 pt-4 border-t border-gray-100 flex justify-between items-center">
                    <a href="{{ route('encuestas.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-semibold">
                        ← Volver al Listado
                    </a>
                    <span class="text-xs text-gray-400">Panel de Auditoría y Control Comunitario</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>