<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vecinos Activos - Comunidad Unida</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet" />
  </head>
  <body class="bg-slate-50 text-slate-800 font-['Open_Sans']">

    @php
        // Obtenemos el estado de configuración de la base de datos
        $votarPermitido = \App\Models\Setting::where('key', 'votar_activo')->value('value') ?? true;
        $postularPermitido = \App\Models\Setting::where('key', 'postular_activo')->value('value') ?? true;
    @endphp
    
    <header class="bg-white shadow-sm sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center justify-between h-16">
          
          <!-- Logo y Título -->
          <div class="flex items-center gap-2.5 flex-shrink-0">
            <div class="bg-slate-900 text-white p-2 rounded-lg flex items-center justify-center">
              <i class="fas fa-home text-lg"></i>
            </div>
            <h1 class="text-lg font-bold text-slate-900 tracking-tight">Vecinos Activos</h1>
          </div>

          <!-- Enlaces de Navegación Compactos (Corregidos con gap-6) -->
          <ul class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
            <li><a href="{{ route('eventos.index') }}" class="hover:text-slate-900 transition">Comunicados</a></li>
            
            <!-- Votar Ahora (Condicionado por el Admin) -->
            @if($votarPermitido)
                <li>
                    <a href="{{ route('votar.index') }}" class="hover:text-slate-900 transition">
                        Votar Ahora
                    </a>
                </li>
            @endif
            
            <!-- Enlace añadido para el Censo Familiar -->
            <li>
                <a href="{{ route('census.index') }}" class="hover:text-slate-900 transition">
                    Censo Familiar
                </a>
            </li>

            @auth
                @if(Auth::user()->role === 'admin')
                    <li>
                        <a href="{{ route('admin.postulaciones') }}" class="hover:text-slate-900 transition">
                            Postulaciones
                        </a>
                    </li>
                @endif
            @endauth
            
            <li><a href="{{ route('reports.index') }}" class="hover:text-slate-900 transition">Reportes</a></li>
          </ul>
          
          <!-- Botones de Acción (Derecha) -->
          <div class="hidden md:flex items-center gap-2.5 flex-shrink-0">
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-1.5 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-950 rounded-md shadow-sm transition flex items-center gap-1.5">
                        <i class="fas fa-users-cog"></i> Gestionar Usuarios
                    </a>
                @endif

                <a href="{{ url('/dashboard') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-md transition flex items-center gap-1.5">
                    <i class="fas fa-user-circle"></i> Mi Panel
                </a>
            @else
                <a href="{{ route('register') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-md transition">Registrarse</a>
                <a href="{{ route('login') }}" class="px-3 py-1.5 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-950 rounded-md shadow-sm transition">Iniciar Sesión</a>
            @endauth
          </div>
        </nav>
      </div>
    </header>

    <!-- Tu GIF original de bienvenida -->
    <div class="max-w-6xl mx-auto px-4 mt-8">
        <div class="relative w-full h-80 overflow-hidden rounded-2xl shadow-lg bg-white flex items-center justify-center p-4">
            <img src="{{ asset('bienvenido.gif') }}" alt="Bienvenido a Vecinos Activos" class="w-full h-full object-contain rounded-xl">
        </div>
    </div>

    <!-- Sección de Próximos Eventos -->
    <div class="max-w-6xl mx-auto px-4 mt-6">
        <div class="relative w-full bg-white rounded-2xl shadow-lg p-6 border border-slate-100">
            <div class="flex items-center justify-between mb-4 border-b pb-2">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-slate-700"></i> Próximos Comunicados, Eventos y Anuncios de la Comunidad
                </h3>
                <a href="{{ route('eventos.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                    Ver todos &rarr;
                </a>
            </div>

            <!-- Contenedor con scroll horizontal -->
            <div class="flex overflow-x-auto gap-4 py-2 scrollbar-thin scrollbar-thumb-slate-200">
                @forelse($eventos as $evento)
                    <div class="min-w-[300px] max-w-[320px] flex-shrink-0 bg-slate-50 border border-slate-200 rounded-xl p-5 flex flex-col justify-between shadow-sm hover:shadow-md transition">
                        <div>
                            <span class="text-xs font-bold px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full uppercase inline-block mb-2">
                                {{ $evento->event_date }}
                            </span>
                            <h4 class="text-md font-bold text-slate-900 mb-1">{{ $evento->title }}</h4>
                            <p class="text-sm text-slate-600 line-clamp-2">{{ $evento->description }}</p>
                        </div>
                        <div class="text-right mt-4">
                            <a href="{{ route('eventos.index') }}" class="text-xs font-bold text-slate-800 hover:underline">
                                Ver detalles &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="w-full text-center py-8 text-slate-400">
                        <i class="fas fa-calendar-times text-4xl mb-2"></i>
                        <p class="text-sm">No hay comunicados, eventos o anuncios programados por ahora.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>


    <!-- Sección Reportes -->
    <section id="reportes" class="py-12 bg-white border-t border-b border-slate-100">
      <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-slate-900 mb-8 text-center">Reportar Problemas</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          
          <div class="bg-slate-50 p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-4">¿Encontraste un problema en el barrio?</h3>
            <form id="form-reporte" action="{{ route('reports.store') }}" method="POST">
              @csrf
              <div class="mb-4">
                <label for="issue-type" class="block text-sm font-semibold text-slate-700 mb-1">Tipo de problema:</label>
                <select id="issue-type" name="issue_type" class="w-full p-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-800 focus:border-slate-800 outline-none transition" required>
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

              @auth
                  <button type="submit" class="w-full bg-slate-800 hover:bg-slate-950 text-white font-semibold py-2.5 px-4 rounded-lg shadow transition duration-200">
                      Enviar Reporte
                  </button>
              @else
                  <div class="bg-slate-100 border border-slate-200 text-slate-700 p-3 rounded-lg text-sm text-center font-medium shadow-sm">
                      <i class="fas fa-lock mr-1.5"></i> Debes <a href="{{ route('login') }}" class="text-slate-900 underline font-bold">iniciar sesión</a> para reportar.
                  </div>
              @endauth
            </form>
          </div>

          <div class="flex flex-col">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Reportes Recientes</h3>
            <div id="lista-reportes" class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
                @forelse($reports as $report)
                    <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm transition hover:shadow-md">
                        <div class="flex justify-between items-start mb-2">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">
                                {{ $report->issue_type }}
                            </span>
                            <span class="text-xs text-slate-400">{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                        <h4 class="font-bold text-slate-900 mb-1">
                            <i class="fas fa-map-marker-alt text-slate-500 mr-1.5"></i>{{ $report->location }}
                        </h4>
                        <p class="text-sm text-slate-600 mb-3">{{ $report->description }}</p>
                        
                        <div class="w-full bg-slate-100 rounded-full h-4 relative overflow-hidden mb-3">
                            <div class="bg-blue-600 h-full flex items-center justify-center text-[10px] text-white font-bold">
                                {{ ucfirst($report->status) }}
                            </div>
                        </div>

                        @auth
                            @if(in_array(Auth::user()->role, ['admin', 'vocero']))
                                <form action="{{ route('reports.resolve', $report->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-950 text-white text-xs font-bold py-1.5 px-3 rounded transition">
                                        <i class="fas fa-check mr-1"></i> Marcar como Resuelto
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                @empty
                    <p class="text-slate-400 text-center py-8">No hay reportes activos en este momento.</p>
                @endforelse
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Sección Postulaciones (Condicionada por el Admin) -->
    @if($postularPermitido)
        <section id="postulaciones" class="py-12 bg-slate-50">
            <div class="max-w-6xl mx-auto px-4">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Postúlate a Vocería</h3>
                    
                    @auth
                        @php
                            $miPostulacion = \App\Models\Postulacion::where('user_id', auth()->id())->first();
                        @endphp

                        @if($miPostulacion)
                            <div class="bg-slate-50 border border-slate-200 p-6 rounded-xl text-center">
                                <span class="inline-block bg-slate-200 text-slate-700 text-xs px-3 py-1 rounded-full font-bold uppercase mb-2">
                                    Postulación Registrada
                                </span>
                                <h4 class="text-md font-bold text-slate-800">Ya estás participando en esta elección</h4>
                                <p class="text-slate-600 text-sm mt-1">
                                    Te has postulado para la vocería de: <strong class="text-slate-900 capitalize">{{ $miPostulacion->voceria }}</strong>
                                </p>
                            </div>
                        @else
                            <form action="{{ route('postulacion.store') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold mb-1">Área de Vocería:</label>
                                    <select name="voceria" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none" required>
                                        <option value="">Selecciona una vocería...</option>
                                        <option value="salud">Vocería de Salud</option>
                                        <option value="Economía Comunal">Vocería de Economía Comunal</option>
                                        <option value="servicios">Vocería de Servicios</option>
                                        <option value="Vivienda y Hábitat">Vocería de Vivienda y Hábitat</option>
                                    </select> 
                                </div>
                                <textarea name="propuesta" class="w-full p-2 border border-slate-300 rounded-lg mb-4 focus:ring-2 focus:ring-slate-800 outline-none" placeholder="Describe brevemente tu propuesta..." required></textarea>
                                <button type="submit" class="w-full bg-slate-800 text-white py-2 rounded-lg hover:bg-slate-950 transition">Enviar Postulación</button>
                            </form>
                        @endif

                    @else
                        <div class="text-center py-6 bg-slate-100 rounded-lg">
                            <p class="text-slate-600 mb-3">Debes iniciar sesión para postularte.</p>
                            <a href="{{ route('login') }}" class="text-slate-900 font-bold underline">Iniciar Sesión</a>
                        </div>
                    @endauth
                </div>
            </div>
        </section>
    @endif

  </body>
</html>