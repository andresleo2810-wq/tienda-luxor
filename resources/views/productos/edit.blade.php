@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Productos / Editar</div>
<div class="mb-8 flex items-end justify-between">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Editar Producto</h1>
        <p class="mt-2 text-sm text-luxor-muted">Modifica la información de <strong>{{ $producto->nombre_producto }}</strong>.</p>
    </div>
    <a href="{{ route('productos.index') }}" class="text-sm text-luxor-muted hover:text-luxor-text">← Volver</a>
</div>

@include('productos._form', [
    'producto' => $producto,
    'ruta' => route('productos.update', $producto->id),
    'metodo' => 'PUT',
])
@endsection