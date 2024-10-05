@extends('adminlte::page')

@section('title', 'Equipo')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1 class="text-center">EQUIPO</h1>
@stop

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('equipos.crear') }}" class="btn btn-success">NUEVO</a>
                @if(request()->has('show_inactivos'))
                    <a href="{{ route('equipos.index') }}" class="btn btn-warning">Ocultar Equipos Inactivos</a>
                @else
                    <a href="{{ route('equipos.index', ['show_inactivos' => 'true']) }}" class="btn btn-secondary">Mostrar Equipos Inactivos</a>
                @endif
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reportModal">REPORTES</button>
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
            </div>

            <table id="mitabla" class="table table-hover table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>NOMBRE EQUIPO</th>
                        <th>TIPO EQUIPO</th>
                        <th>DESCRIPCIÓN EQUIPO</th>
                        <th>ESTADO EQUIPO</th>
                        <th>FECHA COMPRA</th>
                        <th>VALOR EQUIPO</th>
                        <th>ACCIÓN</th>
                    </tr>
                </thead>
                <tbody id="tablaEquipos">
                    @if(!empty($equipos) && (is_array($equipos) || is_object($equipos)))
                        @foreach ($equipos as $equipo)
                            <tr>
                                <td>{{ $equipo['NOM_EQUIPO'] }}</td>
                                <td>{{ $equipo['TIPO_EQUIPO_NOMBRE'] }}</td>
                                <td>{{ $equipo['DESC_EQUIPO'] }}</td>
                                <td>{{ $equipo['ESTADO_EQUIPO_NOMBRE'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($equipo['FECHA_COMPRA'])->format('d/m/Y') }}</td>
                                <td>{{ number_format($equipo['VALOR_EQUIPO'], 2, '.', ',') }}</td>
                                <td>
                                    @if ($equipo['COD_ESTADO_EQUIPO'] == 3)
                                        <button type="button" class="btn btn-warning btn-sm" onclick="confirmRestore({{ $equipo['COD_EQUIPO'] }})">Restaurar</button>
                                    @else
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{ $equipo['COD_EQUIPO'] }}" data-bs-toggle="dropdown" aria-expanded="false">Acciones</button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $equipo['COD_EQUIPO'] }}">
                                                <li><a class="dropdown-item" href="{{ route('equipos.edit', $equipo['COD_EQUIPO']) }}">EDITAR</a></li>
                                                <li>
                                                    <form action="{{ route('equipos.destroy', $equipo['COD_EQUIPO']) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item">ELIMINAR</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron equipos.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- Contenedor para el paginador personalizado -->
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination" id="pagination-container">
                        <!-- Los botones de paginación se generarán aquí con JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>


<!-- Modal para Reportes -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Generar Reporte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Contenedor para mostrar mensajes de error dentro del modal -->
                <div id="errorContainer" class="alert alert-danger d-none"></div>

                <div class="mb-3">
                    <label for="tipoReporte" class="form-label">Tipo de Reporte</label>
                    <select id="tipoReporte" class="form-select" onchange="mostrarFormulario()">
                        <option value="">Seleccione una opción</option>
                        <option value="estado">Por Estado</option>
                        <option value="fecha">Por Fecha</option>
                        <option value="general">General</option>
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <!-- Formulario para Reporte por Estado -->
                    <form id="reporteEstadoForm" class="d-none" onsubmit="return enviarFormulario(event, '{{ route('equipos.reporte.estado') }}')">
                        @csrf
                        <select name="estado" class="form-control mb-2" required>
                            @foreach($estados as $estado)
                                <option value="{{ $estado['COD_ESTADO_EQUIPO'] }}">{{ $estado['DESC_ESTADO_EQUIPO'] }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-warning">Generar Reporte por Estado</button>
                    </form>

                    <!-- Formulario para Reporte por Fecha -->
                    <form id="reporteFechaForm" class="d-none" onsubmit="return enviarFormulario(event, '{{ route('equipos.reporte.fecha') }}')">
                        @csrf
                        <div class="mb-2">
                            <label for="fecha_inicio">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label for="fecha_fin">Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-info">Generar Reporte por Fecha</button>
                    </form>

                    <!-- Formulario para Reporte General -->
                    <form id="reporteGeneralForm" class="d-none" onsubmit="return enviarFormulario(event, '{{ route('equipos.reporte.general') }}')">
                        @csrf
                        <button type="submit" class="btn btn-primary">Generar Reporte General de Equipos</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="restore-form" action="" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="//code.jquery.com/jquery-3.7.0.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            // Guardar las filas originales para poder restaurarlas
            var originalRows = $('#tablaEquipos').html();
    
            // Funcionalidad de búsqueda personalizada
            $('#searchInput').on('input', function() {
                var searchTerm = $(this).val().toLowerCase().trim();
                var hasResults = false;
    
                // Restaurar filas originales cada vez que cambia el término de búsqueda
                $('#tablaEquipos').html(originalRows);
    
                if (searchTerm.length > 0) {
                    $('#tablaEquipos tr').each(function() {
                        var rowText = $(this).text().toLowerCase().replace(/\s+/g, ' ');
                        if (rowText.includes(searchTerm)) {
                            $(this).show();
                            hasResults = true;
                        } else {
                            $(this).hide();
                        }
                    });
    
                    // Mostrar mensaje de "No se encontraron coincidencias" si no hay resultados
                    if (!hasResults) {
                        $('#tablaEquipos').html(
                            '<tr><td colspan="7" class="text-center">No se encontraron coincidencias.</td></tr>'
                        );
                    }
                }
            });
    
            // Configuración básica de DataTables sin el selector "Show entries"
            $('#mitabla').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json',
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    info: "Mostrando _START_ a _END_ de _TOTAL_ equipos",
                    infoEmpty: "Mostrando 0 a 0 de 0 equipos",
                    lengthMenu: "",
                    zeroRecords: "No se encontraron coincidencias",
                },
                pageLength: 5, // Configurar la cantidad de filas por página
                lengthChange: false, // Deshabilita el selector "Show entries"
                paging: true, // Habilita la paginación
                searching: false, // Deshabilita la búsqueda interna de DataTables
                ordering: true, // Habilita la ordenación de columnas
                info: true, // Muestra información de paginación
                autoWidth: false, // Evita el ajuste automático del ancho de las columnas
            });
        });
    
        // Función para confirmación de eliminación con SweetAlert
        window.confirmDelete = function(id) {
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
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        };
    
        // Función para confirmación de restauración con SweetAlert
        window.confirmRestore = function(equipoId) {
            Swal.fire({
                title: '¿Deseas restaurar este equipo?',
                text: "El equipo se restaurará a 'Sin Asignar'.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, restaurar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('restore-form');
                    form.action = `/equipos/restaurar/${equipoId}`;
                    form.submit();
                }
            });
        };
    </script>
    
    
@stop

    