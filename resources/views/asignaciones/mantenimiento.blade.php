@extends('adminlte::page')

@section('title', 'Crear Mantenimiento')

@section('content_header')
    <h1 class="text-center">CREAR MANTENIMIENTO</h1>
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

                <form action="{{ route('mantenimiento.store') }}" method="POST" onsubmit="return disableSubmitButton(this);">
                    @csrf
                    <div class="mb-3">
                        <label for="COD_EQUIPO" class="form-label">CÓDIGO EQUIPO</label>
                        <select class="form-select select2" id="COD_EQUIPO" name="COD_EQUIPO" required>
                            <option value="">Seleccione un equipo</option>
                            @foreach($equipos as $equipo)
                                <option value="{{ $equipo['COD_EQUIPO'] }}" {{ old('COD_EQUIPO') == $equipo['COD_EQUIPO'] ? 'selected' : '' }}>
                                    {{ $equipo['NOM_EQUIPO'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="COD_EMPLEADO" class="form-label">CÓDIGO EMPLEADO</label>
                        <div class="input-group">
                            <select class="form-select select2" id="COD_EMPLEADO" name="COD_EMPLEADO" required>
                                <option value="">Seleccione un empleado</option>
                                @foreach($empleados as $empleado)
                                    <option value="{{ $empleado['COD_EMPLEADO'] }}" {{ old('COD_EMPLEADO') == $empleado['COD_EMPLEADO'] ? 'selected' : '' }}>
                                        {{ $empleado['NOM_EMPLEADO'] }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#empleadoModal">
                                Buscar
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="DESCRIPCION" class="form-label">DESCRIPCIÓN</label>
                        <textarea class="form-control" id="DESCRIPCION" name="DESCRIPCION" rows="3" required>{{ old('DESCRIPCION') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="FECHA_ASIGNACION_INICIO" class="form-label">FECHA INICIO ASIGNACIÓN</label>
                        <input type="date" class="form-control" id="FECHA_ASIGNACION_INICIO" name="FECHA_ASIGNACION_INICIO" value="{{ old('FECHA_ASIGNACION_INICIO') }}" required>
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-primary" id="submitButton">GUARDAR</button>
                        <a href="{{ route('asignaciones.index') }}" class="btn btn-secondary">CANCELAR</a>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <!-- Modal para buscar empleados -->
    <div class="modal fade" id="empleadoModal" tabindex="-1" aria-labelledby="empleadoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="empleadoModalLabel">Buscar Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table id="empleadoTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Seleccionar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($empleados as $empleado)
                                <tr>
                                    <td>{{ $empleado['COD_EMPLEADO'] }}</td>
                                    <td>{{ $empleado['NOM_EMPLEADO'] }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary select-empleado" data-id="{{ $empleado['COD_EMPLEADO'] }}" data-name="{{ $empleado['NOM_EMPLEADO'] }}">Seleccionar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
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
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }
        .modal-lg {
            max-width: 80%;
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

            // Inicializar DataTable
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

        function disableSubmitButton(form) {
            form.submitButton.disabled = true;
            return true;
        }
    </script>
@stop
