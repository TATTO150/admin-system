@extends('adminlte::page')

@section('title', 'Asignaciones')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1 class="text-center">ASIGNACIONES</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
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

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Botones de acción -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="btn-group">
                        <a href="{{ route('asignaciones.crear') }}" class="btn btn-success mx-2">
                            <span class="plus-icon">+</span> Asignar
                        </a>
                        <button type="button" class="btn btn-info mx-2" data-toggle="modal" data-target="#reporteModal">
                            Generar Reporte
                        </button>
                    </div>
                </div>

                <!-- Filtro y búsqueda -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <!-- Formulario de búsqueda activa -->
                    <form id="searchForm" class="form-inline">
                        <div class="form-group mr-2">
                            <label for="searchInput" class="mr-2">Buscar:</label>
                            <input type="text" id="searchInput" class="form-control" placeholder="Ingrese un término de búsqueda">
                        </div>
                    </form>

                    <form action="{{ route('asignaciones.index') }}" method="GET" class="form-inline">
                        <div class="form-group mr-2">
                            <label for="cod_estado_asignacion" class="mr-2">Estado Asignación:</label>
                            <select name="cod_estado_asignacion" id="cod_estado_asignacion" class="form-control">
                                <option value="">TODOS</option>
                                @foreach ($estadosAsignacion as $estado)
                                    <option value="{{ $estado->COD_ESTADO_ASIGNACION }}" {{ request('cod_estado_asignacion') == $estado->COD_ESTADO_ASIGNACION ? 'selected' : '' }}>
                                        {{ $estado->ESTADO }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">FILTRAR</button>
                    </form>
                </div>

                <!-- Mensaje de advertencia cuando no hay coincidencias -->
                <div id="noResultsMessage" class="alert alert-warning text-center" style="display: none;">
                    No se encontraron coincidencias.
                </div>

                <!-- Tabla -->
                <table id="mitabla" class="table table-hover table-bordered w-100">
                    <thead class="thead-dark">
                        <tr>
                            <th>NOMBRE EQUIPO</th>
                            <th>NOMBRE EMPLEADO</th>
                            <th>NOMBRE PROYECTO</th>
                            <th>DESCRIPCIÓN</th>
                            <th>TIPO ASIGNACIÓN</th>
                            <th>ESTADO ASIGNACIÓN</th>
                            <th>FECHA INICIO</th>
                            <th>FECHA FIN</th>
                            <th>ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody id="tablaAsignaciones">
                        @if($asignaciones->isNotEmpty())
                            @foreach ($asignaciones as $asignacion)
                                <tr data-cod-asignacion="{{ $asignacion->COD_ASIGNACION_EQUIPO }}">
                                    <td>{{ $asignacion->NOM_EQUIPO }}</td>
                                    <td>{{ $asignacion->NOM_EMPLEADO }}</td>
                                    <td>{{ $asignacion->NOM_PROYECTO }}</td>
                                    <td>{{ $asignacion->DESCRIPCION }}</td>
                                    <td>{{ $asignacion->TIPO_ASIGNACION_NOMBRE }}</td>
                                    <td>{{ $asignacion->ESTADO_ASIGNACION }}</td>
                                    <td>{{ $asignacion->FECHA_ASIGNACION_INICIO }}</td>                
                                    <td>{{ $asignacion->FECHA_ASIGNACION_FIN }}</td>
                                    <td>
                                        <a href="{{ route('asignaciones.edit', $asignacion->COD_ASIGNACION_EQUIPO) }}" class="btn btn-warning btn-sm">EDITAR</a>
                                        <button class="btn btn-danger btn-sm" onclick="confirmInactive({{ $asignacion->COD_ASIGNACION_EQUIPO }}, {{ $asignacion->COD_ESTADO_ASIGNACION }})">ELIMINAR</button>
                                        @if($asignacion->mostrar_finalizar)
                                            <button class="btn btn-info btn-sm" onclick="confirmFinalizar({{ $asignacion->COD_ASIGNACION_EQUIPO }}, {{ $asignacion->COD_ESTADO_ASIGNACION }}, '{{ $asignacion->COD_EQUIPO }}', '{{ $asignacion->COD_EMPLEADO }}', '{{ $asignacion->COD_PROYECTO }}', '{{ $asignacion->DESCRIPCION }}', '{{ $asignacion->FECHA_ASIGNACION_INICIO }}')">FINALIZAR</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center">No se encontraron asignaciones.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <!-- Botón para eliminar el filtro -->
                <button type="button" class="btn btn-warning mt-3 d-none" id="clearFilter">Quitar Filtro</button>

                <!-- Formulario para finalizar -->
                <form id="finalizar-form" method="POST" style="display: none;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="COD_EQUIPO" id="finalizar-cod-equipo">
                    <input type="hidden" name="COD_EMPLEADO" id="finalizar-cod-empleado">
                    <input type="hidden" name="COD_PROYECTO" id="finalizar-cod-proyecto">
                    <input type="hidden" name="DESCRIPCION" id="finalizar-descripcion">
                    <input type="hidden" name="FECHA_ASIGNACION_INICIO" id="finalizar-fecha-inicio">
                </form>

                <!-- Formulario para inactivar -->
                <form id="inactivar-form" method="POST" style="display: none;">
                    @csrf
                    @method('PUT')
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Generar Reporte -->
    <div class="modal fade" id="reporteModal" tabindex="-1" aria-labelledby="reporteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="reporteModalLabel">Generar Reporte</h5>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reporteForm" action="{{ route('reporte.estado') }}" method="GET">
                        <!-- Opciones de Reporte Desplegables -->
                        <div class="mt-4">
                            <button type="button" class="btn btn-info w-100" data-bs-toggle="collapse" data-bs-target="#reporteOpciones" aria-expanded="false" aria-controls="reporteOpciones">
                                Opciones de Reporte
                            </button>
                            <div class="collapse mt-2" id="reporteOpciones">
                                <div class="list-group">
                                    <a href="{{ route('reporte.general') }}" class="list-group-item list-group-item-action">
                                        <i class="bi bi-file-earmark-text me-2"></i>Reporte General
                                    </a>
                                    <a href="{{ route('reporte.equipo_proyecto') }}" class="list-group-item list-group-item-action">
                                        <i class="bi bi-briefcase me-2"></i>Reporte Asignación de Equipos con sus Proyectos
                                    </a>
                                    <a href="{{ route('reporte.equipo_empleado') }}" class="list-group-item list-group-item-action">
                                        <i class="bi bi-person-check me-2"></i>Reporte Asignación de Equipos con sus Empleados
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Reporte por Estado -->
                        <div class="mt-4">
                            <button type="button" class="btn btn-primary w-100" data-bs-toggle="collapse" data-bs-target="#estadoFiltro" aria-expanded="false" aria-controls="estadoFiltro">
                                <i class="bi bi-graph-up-arrow me-2"></i>Reporte por Estado
                            </button>
                            <div class="collapse mt-2" id="estadoFiltro">
                                <div class="form-group">
                                    <label for="estado" class="form-label">Seleccione el Estado:</label>
                                    <select class="form-control custom-select" id="estado" name="estado">
                                        <option value="ASIGNACION ACTIVA">Asignación Activa</option>
                                        <option value="MANTENIMIENTO ACTIVO">Mantenimiento Activo</option>
                                        <option value="ASIGNACION FINALIZADA">Asignación Finalizada</option>
                                        <option value="MANTENIMIENTO FINALIZADO">Mantenimiento Finalizado</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success mt-2 w-100">Generar Reporte</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js dependencies (required for collapse) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
@stop

@section('css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
            border: none;
        }
        .card-body {
            padding: 2rem;
        }
        .table {
            margin-bottom: 0;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .thead-dark th {
            background-color: #343a40;
            color: #fff;
        }
        .btn-primary, .btn-success {
            border-radius: 30px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .plus-icon {
            font-size: 1.5em;
            margin-right: 5px;
        }
        .btn-group .btn {
            margin-right: 10px;
        }
        /* Estilos responsivos */
        @media (max-width: 767px) {
            .form-inline {
                flex-direction: column;
            }
            .form-inline .form-group {
                margin-bottom: 10px;
            }
            .btn-group {
                flex-direction: column;
                width: 100%;
            }
            .btn-group .btn {
                width: 100%;
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTable sin búsqueda ni botones
            var table = $('#mitabla').DataTable({
                order: [],  // Desactivar ordenamiento por defecto
                searching: false, // Desactivar búsqueda integrada
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json'
                },
                paging: @json(!empty($asignaciones)), // Paginación solo si hay asignaciones
                dom: 't', // Mostrar solo la tabla ('t')
            });

            // Guardar las filas originales para poder restaurarlas
            var originalRows = $('#tablaAsignaciones').html();

            // Funcionalidad de búsqueda con reglas personalizadas
            $('#searchInput').on('input', function() {
                var searchTerm = $(this).val().toLowerCase().trim();
                var hasResults = false;

                if (searchTerm.length === 0) {
                    // Restaurar filas originales si no hay términos de búsqueda
                    $('#tablaAsignaciones').html(originalRows);
                    $('#noResultsMessage').hide();
                    return;
                }

                $('#tablaAsignaciones tr').each(function() {
                    var rowText = $(this).text().toLowerCase().replace(/\s+/g, ' ');
                    if (rowText.includes(searchTerm)) {
                        $(this).show();
                        hasResults = true;
                    } else {
                        $(this).hide();
                    }
                });

                // Mostrar mensaje de "No se encontraron coincidencias"
                if (!hasResults) {
                    $('#tablaAsignaciones').html(
                        '<tr><td colspan="9" class="text-center">No se encontraron coincidencias.</td></tr>'
                    );
                } else {
                    $('#noResultsMessage').hide();
                }
            });
        });

        function confirmDelete(id, estado) {
            if (estado == 1) {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'El equipo está asignado y no se puede eliminar.',
                });
                return false;
            } else if (estado == 2) {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'El equipo está en mantenimiento y no se puede eliminar.',
                });
                return false;
            } else {
                return Swal.fire({
                    title: '¿Estás seguro?',
                    text: "No podrás revertir esta acción",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminarlo',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`delete-form-${id}`).submit();
                    }
                    return false;
                });
            }
        }

        function confirmFinalizar(id, estado, codEquipo, codEmpleado, codProyecto, descripcion, fechaInicio) {
            let mensaje = '';
            let form = document.getElementById('finalizar-form');

            if (estado == 1) {
                mensaje = '¿Estás seguro de que deseas finalizar la asignación de este equipo?';
            } else if (estado == 2) {
                mensaje = '¿Estás seguro de que deseas finalizar este mantenimiento?';
            }

            Swal.fire({
                title: mensaje,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, finalizarlo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.action = `${window.location.origin}/asignaciones/finalizar/${id}`;
                    document.getElementById('finalizar-cod-equipo').value = codEquipo;
                    document.getElementById('finalizar-cod-empleado').value = codEmpleado;
                    document.getElementById('finalizar-cod-proyecto').value = codProyecto;
                    document.getElementById('finalizar-descripcion').value = descripcion;
                    document.getElementById('finalizar-fecha-inicio').value = fechaInicio;
                    form.submit();
                }
            });
        }

        function confirmInactive(id, estado) {
            let mensaje = '';
            let form = document.getElementById('inactivar-form');

            if (estado == 3 || estado == 4) {
                mensaje = '¿Estás seguro de que deseas borrar esta asignación?';
                Swal.fire({
                    title: mensaje,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, borrarlo',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.action = `${window.location.origin}/asignaciones/inactivar/${id}`;
                        form.submit();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Solo se pueden borrar asignaciones finalizadas o mantenimientos finalizados.',
                });
            }
        }
    </script>
@stop
