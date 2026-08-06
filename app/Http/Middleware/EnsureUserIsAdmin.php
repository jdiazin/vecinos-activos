<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Intercepta la petición y valida el rol del vecino.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está logueado y su rol es estrictamente 'admin', lo dejamos pasar
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Si es un usuario estándar, le bloqueamos el paso con un error 403 (Prohibido)
        abort(403, 'No tienes permisos de administrador para gestionar los usuarios de la comunidad.');
    }
}
