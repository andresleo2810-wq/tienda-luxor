@extends('layouts.app')
@section('title', 'Nuevo Proveedor')
@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Proveedores / Nuevo</div>
<div class="mb-8">
    <h1 class="text-4xl font-semibold tracking-tight">Nuevo Proveedor</h1>
    <p class="mt-2 text-sm text-luxor-muted">Registra un distribuidor para los pedidos.</p>
</div>
@include('proveedores._form', ['proveedor' => null, 'ruta' => route('proveedores.store'), 'metodo' => 'POST'])
@endsection