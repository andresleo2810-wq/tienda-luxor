<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaLog;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditoriaLog::query()->orderBy('created_at', 'desc');
        
        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('usuario_nombre', 'LIKE', "%{$request->buscar}%")
                  ->orWhere('descripcion', 'LIKE', "%{$request->buscar}%");
            });
        }
        
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }
        
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }
        
        $logs = $query->paginate(20)->withQueryString();
        $modulos = AuditoriaLog::distinct()->pluck('modulo');
        
        return view('auditoria.index', compact('logs', 'modulos'));
    }
}