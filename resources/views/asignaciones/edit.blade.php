@extends('adminlte::page')

@section('title', 'Editar Asignación')

@section('content_header')
    <h1 class="text-center">Editar Asignación</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: '{{ session('success') }}',
                        });
                    </script>
                @endif
                @if(session('error'))
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: '{{ session('error') }}',
                        });
                    </script>
                @endif

                <!-- Formulario de edición de asignación -->
                <form action="{{ route('asignaciones.update', $asignacion->COD_ASIGNACION_EQUIPO) }}" method="POST" id="editForm">
                    @csrf
                    @method('PUT')

                    <!-- Tipo de Asignación (no editable si el estado es 1 o 2) -->
                    <div class="form-group">
                        <label for="TIPO_ASIGNACION">Tipo de Asignación</label>
                        <select name="TIPO_ASIGNACION" id="TIPO_ASIGNACION" class="form-control select2" {{ in_array($asignacion->COD_ESTADO_ASIGNACION, [1, 2]) ? 'disabled' : '' }}>
                            @foreach($tiposAsignacion as $tipo)
                                <option value="{{ $tipo->COD_TIPO_ASIGNACION }}" 
                                    {{ $asignacion->TIPO_ASIGNACION == $tipo->COD_TIPO_ASIGNACION ? 'selected' : '' }}>
                                    {{ $tipo->TIPO_ASIGNACION }}
                                </option>
                            @endforeach
                        </select>
                        @if(in_array($asignacion->COD_ESTADO_ASIGNACION, [1, 2]))
                            <input type="hidden" name="TIPO_ASIGNACION" value="{{ $asignacion->TIPO_ASIGNACION }}">
                        @endif
                    </div>

                    <!-- Equipo (no editable si el estado es 1 o 2) -->
                    <div class="form-group">
                        <label for="COD_EQUIPO">Equipo</label>
                        <select name="COD_EQUIPO" id="COD_EQUIPO" class="form-control select2" {{ in_array($asignacion->COD_ESTADO_ASIGNACION, [1, 2]) ? 'disabled' : '' }}>
                            @foreach($equipos as $equipo)
                                <option value="{{ $equipo->COD_EQUIPO }}" 
                                    {{ $asignacion->COD_EQUIPO == $equipo->COD_EQUIPO ? 'selected' : '' }}>
                                    {{ $equipo->NOM_EQUIPO }}
                                </option>
                            @endforeach
                        </select>
                        @if(in_array($asignacion->COD_ESTADO_ASIGNACION, [1, 2]))
                            <input type="hidden" name="COD_EQUIPO" value="{{ $asignacion->COD_EQUIPO }}">
                        @endif
                    </div>

                    <!-- Empleado (editable si estado no es 2) -->
                    <div class="form-group">
                        <label for="COD_EMPLEADO">Empleado</label>
                        <select name="COD_EMPLEADO" id="COD_EMPLEADO" class="form-control select2" {{ $asignacion->COD_ESTADO_ASIGNACION == 2 ? 'disabled' : '' }} required>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->COD_EMPLEADO }}" 
                                    {{ $asignacion->COD_EMPLEADO == $empleado->COD_EMPLEADO ? 'selected' : '' }}>
                                    {{ $empleado->nombre_con_dni }} <!-- Mostrar DNI concatenado con nombre -->
                                </option>
                            @endforeach
                        </select>
                        @if($asignacion->COD_ESTADO_ASIGNACION == 2)
                            <input type="hidden" name="COD_EMPLEADO" value="{{ $asignacion->COD_EMPLEADO }}">
                        @endif
                    </div>

                    <!-- Proyecto (editable si estado no es 2 y tipo de asignación no es mantenimiento) -->
                    <div class="form-group" id="proyecto-group">
                        <label for="COD_PROYECTO">Proyecto</label>
                        <select name="COD_PROYECTO" id="COD_PROYECTO" class="form-control select2" {{ $asignacion->COD_ESTADO_ASIGNACION == 2 ? 'disabled' : '' }}>
                            <option value="0">Seleccione un proyecto</option>
                            @foreach($proyectos as $proyecto)
                                <option value="{{ $proyecto->COD_PROYECTO }}" 
                                    {{ $asignacion->COD_PROYECTO == $proyecto->COD_PROYECTO ? 'selected' : '' }}>
                                    {{ $proyecto->NOM_PROYECTO }}
                                </option>
                            @endforeach
                        </select>
                        @if($asignacion->COD_ESTADO_ASIGNACION == 2)
                            <input type="hidden" name="COD_PROYECTO" value="{{ $asignacion->COD_PROYECTO }}">
                        @endif
                    </div>

                    <!-- Estado Asignación (no editable si el estado es 1 o 2) -->
                    <div class="form-group">
                        <label for="COD_ESTADO_ASIGNACION">Estado de Asignación</label>
                        <select name="COD_ESTADO_ASIGNACION" id="COD_ESTADO_ASIGNACION" class="form-control select2" {{ in_array($asignacion->COD_ESTADO_ASIGNACION, [1, 2]) ? 'disabled' : '' }}>
                            @foreach($estadosAsignacion as $estado)
                                <option value="{{ $estado->COD_ESTADO_ASIGNACION }}" 
                                    {{ $asignacion->COD_ESTADO_ASIGNACION == $estado->COD_ESTADO_ASIGNACION ? 'selected' : '' }}>
                                    {{ $estado->ESTADO }}
                                </option>
                            @endforeach
                        </select>
                        @if(in_array($asignacion->COD_ESTADO_ASIGNACION, [1, 2]))
                            <input type="hidden" name="COD_ESTADO_ASIGNACION" value="{{ $asignacion->COD_ESTADO_ASIGNACION }}">
                        @endif
                    </div>

                    <!-- Descripción (editable siempre) -->
                    <div class="form-group">
                        <label for="DESCRIPCION">Descripción</label>
                        <textarea name="DESCRIPCION" id="DESCRIPCION" class="form-control" rows="4" maxlength="255" required>{{ old('DESCRIPCION', $asignacion->DESCRIPCION) }}</textarea>
                        <small id="descripcionHelp" class="form-text text-muted">Máximo 255 caracteres.</small>
                    </div>

                    <!-- Fecha de Asignación (editable solo si estado es 1) -->
                    <div class="form-group">
                        <label for="FECHA_ASIGNACION_INICIO">Fecha de Inicio</label>
                        <input type="date" name="FECHA_ASIGNACION_INICIO" id="FECHA_ASIGNACION_INICIO" class="form-control" 
                            value="{{ old('FECHA_ASIGNACION_INICIO', $asignacion->FECHA_ASIGNACION_INICIO) }}" 
                            {{ $asignacion->COD_ESTADO_ASIGNACION == 1 ? '' : 'readonly' }} required>
                    </div>

                    <!-- Botones Guardar y Cancelar -->
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary" id="saveBtn">Guardar</button>
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
            $('.select2').select2({
                placeholder: "Seleccione una opción",
                allowClear: true
            });

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

            // Ocultar proyecto si el tipo de asignación es mantenimiento
            const tipoAsignacionSelect = $('#TIPO_ASIGNACION');
            const proyectoGroup = $('#proyecto-group');

            function toggleProyectoField() {
                const selectedValue = tipoAsignacionSelect.val();

                if (selectedValue == '2') { 
                    proyectoGroup.hide();
                    $('#COD_PROYECTO').val('0'); // Asignar valor 0 cuando se oculta el campo
                } else {
                    proyectoGroup.show();
                }
            }

            tipoAsignacionSelect.on('change', toggleProyectoField);

            // Ejecutar al cargar la página para manejar el valor inicial
            toggleProyectoField();

            // Evitar que el botón de guardar se presione más de una vez
            $('#saveBtn').on('click', function() {
                $(this).prop('disabled', true);
                $('#editForm').submit();
            });
        });
    </script>
@stop

@section('css')
    <!-- Incluir el CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
@stop
