@extends('adminlte::page')

@section('title', 'Editar Empleado')

@section('content_header')
@stop

@section('content')
<style>
    .form-container {
        max-width: 600px;
        margin: 20px auto; /* Espacio arriba y abajo del contenedor de la tarjeta */
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
        margin: 10px auto; /* 10px arriba y abajo, centrado de lado a lado */
        max-width: 100%; /* Asegura que el margen centrado se respete */
    }
    .form-control, .form-select {
        width: 100%;
        padding: 0.375rem 0.75rem;
        border-radius: 4px;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    }
</style>

    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h2>EDITAR EMPLEADO</h2>
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

                <form action="{{ route('empleados.update', ['COD_EMPLEADO' => $empleados['COD_EMPLEADO']]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="NOM_EMPLEADO" class="form-label">NOMBRE EMPLEADO</label>
                        <input type="text" class="form-control" id="NOM_EMPLEADO" name="NOM_EMPLEADO" value="{{ $empleados['NOM_EMPLEADO'] }}">
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
                        <label for="COD_AREA" class="form-label">AREA</label>
                        <select id="COD_AREA" name="COD_AREA" class="form-select" required>
                            <option value="">{{ __('Seleccione un área') }}</option>
                            @foreach ($areas as $Area)
                                <option value="{{ $Area->COD_AREA }}" {{ $empleados['COD_AREA'] == $Area->COD_AREA ? 'selected' : '' }}>{{ $Area->NOM_AREA }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="DNI_EMPLEADO" class="form-label">DNI EMPLEADO</label>
                        <input type="text" class="form-control" id="DNI_EMPLEADO" name="DNI_EMPLEADO" value="{{ $empleados['DNI_EMPLEADO'] }}">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="LICENCIA_VEHICULAR" name="LICENCIA_VEHICULAR" value="1" {{ $empleados['LICENCIA_VEHICULAR'] ? 'checked' : '' }}>
                        <label for="LICENCIA_VEHICULAR" class="form-check-label">LICENCIA VEHICULAR</label>
                    </div>

                    <div class="mb-3">
                        <label for="COD_CARGO" class="form-label">CARGO</label>
                        <select id="COD_CARGO" name="COD_CARGO" class="form-select" required>
                            <option value="">{{ __('Seleccione un cargo') }}</option>
                            @foreach ($cargos as $Cargo)
                                <option value="{{ $Cargo->COD_CARGO }}" {{ $empleados['COD_CARGO'] == $Cargo->COD_CARGO ? 'selected' : '' }}>{{ $Cargo->NOM_CARGO }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="FEC_INGRESO_EMPLEADO" class="form-label">FECHA INGRESO</label>
                        <input type="date" class="form-control" id="FEC_INGRESO_EMPLEADO" name="FEC_INGRESO_EMPLEADO" value="{{ $empleados['FEC_INGRESO_EMPLEADO'] }}">
                    </div>
                    

                    <div class="mb-3">
                        <label for="CORREO_EMPLEADO" class="form-label">CORREO EMPLEADO</label>
                        <input type="email" class="form-control" id="CORREO_EMPLEADO" name="CORREO_EMPLEADO" value="{{ $empleados['CORREO_EMPLEADO'] }}">
                    </div>

                    <div class="mb-3">
                        <label for="DIRECCION_EMPLEADO" class="form-label">DIRECCION EMPLEADO</label>
                        <input type="text" class="form-control" id="DIRECCION_EMPLEADO" name="DIRECCION_EMPLEADO" value="{{ $empleados['DIRECCION_EMPLEADO'] }}">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="CONTRATO_EMPLEADO" name="CONTRATO_EMPLEADO" value="1" {{ $empleados['CONTRATO_EMPLEADO'] ? 'checked' : '' }}>
                        <label for="CONTRATO_EMPLEADO" class="form-check-label">CONTRATO EMPLEADO</label>
                    </div>

                    <div class="mb-3">
                        <label for="SALARIO_BASE" class="form-label">SALARIO BASE</label>
                        <input type="number" step="0.01" class="form-control" id="SALARIO_BASE" name="SALARIO_BASE" value="{{ $empleados['SALARIO_BASE'] }}">
                    </div>

                    <!-- Proyectos Asignados -->
                    <div class="mb-3">
                        <label for="proyectos" class="form-label">Asignar Proyectos</label>
                        <input type="text" class="form-control" id="proyecto-search" placeholder="Buscar proyectos...">
                        <div id="proyectos-list" style="max-height: 200px; overflow-y: auto;">
                            @foreach ($proyectos as $proyecto)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="proyectos[]" value="{{ $proyecto->COD_PROYECTO }}" id="proyecto-{{ $proyecto->COD_PROYECTO }}"
                                        {{ in_array($proyecto->COD_PROYECTO, $empleadosProyectos) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="proyecto-{{ $proyecto->COD_PROYECTO }}">
                                        {{ $proyecto->NOM_PROYECTO }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Botón de Guardar Cambios -->
                    <button type="submit" class="btn btn-primary btn-custom">GUARDAR CAMBIOS</button>
                    <a href="{{ route('empleados.index') }}" class="btn btn-secondary btn-custom mt-2">CANCELAR</a>
                </form>
            </main>
        </div>
    </div>

    <!-- Agregar enlaces a los archivos JS de Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmSubmit() {
            if (confirm('¿Está seguro que desea guardar este registro?')) {
                document.querySelector('form').submit();
            }
        }

        // Función de búsqueda para proyectos
        document.getElementById('proyecto-search').addEventListener('input', function() {
            var searchValue = this.value.toLowerCase();
            var proyectos = document.querySelectorAll('#proyectos-list .form-check');

            proyectos.forEach(function(proyecto) {
                var label = proyecto.querySelector('label').innerText.toLowerCase();
                if (label.includes(searchValue)) {
                    proyecto.style.display = '';
                } else {
                    proyecto.style.display = 'none';
                }
            });
        });
    </script>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
