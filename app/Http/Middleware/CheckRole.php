<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  ...$roles  // 👈 Documentar que $roles son integers
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = (int) Auth::user()->role; // Asegurar que es entero

        // Si no hay roles específicos, solo requiere autenticación
        if (empty($roles)) {
            return $next($request);
        }

        // Convertir roles a enteros
        $allowedRoles = array_map('intval', $roles);

        // Verificar permiso
        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Acceso denegado. No tienes el rol necesario.');
        }

        return $next($request);
    }
}
