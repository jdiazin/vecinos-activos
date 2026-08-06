<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del Sitio - Vecinos Activos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-800 font-['Open_Sans']">

    <header class="bg-white shadow-sm sticky top-0 z-50">
      <div class="max-w-6xl mx-auto px-4">
        <nav class="flex items-center justify-between h-16">
          <div class="flex items-center gap-2">
            <i class="fas fa-sliders-h text-xl"></i>
            <h1 class="text-lg font-bold text-slate-900">Configuración de Módulos</h1>
          </div>
          <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
            &larr; Volver al Panel
          </a>
        </nav>
      </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-12">
        <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Visibilidad de Secciones en la Página Principal</h2>
            <p class="text-slate-600 mb-8 text-sm">Controla qué características están habilitadas para los vecinos en tiempo real.</p>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <div class="space-y-6">
                <!-- Configurar Votaciones -->
                <div class="flex items-center justify-between p-5 bg-slate-50 rounded-xl border border-slate-200">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Módulo de Votación ("Votar Ahora")</h3>
                        <p class="text-sm text-slate-500">Muestra u oculta el botón en el menú y el acceso a votaciones.</p>
                    </div>
                    <form action="{{ route('admin.settings.toggle', 'votar_activo') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition shadow-sm {{ $votarActivo ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-200 hover:bg-slate-300 text-slate-700' }}">
                            {{ $votarActivo ? 'Activado' : 'Desactivado' }}
                        </button>
                    </form>
                </div>

                <!-- Configurar Postulaciones -->
                <div class="flex items-center justify-between p-5 bg-slate-50 rounded-xl border border-slate-200">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Módulo de Postulación a Vocería</h3>
                        <p class="text-sm text-slate-500">Muestra u oculta la sección para postularse en la página principal.</p>
                    </div>
                    <form action="{{ route('admin.settings.toggle', 'postular_activo') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition shadow-sm {{ $postularActivo ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-200 hover:bg-slate-300 text-slate-700' }}">
                            {{ $postularActivo ? 'Activado' : 'Desactivado' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

</body>
</html>