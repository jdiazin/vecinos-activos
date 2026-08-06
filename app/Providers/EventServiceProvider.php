<?php

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Helpers\AuditLogger;
use Illuminate\Support\Facades\Event;

// Dentro del método boot():
Event::listen(Login::class, function ($event) {
    AuditLogger::log(
        'Inicio de sesión',
        "El usuario {$event->user->name} ha iniciado sesión en el sistema.",
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