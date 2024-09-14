@extends('adminlte::page')

@section('title', 'Crear Asignación')

@section('content_header')
    <h1 class="text-center">Crear Asignación</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Mostrar errores de validación -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulario de creación de asignación -->
                <form action="{{ route('asignaciones.store') }}" method="POST">
                    @csrf

                    <!-- Tipo de Asignación -->
                    <div class="form-group">
                        <label for="TIPO_ASIGNACION">Tipo de Asignación</label>
                        <select name="TIPO_ASIGNACION" id="TIPO_ASIGNACION" class="form-control select2" required>
                            <option value="">Seleccione un tipo</option>
                            @foreach ($tiposAsignacion as $tipo)
                                <option value="{{ $tipo->COD_TIPO_ASIGNACION }}" {{ old('TIPO_ASIGNACION') == $tipo->COD_TIPO_ASIGNACION ? 'selected' : '' }}>
                                    {{ $tipo->TIPO_ASIGNACION }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Equipo -->
                    <div class="form-group">
                        <label for="COD_EQUIPO">Equipo</label>
                        <select name="COD_EQUIPO" id="COD_EQUIPO" class="form-control select2" required>
                            <option value="">Seleccione un equipo</option>
                            @foreach ($equipos as $equipo)
                                <option value="{{ $equipo->COD_EQUIPO }}" {{ old('COD_EQUIPO') == $equipo->COD_EQUIPO ? 'selected' : '' }}>
                                    {{ $equipo->NOM_EQUIPO }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Empleado -->
                    <div class="form-group">
                        <label for="COD_EMPLEADO">Empleado</label>
                        <select name="COD_EMPLEADO" id="COD_EMPLEADO" class="form-control select2" required>
                            <option value="">Seleccione un empleado</option>
                            @foreach ($empleados as $empleado)
                                <option value="{{ $empleado->COD_EMPLEADO }}" {{ old('COD_EMPLEADO') == $empleado->COD_EMPLEADO ? 'selected' : '' }}>
                                    {{ $empleado->nombre_con_dni }} <!-- Mostrar DNI concatenado con nombre -->
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Proyecto -->
                    <div class="form-group" id="proyecto-group">
                        <label for="COD_PROYECTO">Proyecto</label>
                        <select name="COD_PROYECTO" id="COD_PROYECTO" class="form-control select2">
                            <option value="0">Seleccione un proyecto</option>
                            @foreach ($proyectos as $proyecto)
                                <option value="{{ $proyecto->COD_PROYECTO }}" {{ old('COD_PROYECTO') == $proyecto->COD_PROYECTO ? 'selected' : '' }}>
                                    {{ $proyecto->NOM_PROYECTO }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estado Asignación -->
                    <div class="form-group">
                        <label for="COD_ESTADO_ASIGNACION">Estado Asignación</label>
                        <select name="COD_ESTADO_ASIGNACION" id="COD_ESTADO_ASIGNACION" class="form-control select2" required>
                            <option value="1" selected>ACTIVO</option> <!-- Selecciona por defecto el estado "ACTIVO" -->
                            @foreach ($estadosAsignacion as $estado)
                                <option value="{{ $estado->COD_ESTADO_ASIGNACION }}" {{ old('COD_ESTADO_ASIGNACION') == $estado->COD_ESTADO_ASIGNACION ? 'selected' : '' }}>
                                    {{ $estado->ESTADO }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Descripción -->
                    <div class="form-group">
                        <label for="DESCRIPCION">Descripción</label>
                        <textarea name="DESCRIPCION" id="DESCRIPCION" class="form-control" rows="4" maxlength="255" required>{{ old('DESCRIPCION') }}</textarea>
                        <small id="descripcionHelp" class="form-text text-muted">Máximo 255 caracteres.</small>
                    </div>

                    <!-- Fecha de Asignación -->
                    <div class="form-group">
                        <label for="FECHA_ASIGNACION_INICIO">Fecha de Inicio</label>
                        <input type="date" name="FECHA_ASIGNACION_INICIO" id="FECHA_ASIGNACION_INICIO" class="form-control" value="{{ old('FECHA_ASIGNACION_INICIO') }}" required>
                    </div>

                    <!-- Botones Guardar y Cancelar -->
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="{{ route('asignaciones.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <!-- Incluir Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2 en todos los selectores
            $('.select2').select2({
                placeholder: "Seleccione una opción",
                allowClear: true
            });

            const tipoAsignacionSelect = $('#TIPO_ASIGNACION');
            const proyectoGroup = $('#proyecto-group');

            function toggleProyectoField() {
                const selectedValue = tipoAsignacionSelect.val();

                if (selectedValue === '2') { 
                    proyectoGroup.hide();
                    $('#COD_PROYECTO').val('0'); // Asignar valor 0 cuando se oculta el campo
                } else {
                    proyectoGroup.show();
                }
            }

            tipoAsignacionSelect.on('change', toggleProyectoField);

            // Ejecutar al cargar la página para manejar el valor inicial
            toggleProyectoField();

            // Limitar la entrada en el campo de descripción a 255 caracteres
            $('#DESCRIPCION').on('input', function() {
                const maxLength = 255;
                const currentLength = $(this).val().length;

                if (currentLength >= maxLength) {
                    $('#descripcionHelp').text('Has alcanzado el máximo de 255 caracteres.');
                } else {
                    $('#descripcionHelp').text(`Máximo 255 caracteres. Te quedan ${maxLength - currentLength}.`);
                }
            });
        });
    </script>
@stop

@section('css')
    <!-- Incluir el CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
@stop
