@extends('adminlte::page')

@section('title', 'Crear Mantenimiento')

@section('content_header')
    <h1>NUEVO MANTENIMIENTO</h1>
@stop

@section('content')
   <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INGRESAR MANTENIMIENTO</title>
    <!-- Agregar enlaces a los archivos CSS de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
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
            <form action="{{ route('mantenimientos.insertar') }}" method="POST">
                @csrf
                <div class="mt-4 text-black">
                    <x-label for="COD_EMPLEADO" value="{{ __('NOMBRE EMPLEADO') }}" />
                    <select id="COD_EMPLEADO" name="COD_EMPLEADO" class="block mt-1 w-full" required>
                        <option value="">{{ __('Seleccione un empleado') }}</option>
                        @foreach ($empleados as $empleado)
                            <option value="{{ $empleado->COD_EMPLEADO}}">{{ $empleado->NOM_EMPLEADO}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4 text-black">
                    <x-label for="COD_ESTADO_MANTENIMIENTO" value="{{ __('Estado Mantenimiento') }}" />
                    <select id="COD_ESTADO_MANTENIMIENTO" name="COD_ESTADO_MANTENIMIENTO" class="block mt-1 w-full" required>
                        <option value="">{{ __('Seleccione un estado') }}</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado->COD_ESTADO_MANTENIMIENTO }}">{{ $estado->ESTADO }}</option>
                        @endforeach
                    </select>
                </div>
    
                <div class="mt-4 text-black">
                    <x-label for="COD_EQUIPO" value="{{ __('NOMBRE EQUIPO') }}" />
                    <select id="COD_EQUIPO" name="COD_EQUIPO" class="block mt-1 w-full" required>
                        <option value="">{{ __('Seleccione un equipo') }}</option>
                        @foreach ($equipo as $equipo)
                            <option value="{{ $equipo->COD_EQUIPO}}">{{ $equipo->NOM_EQUIPO}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="DESC_MANTENIMIENTO" class="form-label">DESCRIPCION MANTENIMIENTO</label>
                    <input type="text" class="form-control" id="DESC_MANTENIMIENTO" name="DESC_MANTENIMIENTO">
                </div>

                <div class="mb-3">
                    <label for="FEC_FINAL_PLANIFICADA" class="form-label">FECHA FINAL PLANIFICADA</label>
                    <input type="date" class="form-control" id="FEC_FINAL_PLANIFICADA" name="FEC_FINAL_PLANIFICADA">

                    <div class="mb-3">
                    <label for="FEC_FINAL_REAL" class="form-label">FECHA FINAL REAL</label>
                    <input type="date" class="form-control" id="FEC_FINAL_REAL" name="FEC_FINAL_REAL">

                <button type="submit" class="btn btn-primary">AGREGAR</button>
                <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary">CANCELAR</a>
            </form>
        </main>
    </div>
    
    <!-- Agregar enlaces a los archivos JS de Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop