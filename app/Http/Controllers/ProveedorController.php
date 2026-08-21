<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use App\Models\AuditoriaLog;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::where('estado', true);;
        
        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'LIKE', "%{$request->buscar}%")
                  ->orWhere('nit', 'LIKE', "%{$request->buscar}%")
                  ->orWhere('contacto', 'LIKE', "%{$request->buscar}%");
            });
        }
        
        $proveedores = $query->orderBy('nombre')->paginate(10)->withQueryString();
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'nit' => 'nullable|string|max:20|unique:proveedores,nit',
            'contacto' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:200'
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio',
            'nit.unique' => 'Este NIT ya está registrado',
            'email.email' => 'El email no es válido'
        ]);
        
        $validated['estado'] = $request->has('estado') && $request->estado == 1;
        
        Proveedor::create($validated);
        AuditoriaLog::registrar('Crear', 'Proveedores', "Proveedor: {$validated['nombre']}");
        
        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado exitosamente');
    }

    public function edit($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'nit' => 'nullable|string|max:20|unique:proveedores,nit,' . $proveedor->id,
            'contacto' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:200'
        ]);
        
        $validated['estado'] = $request->has('estado') && $request->estado == 1;
        
        $proveedor->update($validated);
        AuditoriaLog::registrar('Editar', 'Proveedores', "Proveedor: {$proveedor->nombre}");
        
        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente');
    }

    public function destroy($id)
{
    $proveedor = Proveedor::findOrFail($id);
    
    // Soft delete: cambiar estado a inactivo
    $proveedor->update(['estado' => false]);
    AuditoriaLog::registrar('Eliminar', 'Proveedores', "Proveedor: {$proveedor->nombre}");
    
    return redirect()->route('proveedores.index')
        ->with('success', 'Proveedor eliminado exitosamente');
}
}