<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    /**
     * Verifica que el usuario tenga el rol permitido.
     * Uso: ->middleware('rol:Administrador') o ('rol:Administrador,Cajero')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $rolUsuario = auth()->user()->rol->nombre_rol;
        
        if (!in_array($rolUsuario, $roles)) {
            return redirect()->route('dashboard')
                ->with('error', ' No tienes permisos para acceder a esta sección');
        }
        
        return $next($request);
    }
}