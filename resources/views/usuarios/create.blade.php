@extends('layouts.app')
@section('title', 'Nuevo Usuario')
@section('content')
<div class="mb-2 text-xs text-luxor-muted">Gestión / Usuarios / Nuevo</div>
<div class="mb-8">
    <h1 class="text-4xl font-semibold tracking-tight">Nuevo Usuario</h1>
    <p class="mt-2 text-sm text-luxor-muted">Crea una cuenta de acceso para el personal.</p>
</div>
@include('usuarios._form', ['usuario' => null, 'ruta' => route('usuarios.store'), 'metodo' => 'POST'])
@endsection