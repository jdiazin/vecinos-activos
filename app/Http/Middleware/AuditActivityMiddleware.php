<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\AuditLogger;
use Illuminate\Support\Facades\Auth;

class AuditActivityMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Si el usuario está autenticado y realiza una acción de modificación (POST, PUT, PATCH, DELETE)
        if (Auth::check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $user = Auth::user();
            $path = $request->path();
            
            $component = 'Sistema General';
            if (str_contains($path, 'report')) $component = 'Reportes';
            if (str_contains($path, 'censo')) $component = 'Censos';
            if (str_contains($path, 'evento')) $component = 'Eventos';
            if (str_contains($path, 'usuario')) $component = 'Usuarios';
            if (str_contains($path, 'configuracion')) $component = 'Configuración';

            // Verificamos si hay una descripción personalizada en la sesión (como la de eliminación de usuarios)
            $defaultDescription = "El usuario {$user->name} ejecutó una acción en: /{$path}";
            $description = session('audit_description', $defaultDescription);

            AuditLogger::log(
                'Acción (' . $request->method() . ')',
                $description,
                $component,
                $request->method()
            );
        }

        return $response;
    }
}