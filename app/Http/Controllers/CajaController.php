<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Venta;
use Illuminate\Http\Request;
use App\Models\AuditoriaLog;

class CajaController extends Controller
{
    public function index()
    {
        $cajas = Caja::with('usuario')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('cajas.index', compact('cajas'));
    }

    public function create()
    {
        $cajaAbierta = Caja::where('estado', 'Abierta')->first();
        return view('cajas.create', compact('cajaAbierta'));
    }

    /**
     * Abrir caja
     */
    public function store(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
            'notas' => 'nullable|string'
        ], [
            'monto_inicial.required' => 'Ingrese el monto inicial de la caja'
        ]);

        if (Caja::where('estado', 'Abierta')->exists()) {
            return back()->with('error', 'Ya hay una caja abierta. Debe cerrarla antes de abrir otra.');
        }

        Caja::create([
            'id_usuario' => auth()->id(),
            'monto_inicial' => $request->monto_inicial,
            'fecha_apertura' => now(),
            'estado' => 'Abierta',
            'notas' => $request->notas
        ]);
AuditoriaLog::registrar('Apertura', 'Caja', "Caja abierta con $" . number_format($request->monto_inicial, 0));
        return redirect()->route('cajas.index')
            ->with('success', 'Caja abierta exitosamente');
    }

    /**
     * Formulario de cierre con arqueo
     */
    public function cerrarForm($id)
    {
        $caja = Caja::findOrFail($id);

        if ($caja->estado !== 'Abierta') {
            return redirect()->route('cajas.index')
                ->with('error', 'Esta caja ya está cerrada');
        }

        // Ventas en efectivo desde la apertura (filtro en PHP por zona horaria)
        $apertura = $caja->fecha_apertura;
        $ventasEfectivo = Venta::where('metodo_pago', 'Efectivo')
            ->get()
            ->filter(fn($v) => $v->created_at->gte($apertura))
            ->sum('total_venta');

        $montoEsperado = $caja->monto_inicial + $ventasEfectivo;

        return view('cajas.cerrar', compact('caja', 'ventasEfectivo', 'montoEsperado'));
    }

    /**
     * Cerrar caja con arqueo
     */
    public function cerrar(Request $request, $id)
    {
        $caja = Caja::findOrFail($id);

        if ($caja->estado !== 'Abierta') {
            return redirect()->route('cajas.index')
                ->with('error', 'Esta caja ya está cerrada');
        }

        $request->validate([
            'monto_final_cierre' => 'required|numeric|min:0'
        ], [
            'monto_final_cierre.required' => 'Ingrese el dinero contado en caja'
        ]);

        // Calcular esperado
        $apertura = $caja->fecha_apertura;
        $ventasEfectivo = Venta::where('metodo_pago', 'Efectivo')
            ->get()
            ->filter(fn($v) => $v->created_at->gte($apertura))
            ->sum('total_venta');

        $montoEsperado = $caja->monto_inicial + $ventasEfectivo;
        $diferencia = $request->monto_final_cierre - $montoEsperado;

        $caja->update([
            'fecha_cierre' => now(),
            'monto_final_cierre' => $request->monto_final_cierre,
            'monto_esperado' => $montoEsperado,
            'diferencia' => $diferencia,
            'estado' => 'Cerrada'
        ]);
AuditoriaLog::registrar('Cierre', 'Caja', "Caja #{$caja->id} cerrada. Diferencia: $" . number_format($diferencia, 0));
        return redirect()->route('cajas.index')
            ->with('success', 'Caja cerrada exitosamente. Diferencia: $' . number_format($diferencia, 0));
    }
}