@extends('layouts.app')

@section('title', 'Nuevo Producto')

@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Productos / Nuevo</div>
<div class="mb-8 flex items-end justify-between">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Nuevo Producto</h1>
        <p class="mt-2 text-sm text-luxor-muted">Registra un producto en el inventario.</p>
    </div>
    <a href="{{ route('productos.index') }}" class="text-sm text-luxor-muted hover:text-luxor-text">← Volver</a>
</div>

@include('productos._form', [
    'producto' => null,
    'ruta' => route('productos.store'),
    'metodo' => 'POST',
])
@endsection