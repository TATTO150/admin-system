@extends('adminlte::page')

@section('title', 'Crear Empleado')

@section('content_header')
    <h1></h1>
@stop

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar Nuevo Empleado</title>
    <!-- Agregar enlaces a los archivos CSS de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 700px;
            margin: auto;
            padding: 2rem;
            background-color: rgb(220, 223, 228);
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-label {
            font-weight: bold;
        }
        .form-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .btn-custom {
            width: 100%;
        }
        .mb-3, .mt-4, .text-black {
            margin-bottom: 1rem !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h2>Ingresar Nuevo Empleado</h2>
            </div>
            <main>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('empleados.insertar') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="NOM_EMPLEADO" class="form-label">Nombre Empleado</label>
                        <input type="text" class="form-control" id="NOM_EMPLEADO" name="NOM_EMPLEADO" value="{{ old('NOM_EMPLEADO') }}">
                    </div>

                    <div class="mb-3">
                        <label for="COD_ESTADO_EMPLEADO" class="form-label">Tipo de Empleado</label>
                        <select id="COD_ESTADO_EMPLEADO" name="COD_ESTADO_EMPLEADO" class="form-select" required>
                            <option value="">{{ __('Seleccione un tipo') }}</option>
                            @foreach ($tipo as $Tipo)
                                <option value="{{ $Tipo->COD_ESTADO_EMPLEADO }}" {{ old('COD_ESTADO_EMPLEADO') == $Tipo->COD_ESTADO_EMPLEADO ? 'selected' : '' }}>
                                    {{ $Tipo->ESTADO_EMPLEADO }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="COD_AREA" class="form-label">Área</label>
                        <select id="COD_AREA" name="COD_AREA" class="form-select" required>
                            <option value="">{{ __('Seleccione un área') }}</option>
                            @foreach ($areas as $Area)
                                <option value="{{ $Area->COD_AREA }}" {{ old('COD_AREA') == $Area->COD_AREA ? 'selected' : '' }}>
                                    {{ $Area->NOM_AREA }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="DNI_EMPLEADO" class="form-label">DNI Empleado</label>
                        <input type="number" class="form-control" id="DNI_EMPLEADO" name="DNI_EMPLEADO" value="{{ old('DNI_EMPLEADO') }}">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="LICENCIA_VEHICULAR" name="LICENCIA_VEHICULAR" value="1" {{ old('LICENCIA_VEHICULAR') ? 'checked' : '' }}>
                        <label for="LICENCIA_VEHICULAR" class="form-check-label">Licencia Vehicular</label>
                    </div>

                    <div class="mb-3">
                        <label for="COD_CARGO" class="form-label">Cargo</label>
                        <select id="COD_CARGO" name="COD_CARGO" class="form-select" required>
                            <option value="">{{ __('Seleccione un cargo') }}</option>
                            @foreach ($cargos as $Cargo)
                                <option value="{{ $Cargo->COD_CARGO }}" {{ old('COD_CARGO') == $Cargo->COD_CARGO ? 'selected' : '' }}>
                                    {{ $Cargo->NOM_CARGO }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="CORREO_EMPLEADO" class="form-label">Correo Empleado</label>
                        <input type="email" class="form-control" id="CORREO_EMPLEADO" name="CORREO_EMPLEADO" value="{{ old('CORREO_EMPLEADO') }}">
                    </div>

                    <div class="mb-3">
                        <label for="DIRECCION_EMPLEADO" class="form-label">Dirección Empleado</label>
                        <input type="text" class="form-control" id="DIRECCION_EMPLEADO" name="DIRECCION_EMPLEADO" value="{{ old('DIRECCION_EMPLEADO') }}">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="CONTRATO_EMPLEADO" name="CONTRATO_EMPLEADO" value="1" {{ old('CONTRATO_EMPLEADO') ? 'checked' : '' }}>
                        <label for="CONTRATO_EMPLEADO" class="form-check-label">Contrato Empleado</label>
                    </div>

                    <div class="mb-3">
                        <label for="SALARIO_BASE" class="form-label">Salario Base</label>
                        <input type="number" step="0.01" class="form-control" id="SALARIO_BASE" name="SALARIO_BASE" value="{{ old('SALARIO_BASE') }}">
                    </div>

                    <!-- Asignación de proyectos -->
                    <div class="mb-3">
                        <label for="proyectos" class="form-label">Proyectos</label>
                        <p class="form-text">Seleccione los proyectos en los que el empleado estará asignado. Puede buscar proyectos específicos usando el campo de búsqueda.</p>
                        <input type="text" id="buscar-proyectos" class="form-control mb-2" placeholder="Buscar proyectos...">
                        <div id="lista-proyectos" style="max-height: 200px; overflow-y: auto;">
                            @foreach ($proyectos as $proyecto)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="proyectos[]" id="proyecto{{ $proyecto->COD_PROYECTO }}" value="{{ $proyecto->COD_PROYECTO }}" {{ in_array($proyecto->COD_PROYECTO, old('proyectos', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="proyecto{{ $proyecto->COD_PROYECTO }}">
                                        {{ $proyecto->NOM_PROYECTO }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-custom">Agregar Empleado</button>
                    <a href="{{ route('empleados.index') }}" class="btn btn-secondary btn-custom mt-2">Cancelar</a>
                </form>
            </main>
        </div>
    </div>

    <!-- Agregar enlaces a los archivos JS de Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('buscar-proyectos').addEventListener('input', function() {
            let filter = this.value.toLowerCase();
            let proyectos = document.querySelectorAll('#lista-proyectos .form-check');

            proyectos.forEach(function(proyecto) {
                let texto = proyecto.textContent.toLowerCase();
                if (texto.includes(filter)) {
                    proyecto.style.display = '';
                } else {
                    proyecto.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
