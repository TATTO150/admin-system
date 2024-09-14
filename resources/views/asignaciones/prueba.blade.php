@extends('adminlte::page')

@section('title', 'Asignaciones')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1 class="text-center">ASIGNACIONES</h1>
@stop

@section('content')
    <div class="container mt-4">
               <!-- Sección para mostrar mensajes de éxito o error -->
       @if(session('success'))
       <div class="alert alert-success alert-dismissible fade show" role="alert">
           {{ session('success') }}
           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
       </div>
   @endif
   @if(session('error'))
       <div class="alert alert-danger alert-dismissible fade show" role="alert">
           {{ session('error') }}
           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
       </div>
   @endif
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
                                        @if ($asignacion->COD_ESTADO_ASIGNACION != 3 && $asignacion->COD_ESTADO_ASIGNACION != 2)
                                        <a href="{{ route('asignaciones.gestionar', $asignacion->COD_ASIGNACION_EQUIPO) }}" class="btn btn-primary btn-sm">GESTIONAR</a>
                                        @endif
                                        @if ($asignacion->COD_ESTADO_ASIGNACION != 3)
                                        <button type="button" class="btn btn-danger btn-sm eliminar-btn" data-cod-asignacion="{{ $asignacion->COD_ASIGNACION_EQUIPO }}" data-estado="{{ $asignacion->COD_ESTADO_ASIGNACION }}">ELIMINAR</button>
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

    <!-- Modal para mostrar la descripción completa -->
    <div class="modal fade" id="descripcionModal" tabindex="-1" aria-labelledby="descripcionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descripcionModalLabel">Descripción Completa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="descripcionCompleta"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
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
                <div class="modal-body bg-dark text-white">
                    <form id="reporteForm" action="{{ route('asignaciones.general') }}" method="POST" target="_blank">
                        @csrf
                        <!-- Opciones de Reporte -->
                        <div class="mb-3">
                            <label for="reporteTipo" class="form-label">Tipo de Reporte</label>
                            <select id="reporteTipo" name="reporteTipo" class="form-control" required>
                                <option value="general">Reporte General</option>
                                <option value="tipo_asignacion">Por Tipo de Asignación</option>
                                <option value="estado_asignacion">Por Estado de Asignación</option>
                                <option value="proyecto">Por Proyecto</option>
                                <option value="empleado">Por Empleado</option>
                            </select>
                        </div>

                        <!-- Subopciones para Selección de Empleado -->
                        <div id="empleadoOptions" class="mb-3" style="display: none;">
                            <label for="empleado" class="form-label">Seleccione el Empleado</label>
                            <select id="empleado" name="empleado" class="form-control select2">
                                @foreach ($empleadosAsignados as $empleado)
                                    <option value="{{ $empleado->COD_EMPLEADO }}">{{ $empleado->NOM_EMPLEADO }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subopciones para Tipo de Asignación -->
                        <div id="tipoAsignacionOptions" class="mb-3" style="display: none;">
                            <label for="tipoAsignacion" class="form-label">Seleccione el Tipo de Asignación</label>
                            <select id="tipoAsignacion" name="tipoAsignacion" class="form-control select2">
                                @foreach ($tiposAsignacion as $tipo)
                                    <option value="{{ $tipo->COD_TIPO_ASIGNACION }}">{{ $tipo->TIPO_ASIGNACION }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subopciones para Estado de Asignación -->
                        <div id="estadoAsignacionOptions" class="mb-3" style="display: none;">
                            <label for="estadoAsignacion" class="form-label">Seleccione el Estado de Asignación</label>
                            <select id="estadoAsignacion" name="estadoAsignacion" class="form-control select2">
                                @foreach ($estadosAsignacion as $estado)
                                    <option value="{{ $estado->COD_ESTADO_ASIGNACION }}">{{ $estado->ESTADO }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subopciones para Selección de Proyecto -->
                        <div id="proyectoOptions" class="mb-3" style="display: none;">
                            <label for="proyecto" class="form-label">Seleccione el Proyecto</label>
                            <select id="proyecto" name="proyecto" class="form-control select2">
                                @foreach ($proyectosAsignados as $proyecto)
                                    <option value="{{ $proyecto->COD_PROYECTO }}">{{ $proyecto->NOM_PROYECTO }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 bg-dark text-white">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" form="reporteForm">Generar Reporte</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                placeholder: "Seleccione una opción",
                allowClear: true
            });

            // Inicializar DataTable
            var table = $('#mitabla').DataTable({
                order: [],
                searching: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json'
                },
                paging: true,
                dom: 'tip',
            });

            // Funcionalidad de búsqueda personalizada
            $('#searchInput').on('input', function() {
                var searchTerm = $(this).val().toLowerCase().trim();
                var hasResults = false;

                if (searchTerm.length === 0) {
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

                if (!hasResults) {
                    $('#tablaAsignaciones').html(
                        '<tr><td colspan="9" class="text-center">No se encontraron coincidencias.</td></tr>'
                    );
                } else {
                    $('#noResultsMessage').hide();
                }
            });

            // Manejo de selección de tipo de reporte
            $('#reporteTipo').on('change', function() {
                var tipo = $(this).val();
                $('#tipoAsignacionOptions, #estadoAsignacionOptions, #proyectoOptions, #empleadoOptions').hide();

                switch (tipo) {
                    case 'tipo_asignacion':
                        $('#tipoAsignacionOptions').show();
                        $('#reporteForm').attr('action', "{{ route('asignaciones.tipo_asignacion') }}");
                        break;
                    case 'estado_asignacion':
                        $('#estadoAsignacionOptions').show();
                        $('#reporteForm').attr('action', "{{ route('asignaciones.estado_asignacion') }}");
                        break;
                    case 'proyecto':
                        $('#proyectoOptions').show();
                        $('#reporteForm').attr('action', "{{ route('asignaciones.proyecto') }}");
                        break;
                    case 'empleado':
                        $('#empleadoOptions').show();
                        $('#reporteForm').attr('action', "{{ route('asignaciones.empleado') }}");
                        break;
                    default:
                        $('#reporteForm').attr('action', "{{ route('asignaciones.general') }}");
                }
            });

            // Evento para el botón eliminar
            $(document).on('click', '.eliminar-btn', function () {
                var codAsignacion = $(this).data('cod-asignacion');
                var estadoAsignacion = $(this).data('estado');

                if (estadoAsignacion == 2) {
                    Swal.fire({
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
                            eliminarAsignacion(codAsignacion);
                        }
                    });
                } else if (estadoAsignacion == 3) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Asignación ya eliminada',
                        text: 'Esta asignación ya está inactiva.',
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Acción no permitida',
                        text: 'No se puede eliminar las asignaciones activas.',
                    });
                }
            });

            function eliminarAsignacion(codAsignacion) {
                $.ajax({
                    url: '{{ route("asignaciones.eliminar", ":id") }}'.replace(':id', codAsignacion),
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response.message,
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON.message,
                        });
                    }
                });
            }
        });
    </script>
@stop
