<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PostulacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VotacionController;
use App\Http\Controllers\CensusController;
use App\Http\Controllers\EncuestaController;
use App\Http\Controllers\Admin\AuditController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuditActivityMiddleware;

// --- Rutas Públicas ---
Route::get('/', [ReportController::class, 'index'])->name('home');
Route::get('/eventos', [EventController::class, 'index'])->name('eventos.index');
Route::get('/votar', [VotacionController::class, 'index'])->name('votar.index');
Route::post('/votar', [VotacionController::class, 'store'])->name('votar.store');

// --- Rutas Protegidas (Vecinos Estándar, Voceros y Auditores con Auditoría) ---
Route::middleware(['auth', AuditActivityMiddleware::class])->group(function () {
    Route::post('/reportes', [ReportController::class, 'store'])->name('reports.store');
    
    // Vista dedicada de gestión/listado de reportes
    Route::get('/reportes/gestion', [ReportController::class, 'gestionIndex'])->name('reports.index');

    // Ruta para resolver el reporte con evidencia (Accesible para Admin y Voceros)
    Route::patch('/reportes/{id}/resolver', [ReportController::class, 'resolve'])->name('reports.resolve');

    Route::get('/mi-censo', [CensusController::class, 'index'])->name('census.index');
    Route::post('/mi-censo', [CensusController::class, 'store'])->name('census.store');
    Route::get('/mi-censo/pdf/{id?}', [CensusController::class, 'generarPdf'])->name('census.pdf');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['verified'])
        ->name('dashboard');

    Route::post('/postulacion/store', [PostulacionController::class, 'store'])->name('postulacion.store');
    Route::get('/votaciones', [PostulacionController::class, 'indexVotaciones'])->name('votaciones');
    Route::post('/votar/{id}', [PostulacionController::class, 'votar'])->name('votar');
    
    // --- Módulo de Encuestas y Votaciones Digitales ---
    Route::get('/encuestas', [EncuestaController::class, 'index'])->name('encuestas.index');
    Route::get('/encuestas/create', [EncuestaController::class, 'create'])->name('encuestas.create');
    Route::post('/encuestas', [EncuestaController::class, 'store'])->name('encuestas.store');
    Route::post('/encuestas/{id}/vote', [EncuestaController::class, 'vote'])->name('encuestas.vote');
    Route::get('/encuestas/{id}/results', [EncuestaController::class, 'results'])->name('encuestas.results');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- Panel de Administración y Gestión ---
Route::middleware(['auth', AuditActivityMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // 1. Ruta Principal del Panel (Valida que sea Admin o Auditor)
        Route::get('/', function () {
            $role = strtolower(trim(auth()->user()->role ?? ''));
            if (!in_array($role, ['admin', 'auditor'])) {
                abort(403, 'No tienes autorización.');
            }
            return view('admin.dashboard');
        })->name('dashboard');

        // 2. Rutas Compartidas de Consulta (Accesibles para Admin y Auditor)
        Route::get('/audits', [AuditController::class, 'index'])->name('audits.index');
        Route::get('/audits/export', [AuditController::class, 'export'])->name('audits.export');

        Route::get('/censos', [AdminController::class, 'verCensos'])->name('censos');
        Route::get('/censos/excel', [AdminController::class, 'exportarCensosExcel'])->name('censos.excel');
        Route::get('/censos/exportar', [AdminController::class, 'exportarCensosExcel'])->name('censos.export');

        Route::get('/gestion/censos', [AdminController::class, 'verCensos'])->name('gestion.censos');
        Route::get('/gestion/censos/excel', [AdminController::class, 'exportarCensosExcel'])->name('gestion.censos.excel');

        Route::get('/postulaciones', [AdminController::class, 'verPostulaciones'])->name('postulaciones');
        Route::get('/resultados', [AdminController::class, 'resultadosVotacion'])->name('resultados');

        // 3. Rutas Exclusivas y Estrictas para Administradores
        Route::middleware('admin')->group(function () {
            Route::get('/usuarios', [AdminController::class, 'index'])->name('users.index');
            Route::patch('/usuarios/{user}/role', [AdminController::class, 'toggleRole'])->name('users.toggleRole');
            Route::patch('/usuarios/{user}/status', [AdminController::class, 'toggleStatus'])->name('users.toggleStatus');

            Route::get('/configuraciones', [AdminController::class, 'settingsIndex'])->name('settings');
            Route::patch('/configuraciones/{key}/toggle', [AdminController::class, 'toggleSetting'])->name('settings.toggle');
            
            Route::post('/eventos', [EventController::class, 'store'])->name('events.store');
            Route::put('/eventos/{event}', [EventController::class, 'update'])->name('events.update');
            Route::delete('/eventos/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        });
    });

require __DIR__.'/auth.php';