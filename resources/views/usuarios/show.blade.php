@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">{{ __('Detalles del Usuario') }}</div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $usuario->Id_usuario }}</p>
            <p><strong>Nombre:</strong> {{ $usuario->nombre }}</p>
            <p><strong>Email:</strong> {{ $usuario->email }}</p>
            <p><strong>Estado:</strong> {{ $usuario->Estado_Usuario }}</p>
            <p><strong>Rol:</strong> {{ $usuario->roles }}</p>
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</div>
@endsection
