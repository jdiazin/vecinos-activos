<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;
use App\Helpers\AuditLogger;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Forzar HTTPS en producción para que carguen bien los assets (Vite)
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
            
            // Ejecutar migraciones automáticamente al arrancar en producción (Render)
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Exception $e) {
                // Previene que falle el arranque si la base de datos no está lista de inmediato
            }
        }

        Event::listen(Login::class, function ($event) {
            AuditLogger::log(
                'Inicio de sesión', 
                "El usuario {$event->user->name} ha iniciado sesión.", 
                'Autenticación', 
                'Login'
            );
        });

        Event::listen(Logout::class, function ($event) {
            if ($event->user) {
                AuditLogger::log(
                    'Cierre de sesión', 
                    "El usuario {$event->user->name} ha cerrado sesión.", 
                    'Autenticación', 
                    'Logout'
                );
            }
        });
    }
}