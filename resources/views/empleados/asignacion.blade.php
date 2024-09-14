@extends('adminlte::page')

@section('title', 'Asignación de Empleados a Proyectos')

@section('content_header')
    <h1>Asignar Empleados a Proyectos</h1>
@stop

@section('content')
    <div class="container">
        <!-- Mostrar errores -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Mostrar mensaje de éxito -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('asignacion.guardar') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="empleado" class="form-label">Empleado</label>
                <select id="empleado" name="empleado[]" class="form-select select2" multiple required>
                    <option value="">{{ __('Seleccione uno o más empleados') }}</option>
                    @foreach ($empleados as $empleado)
                        <option value="{{ $empleado['COD_EMPLEADO'] }}" 
                                {{ (isset($empleadoId) && $empleadoId == $empleado['COD_EMPLEADO']) ? 'selected' : '' }}>
                            {{ $empleado['NOM_EMPLEADO'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="proyecto" class="form-label">Proyecto</label>
                <select id="proyecto" name="proyecto" class="form-select select2" required>
                    <option value="">{{ __('Seleccione un proyecto') }}</option>
                    @foreach ($proyectos as $proyecto)
                        <option value="{{ $proyecto['COD_PROYECTO'] }}">{{ $proyecto['NOM_PROYECTO'] }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Asignar</button>
        </form>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" type="text/css">
@stop

@section('js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });
        });
    </script>
@stop
