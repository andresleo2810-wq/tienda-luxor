@extends('layouts.app')
@section('title', 'Editar Usuario')
@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Usuarios / Editar</div>
<div class="mb-8">
    <h1 class="text-4xl font-semibold tracking-tight">Editar Usuario</h1>
    <p class="mt-2 text-sm text-luxor-muted">Modifica los datos de <strong>{{ $usuario->nombre_completo }}</strong>.</p>
</div>
@include('usuarios._form', ['usuario' => $usuario, 'ruta' => route('usuarios.update', $usuario->id), 'metodo' => 'PUT'])
@endsection