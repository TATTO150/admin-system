@extends('adminlte::page')

@section('title', 'Crear Solicitud')

@section('content_header')
    <h1 class="text-center">CREAR NUEVA SOLICITUD</h1>
@stop

@section('content')
    <div class="container d-flex justify-content-center">
        <div class="card shadow-sm p-4 mb-5 bg-white rounded" style="width: 50%;">
            <main class="mt-3">
                @if (session('success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: '{{ session('success') }}',
                        });
                    </script>
                @endif
                @if (session('error'))
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: '{{ session('error') }}',
                        });
                    </script>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('solicitudes.insertar') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="COD_EMPLEADO" class="form-label">{{ __('NOMBRE EMPLEADO') }}</label>
                        <select class="form-select select2" id="COD_EMPLEADO" name="COD_EMPLEADO" required>
                            <option value="">{{ __('Seleccione un empleado') }}</option>
                            @foreach ($empleados as $empleado)
                                <option value="{{ $empleado->COD_EMPLEADO }}" {{ old('COD_EMPLEADO') == $empleado->COD_EMPLEADO ? 'selected' : '' }}>
                                {{ $empleado->nombre_con_dni }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="DESC_SOLICITUD" class="form-label">{{ __('DESCRIPCIÓN SOLICITUD') }}</label>
                        <textarea class="form-control" id="DESC_SOLICITUD" name="DESC_SOLICITUD" rows="3" required>{{ old('DESC_SOLICITUD') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="COD_AREA" class="form-label">{{ __('NOMBRE AREA') }}</label>
                        <select class="form-select select2" id="COD_AREA" name="COD_AREA" required>
                            <option value="">{{ __('Seleccione un área') }}</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->COD_AREA }}" {{ old('COD_AREA') == $area->COD_AREA ? 'selected' : '' }}>
                                    {{ $area->NOM_AREA }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="COD_PROYECTO" class="form-label">{{ __('NOMBRE PROYECTO') }}</label>
                        <select class="form-select select2" id="COD_PROYECTO" name="COD_PROYECTO" required>
                            <option value="">{{ __('Seleccione un proyecto') }}</option>
                            @foreach ($proyectos as $proyecto)
                                <option value="{{ $proyecto->COD_PROYECTO }}" {{ old('COD_PROYECTO') == $proyecto->COD_PROYECTO ? 'selected' : '' }}>
                                    {{ $proyecto->NOM_PROYECTO }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="PRESUPUESTO_SOLICITUD" class="form-label">{{ __('PRESUPUESTO SOLICITUD') }}</label>
                        <input type="number" step="0.01" class="form-control" id="PRESUPUESTO_SOLICITUD" name="PRESUPUESTO_SOLICITUD" value="{{ old('PRESUPUESTO_SOLICITUD') }}" required>
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-primary">GUARDAR</button>
                        <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">CANCELAR</a>
                    </div>
                </form>
            </main>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css">
    <style>
        .card {
            margin-top: 20px;
        }
        .form-label {
            font-weight: bold;
        }
        .select2-container {
            width: 100% !important;
        }
    </style>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();

            // Manejar la selección de datos
            $('#empleadoTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
                },
                pageLength: 15
            });

            // Manejar la selección de empleados desde el modal
            $('.select-empleado').click(function() {
                var selectedEmpleado = $(this).data('id');
                var selectedEmpleadoText = $(this).data('name');

                $('#COD_EMPLEADO').append(new Option(selectedEmpleadoText, selectedEmpleado, true, true)).trigger('change');
                $('#empleadoModal').modal('hide');
            });
        });
    </script>
@stop
