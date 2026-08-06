<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use App\Helpers\AuditLogger;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
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