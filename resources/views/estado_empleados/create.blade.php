@extends('adminlte::page')

@section('title', 'Crear Estado de Empleado')

@section('content_header')
    <h1>NUEVO TIPO DE EMPLEADO</h1>
@stop

@section('content')
    <div class="container">
        <main class="mt-3">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('estado_empleados.insertar') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="ESTADO_EMPLEADO" class="form-label">TIPO EMPLEADO</label>
                    <input type="text" class="form-control" id="ESTADO_EMPLEADO" name="ESTADO_EMPLEADO" value="{{ old('ESTADO_EMPLEADO') }}">
                </div>
                
                <button type="submit" class="btn btn-primary">AGREGAR TIPO</button>
                <a href="{{ route('estado_empleados.index') }}" class="btn btn-secondary">CANCELAR</a>
            </form>
        </main>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Formulario para crear un nuevo estado de empleado.'); </script>
@stop
