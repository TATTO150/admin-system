@extends('adminlte::page')

@section('title', 'Crear Empleado')

@section('content_header')
    <h1>INGRESAR NUEVO EMPLEADO</h1>
@stop

@section('content')
   <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INGRESAR EMPLEADO</title>
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
            <form action="{{ route('empleados.generarPlanilla', ['COD_PROYECTO' => $COD_PROYECTO]) }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="NOM_EMPLEADO" class="form-label">NOMBRE EMPLEADO</label>
                    <input type="text" class="form-control"  id="NOM_EMPLEADO" name="NOM_EMPLEADO" :value="old('NOM_EMPLEADO')" > 
                </div>

                <div class="text-black">
                <x-label for="TIP_EMPLEADO" value="{{ __('TIPO EMPLEADO') }}" />
                <select id="TIP_EMPLEADO" class="block mt-1 w-full" name="TIP_EMPLEADO" required>
                    <option value="TEMPORAL" @if(old('TIP_EMPLEADO') == 'TEMPORAL') selected @endif>TEMPORAL</option>
                    <option value="PERMANENTE" @if(old('TIP_EMPLEADO') == 'PERMANENTE') selected @endif>PERMANENTE</option>
                    </select>
                </div>
                
                <div class="mt-4 text-black">
                    <x-label for="COD_AREA" value="{{ __('AREA') }}" />
                    <select id="COD_AREA" name="COD_AREA" class="block mt-1 w-full" required>
                        <option value="">{{ __('Seleccione un area') }}</option>
                        @foreach ($areas as $Area)
                            <option value="{{ $Area->COD_AREA }}">{{ $Area->NOM_AREA }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="DNI_EMPLEADO" class="form-label">DNI EMPLEADO</label>
                    <input type="number" class="form-control" id="DNI_EMPLEADO" name="DNI_EMPLEADO">
                </div>

                <div class="mb-3 form-check">
                 <input type="checkbox" class="form-check-input" id="LICENCIA_VEHICULAR" name="LICENCIA_VEHICULAR" value="1">
                  <label for="LICENCIA_VEHICULAR" class="form-check-label">LICENCIA VEHICULAR</label>
                </div>


                <div class="mt-4 text-black">
                    <x-label for="COD_CARGO" value="{{ __('CARGO') }}" />
                    <select id="COD_CARGO" name="COD_CARGO" class="block mt-1 w-full" required>
                        <option value="">{{ __('Seleccione un cargo') }}</option>
                        @foreach ($cargos as $Cargo)
                            <option value="{{ $Cargo->COD_CARGO }}">{{ $Cargo->NOM_CARGO }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="CORREO_EMPLEADO" class="form-label">CORREO EMPLEADO</label>
                    <input type="text" class="form-control" id="CORREO_EMPLEADO" name="CORREO_EMPLEADO">
                </div>

                <div class="mb-3">
                    <label for="DIRECCION_EMPLEADO" class="form-label">DIRECCION EMPLEADO</label>
                    <input type="text" class="form-control" id="DIRECCION_EMPLEADO" name="DIRECCION_EMPLEADO">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="CONTRATO_EMPLEADO" value="1">
                    <label for="CONTRATO_EMPLEADO" class="form-check-label">CONTRATO EMPLEADO</label>
                </div>

                <div class="mb-3">
                    <label for="SALARIO_BASE" class="form-label">SALARIO BASE</label>
                    <input type="number" step="0.01" class="form-control" id="SALARIO_BASE" name="SALARIO_BASE" oninput="calculateNetSalary()">
                </div>


                <button type="submit" class="btn btn-primary">AGREGAR EMPLEADO</button>
                <a href="{{ route('empleados.index') }}" class="btn btn-secondary">CANCELAR</a>
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