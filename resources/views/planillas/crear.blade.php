@extends('adminlte::page')

@section('title', 'CREAR PLANILLA')

@section('content_header')
    <h1>CREAR NUEVA PLANILLA</h1>
@stop

@section('content')
   <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CREAR NUEVA</title>
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
            <form action="{{ route('planillas.insertar') }}" method="POST">
                @csrf
                <div class="mt-4 text-black">
                    <x-label for="COD_PROYECTO" value="{{ __('NOMBRE PROYECTO') }}" />
                    <select id="COD_PROYECTO" name="COD_PROYECTO" class="block mt-1 w-full" required>
                        <option value="">{{ __('Seleccione un proyecto') }}</option>
                        @foreach ($proyectos as $Proyecto)
                            <option value="{{ $Proyecto->COD_PROYECTO}}">{{ $Proyecto->NOM_PROYECTO}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="text-black">
                    <x-label for="TIP_PLANILLA" value="{{ __('TIPO PLANILLA') }}" />
                    <select id="TIP_PLANILLA" class="block mt-1 w-full" name="TIP_PLANILLA" required>
                        <option value="PROYECTOS" @if(old('TIP_PLANILLA') == 'PROYECTOS') selected @endif>PROYECTOS</option>
                        <option value="ADMINISTRACION" @if(old('TIP_PLANILLA') == 'ADMINISTRACION') selected @endif>ADMINISTRACION</option>
                    </select>
                </div>
    
                <div class="text-black">
                    <x-label for="ESTADO_PLANILLA" value="{{ __('ESTADO PLANILLA') }}" />
                    <select id="ESTADO_PLANILLA" class="block mt-1 w-full" name="ESTADO_PLANILLA" required>
                        <option value="PAGADA" @if(old('ESTADO_PLANILLA') == 'PAGADA') selected @endif>PAGADA</option>
                        <option value="PENDIENTE" @if(old('ESTADO_PLANILLA') == 'PENDIENTE') selected @endif>PENDIENTE</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="FECHA_PAGO" class="form-label">FECHA PAGO</label>
                    <input type="date" class="form-control" id="FECHA_PAGO" name="FECHA_PAGO">
                </div>

                <button type="submit" class="btn btn-primary">NUEVO</button>
                <a href="{{ route('planillas.index') }}" class="btn btn-secondary">CANCELAR</a>
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
