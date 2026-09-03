<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\AuditoriaLog;

class ProductoController extends Controller
{
    // Listar producto
     //
    public function index(Request $request)
    {
        $query = Producto::query();
        
        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre_producto', 'LIKE', "%{$request->buscar}%")
                  ->orWhere('marca', 'LIKE', "%{$request->buscar}%")
                  ->orWhere('codigo_barras', 'LIKE', "%{$request->buscar}%");
            });
        }
        
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        
        if ($request->stock === 'bajo') {
            $query->whereColumn('stock_actual', '<=', 'stock_minimo');
        } elseif ($request->stock === 'disponible') {
            $query->where('stock_actual', '>', 0);
        }
        
        $query->where('estado', true);
        
        $productos = $query->orderBy('nombre_producto')->paginate(10)->withQueryString();
        
        return view('productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('productos.create');
    }

    // Crear producto
     //
    public function store(Request $request)
{
    $validated = $request->validate([
        'nombre_producto' => 'required|string|max:100',
        'marca' => 'nullable|string|max:100',
        'descripcion' => 'nullable|string',
        'codigo_barras' => 'nullable|unique:productos,codigo_barras',
        'precio_costo' => 'required|numeric|min:0',
        'precio_venta' => 'required|numeric|min:0',
        'stock_actual' => 'required|integer|min:0',
        'stock_minimo' => 'nullable|integer|min:0',
        'categoria' => 'required|string|max:50',
        'grado_alcoholico' => 'nullable|numeric|min:0|max:100',
        'volumen_ml' => 'nullable|integer|min:0',
        'pais_origen' => 'nullable|string|max:50',
        'fecha_vencimiento' => 'nullable|date',   
        'estado' => 'nullable'                      
    ], [
        'nombre_producto.required' => 'El nombre del producto es obligatorio',
        'precio_costo.required' => 'El precio de costo es obligatorio',
        'precio_venta.required' => 'El precio de venta es obligatorio',
        'stock_actual.required' => 'El stock actual es obligatorio',
        'categoria.required' => 'La categoría es obligatoria'
    ]);
    
    //  Normalizar el campo estado (checkbox envía "0" cuando no está marcado)
    $validated['estado'] = $request->has('estado') && $request->estado == 1 ? true : false;
    
    // Precio sugerido si no viene venta
    if (empty($validated['precio_venta']) && !empty($validated['precio_costo'])) {
        $validated['precio_venta'] = $validated['precio_costo'] * 1.30;
    }
    
    Producto::create($validated);
    AuditoriaLog::registrar('Crear', 'Productos', "Producto: {$validated['nombre_producto']}");

    
    return redirect()->route('productos.index')
        ->with('success', 'Producto creado exitosamente');
}

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $producto = \App\Models\Producto::findOrFail($id);
        return view('productos.edit', compact('producto'));
    }
// Actualizar producto
     //
    public function update(Request $request, $id)
{
    $producto = Producto::findOrFail($id);
    
    $validated = $request->validate([
        'nombre_producto' => 'required|string|max:100',
        'marca' => 'nullable|string|max:100',
        'descripcion' => 'nullable|string',
        'codigo_barras' => 'nullable|unique:productos,codigo_barras,' . $producto->id . ',id',
        'precio_costo' => 'required|numeric|min:0',
        'precio_venta' => 'required|numeric|min:0',
        'stock_actual' => 'required|integer|min:0',
        'stock_minimo' => 'nullable|integer|min:0',
        'categoria' => 'required|string|max:50',
        'grado_alcoholico' => 'nullable|numeric|min:0|max:100',
        'volumen_ml' => 'nullable|integer|min:0',
        'pais_origen' => 'nullable|string|max:50',
        'fecha_vencimiento' => 'nullable|date',
        'estado' => 'nullable'
    ]);
    
    // Normalizar estado
    $validated['estado'] = $request->has('estado') && $request->estado == 1 ? true : false;
    
    $producto->update($validated);
    AuditoriaLog::registrar('Editar', 'Productos', "Producto: {$producto->nombre_producto}");
    
    return redirect()->route('productos.index')
        ->with('success', 'Producto actualizado exitosamente');
}
// Eliminar producto
     //
    public function destroy($id)
    {
        $producto = \App\Models\Producto::findOrFail($id);
        $producto->update(['estado' => false]);
        AuditoriaLog::registrar('Eliminar', 'Productos', "Producto: {$producto->nombre_producto}");
        
        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente');
    }
}