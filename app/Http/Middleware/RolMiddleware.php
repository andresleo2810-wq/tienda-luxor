<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Obtenemos el nombre del rol y lo pasamos a minúsculas para comparar sin errores
        $userRole = strtolower($user->rol->nombre_rol ?? '');
        
        // Convertimos los roles permitidos a minúsculas
        $allowedRoles = array_map('strtolower', $roles);

        // Si el rol del usuario NO está en la lista de permitidos, denegamos el acceso
        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'No tienes permisos suficientes para acceder a esta sección.');
        }

        return $next($request);
    }
}