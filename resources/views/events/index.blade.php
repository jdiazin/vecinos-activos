<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Cartelera de Eventos Comunitarios') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(session('success'))
                <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-lg shadow-sm" role="alert">
                    <span class="block sm:inline font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @auth
                @if(in_array(Auth::user()->role, ['admin', 'vocero']))
                    <!-- Contenedor con paleta Slate -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
                        <div class="p-6 bg-slate-50/50 border-b border-slate-100">
                            <h3 class="text-lg font-bold text-slate-900 mb-4">
                                <i class="fas fa-calendar-plus mr-2"></i>Publicar Nuevo Evento Comunitario
                            </h3>
                            
                            <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Título del Evento:</label>
                                    <input type="text" name="title" class="w-full p-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900" required />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Lugar / Ubicación:</label>
                                    <input type="text" name="location" class="w-full p-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900" required />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Fecha y Hora:</label>
                                    <input type="datetime-local" name="event_date" class="w-full p-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900" required />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Imagen (Opcional):</label>
                                    <input type="file" name="image" accept="image/*" class="w-full p-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Descripción:</label>
                                    <textarea name="description" rows="3" class="w-full p-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900" required></textarea>
                                </div>
                                <div class="md:col-span-2 flex justify-end">
                                    <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-lg shadow-sm transition flex items-center gap-2">
                                        <i class="fas fa-bullhorn"></i> Publicar en Cartelera
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endauth

            <!-- Grid de eventos -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($events as $event)
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            @if($event->image_path)
                                <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-slate-100 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-calendar-alt text-5xl"></i>
                                </div>
                            @endif
                            
                            <div class="p-5">
                                <div class="flex justify-between items-start gap-4 mb-2">
                                    <h4 class="text-xl font-bold text-slate-900 leading-snug">{{ $event->title }}</h4>
                                    
                                    @auth
                                        @if(in_array(Auth::user()->role, ['admin', 'vocero']))
                                            <div class="flex items-center gap-2 shrink-0 z-10">
                                                <button type="button" onclick="abrirModalEditar({{ json_encode($event) }})" class="p-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg shadow-md transition" title="Editar Evento">
                                                    <i class="fas fa-edit text-sm"></i>
                                                </button>
                                                
                                                <form action="{{ route('admin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('¿De verdad quieres borrar este evento?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-md transition" title="Eliminar Evento">
                                                        <i class="fas fa-trash-alt text-sm"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                                <p class="text-sm text-slate-600 mt-2">{{ $event->description }}</p>
                            </div>
                        </div>

                        <div class="p-5 bg-slate-50 border-t border-slate-100 text-xs font-semibold text-slate-500 space-y-1.5">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar-day text-slate-500 w-4"></i>
                                <span>{{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y h:i A') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-slate-500 w-4"></i>
                                <span class="truncate">{{ $event->location }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 md:col-span-3 text-center py-12 bg-white rounded-xl border border-dashed border-slate-300 text-slate-400">
                        <i class="fas fa-calendar-times text-5xl mb-3"></i>
                        <p class="text-lg">No hay eventos comunitarios programados por el momento.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal Editar con paleta Slate -->
    <div id="modal-editar" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-xl overflow-hidden">
            <div class="p-6 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-edit"></i> Editar Evento
                </h3>
                <button onclick="cerrarModalEditar()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>
            
            <form id="form-editar-evento" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Título del Evento:</label>
                    <input type="text" id="edit-title" name="title" class="w-full p-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Lugar / Ubicación:</label>
                    <input type="text" id="edit-location" name="location" class="w-full p-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Fecha y Hora:</label>
                    <input type="datetime-local" id="edit-date" name="event_date" class="w-full p-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Imagen (Opcional):</label>
                    <input type="file" name="image" accept="image/*" class="w-full p-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-500" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Descripción:</label>
                    <textarea id="edit-description" name="description" rows="3" class="w-full p-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900" required></textarea>
                </div>
                
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="cerrarModalEditar()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition text-sm">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-lg shadow-sm transition text-sm">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalEditar(event) {
            document.getElementById('form-editar-evento').action = `/admin/eventos/${event.id}`;
            document.getElementById('edit-title').value = event.title;
            document.getElementById('edit-location').value = event.location;
            if(event.event_date) document.getElementById('edit-date').value = event.event_date.substring(0, 16);
            document.getElementById('edit-description').value = event.description;
            document.getElementById('modal-editar').classList.remove('hidden');
        }
        function cerrarModalEditar() {
            document.getElementById('modal-editar').classList.add('hidden');
        }
    </script>
</x-app-layout>