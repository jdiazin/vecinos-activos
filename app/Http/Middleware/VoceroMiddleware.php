namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VoceroMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'vocero'])) {
            return $next($request);
        }

        abort(403, 'No autorizado.');
    }
}