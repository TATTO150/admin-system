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
                        <th>ACCIÓN</th>
                        <th>NOMBRE EQUIPO</th>
                        <th>TIPO EQUIPO</th>
                        <th>DESCRIPCIÓN EQUIPO</th>
                        <th>ESTADO EQUIPO</th>
                        <th>FECHA COMPRA</th>
                        <th>VALOR EQUIPO</th>
                    </tr>
                </thead>
                <tbody id="tablaEquipos">
                    @if(!empty($equipos) && (is_array($equipos) || is_object($equipos)))
                        @foreach ($equipos as $equipo)
                            <tr>
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
                                <td>{{ $equipo['NOM_EQUIPO'] }}</td>
                                <td>{{ $equipo['TIPO_EQUIPO_NOMBRE'] }}</td>
                                <td>{{ $equipo['DESC_EQUIPO'] }}</td>
                                <td>{{ $equipo['ESTADO_EQUIPO_NOMBRE'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($equipo['FECHA_COMPRA'])->format('d/m/Y') }}</td>
                                <td>{{ number_format($equipo['VALOR_EQUIPO'], 2, '.', ',') }}</td>
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
            <!-- Encabezado del Modal -->
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Generar Reporte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                <!-- Contenedor para mensajes de error -->
                <div id="errorContainer" class="alert alert-danger d-none"></div>

                <!-- Selección de Tipo de Reporte -->
                <div class="mb-3">
                    <label for="tipoReporte" class="form-label">Tipo de Reporte</label>
                    <select id="tipoReporte" class="form-select" onchange="mostrarFormulario()">
                        <option value="">Seleccione una opción</option>
                        <option value="estado">Por Estado</option>
                        <option value="fecha">Por Fecha</option>
                        <option value="general">General</option>
                    </select>
                </div>

                <!-- Formulario para Reporte por Estado -->
                <form id="reporteEstadoForm" class="d-none" method="POST" action="{{ route('equipos.reporte.estado') }}" onsubmit="return enviarFormulario(event)">
                    @csrf
                    <div class="mb-3">
                        <label for="estado" class="form-label">Seleccione el Estado</label>
                        <select name="estado" class="form-control" required>
                            @foreach($estados as $estado)
                                <option value="{{ $estado['COD_ESTADO_EQUIPO'] }}">{{ $estado['DESC_ESTADO_EQUIPO'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Generar Reporte por Estado</button>
                </form>

                <!-- Formulario para Reporte por Fecha -->
                <form id="reporteFechaForm" class="d-none" method="POST" action="{{ route('equipos.reporte.fecha') }}" onsubmit="return enviarFormulario(event)">
                    @csrf
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-info w-100">Generar Reporte por Fecha</button>
                </form>

                <!-- Formulario para Reporte General -->
                <form id="reporteGeneralForm" class="d-none" method="POST" action="{{ route('equipos.reporte.general') }}" onsubmit="return enviarFormulario(event)">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100">Generar Reporte General de Equipos</button>
                </form>
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
     function mostrarFormulario() {
        const tipoReporte = document.getElementById("tipoReporte").value;

        // Oculta todos los formularios inicialmente
        document.getElementById("reporteEstadoForm").classList.add("d-none");
        document.getElementById("reporteFechaForm").classList.add("d-none");
        document.getElementById("reporteGeneralForm").classList.add("d-none");

        // Muestra el formulario correspondiente según la opción seleccionada
        if (tipoReporte === "estado") {
            document.getElementById("reporteEstadoForm").classList.remove("d-none");
        } else if (tipoReporte === "fecha") {
            document.getElementById("reporteFechaForm").classList.remove("d-none");
        } else if (tipoReporte === "general") {
            document.getElementById("reporteGeneralForm").classList.remove("d-none");
        }
    }

    function enviarFormulario(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const url = form.getAttribute("action");

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => {
        if (!response.ok) {
            // Si hay un error en la respuesta, devuelve la respuesta en JSON
            return response.json().then(data => {
                throw new Error(data.error); // Lanza el error con el mensaje del servidor
            });
        }
        return response.blob(); // Continua con la generación del PDF si no hay errores
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        window.open(url, '_blank'); // Abre el PDF en una nueva pestaña
        window.URL.revokeObjectURL(url);
    })
    .catch(error => {
        // Muestra el mensaje de error en el modal
        const errorContainer = document.getElementById("errorContainer");
        errorContainer.classList.remove("d-none"); // Muestra el contenedor de error
        errorContainer.innerText = error.message; // Coloca el mensaje de error en el contenedor
    });
}
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
         // Comprueba si existe un mensaje de éxito en la sesión
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    // Comprueba si existe un mensaje de error en la sesión
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
    </script>
    
    
@stop

    