@extends('adminlte::page')

@section('title', 'Ingresar Empleado Planilla')

@section('content_header')
    <h1>INGRESAR NUEVO EMPLEADO PLANILLA</h1>
@stop

@section('content')
   <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INGRESAR EMPLEADO PLANILLA</title>
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
            <form action="{{ route('empleado_planillas.insertar') }}" method="POST">
                @csrf

                <div class="mt-4 text-black">
                    <x-label for="COD_EMPLEADO" value="{{ __('NOM_EMPLEADO') }}" />
                    <select id="COD_EMPLEADO" name="COD_EMPLEADO" class="block mt-1 w-full" required>
                        <option value="">{{ __('Seleccione un empleado') }}</option>
                        @foreach ($empleados as $empleado)
                            <option value="{{ $empleado->COD_EMPLEADO }}">{{ $empleado->NOM_EMPLEADO }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4 text-black">
                    <x-label for="COD_PLANILLA" value="{{ __('PLANILLA') }}" />
                    <select id="COD_PLANILLA" name="COD_PLANILLA" class="block mt-1 w-full" required>
                        <option value="">{{ __('Seleccione una planilla') }}</option>
                        @foreach ($planillas as $planilla)
                            <option value="{{ $planilla->COD_PLANILLA }}">{{ $planilla->COD_PLANILLA }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="SALARIO_BASE" class="form-label">SALARIO BASE</label>
                    <input type="number" class="form-control" id="SALARIO_BASE" name="SALARIO_BASE" required>
                </div>

                <div class="mb-3">
                    <label for="DEDUCCIONES" class="form-label">PORDENTAJE DE DEDUCCIONES (%)</label>
                    <input type="number" class="form-control" id="DEDUCCIONES" name="DEDUCCIONES" required>
                    <small class="form-text text-muted">Ingrese el porcentaje de deducción aplicado al salario base.</small>
                </div>

                <button type="submit" class="btn btn-primary">AGREGAR</button>
                <a href="{{ route('empleado_planillas.index') }}" class="btn btn-secondary">CANCELAR</a>
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
