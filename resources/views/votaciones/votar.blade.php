<x-app-layout>
    <div class="max-w-4xl mx-auto py-12 px-4">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Votación para Vocerías</h2>

        @if(session('success'))
            <div class="bg-emerald-100 text-emerald-700 p-4 rounded-lg mb-6">{{ session('success') }}</div>
        @endif

        <form action="{{ route('votar.store') }}" method="POST">
            @csrf
            @foreach($postuladosPorVoceria as $voceria => $postulados)
                
                @php
                    // Convertimos a slug para comparar con lo que hay en la base de datos
                    $slugActual = \Illuminate\Support\Str::slug($voceria);
                    $votoExistente = \App\Models\Voto::where('user_id', auth()->id())
                                                    ->where('voceria_name', $slugActual)
                                                    ->first();
                @endphp

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-8">
                    <h3 class="text-lg font-bold text-slate-900 mb-4 {{ $votoExistente ? 'text-gray-500' : '' }}">
                        {{ $voceria }} 
                        @if($votoExistente) 
                            <span class="text-xs bg-slate-200 px-2 py-1 rounded text-slate-600">Bloqueado</span> 
                        @endif
                    </h3>
                    
                    <div class="space-y-3">
                        @foreach($postulados as $postulado)
                            <label class="flex items-center p-4 border rounded-lg transition 
                                {{ $votoExistente ? 'bg-slate-50 border-slate-100 cursor-not-allowed' : 'border-slate-200 hover:bg-slate-50 cursor-pointer' }}">
                                
                                <input type="radio" 
                                       name="voceria_{{ $voceria }}" 
                                       value="{{ $postulado->id }}" 
                                       class="mr-3"
                                       {{ $votoExistente ? 'disabled' : '' }}
                                       {{ ($votoExistente && $votoExistente->postulado_id == $postulado->id) ? 'checked' : '' }}>
                                
                                <span class="font-medium {{ $votoExistente ? 'text-gray-400' : 'text-slate-700' }}">
                                    {{ $postulado->nombre }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <button type="submit" class="w-full bg-slate-900 text-white py-3 rounded-lg font-bold hover:bg-slate-800 transition">
                Enviar mis votos
            </button>
        </form>
    </div>
</x-app-layout>