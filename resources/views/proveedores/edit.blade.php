@extends('layouts.app')
@section('title', 'Editar Proveedor')
@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Proveedores / Editar</div>
<div class="mb-8">
    <h1 class="text-4xl font-semibold tracking-tight">Editar Proveedor</h1>
    <p class="mt-2 text-sm text-luxor-muted">Modifica los datos de <strong>{{ $proveedor->nombre }}</strong>.</p>
</div>
@include('proveedores._form', ['proveedor' => $proveedor, 'ruta' => route('proveedores.update', $proveedor->id), 'metodo' => 'PUT'])
@endsection