@extends('adminlte::page')

@section('title', 'Listado de Empleados')

@section('content_header')
<h3 class="text-center mt-3" id="tituloActivos">Empleados</h3>
<h3 id="tituloInactivos" class="text-center mt-3" style="display: none;">Empleados Anteriores</h3>
@stop

@section('content')
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

    <!-- Mostrar mensajes de éxito -->
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
            });
        });
    </script>
@endif


   

    <!-- Card para empleados activos -->
    <div class="card mb-3 mt-4">
        <div class="card-header">
           <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
                <!-- Botones para crear empleado, ver inactivos y generar reporte -->
                <a class="btn btn-success text-white hover:bg-green-600" href="{{ route('empleados.crear') }}">Nuevo</a>
                <button id="toggleInactivos" class="btn btn-warning text-white hover:bg-blue-600">Ver Empleados Anteriores</button>
                <button id="reporteModalBtn" class="btn btn-primary bg-teal-500 text-white hover:bg-teal-600" data-toggle="modal" data-target="#pdfModal">Generar Reporte</button>
            </div>
            
        
            <!-- Formulario de búsqueda -->
        <form id="buscador-form" method="GET">
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="buscar" placeholder="Buscar..." name="buscar" value="{{ request()->input('buscar') }}">
            </div>
        </form>
        </div>
        
        <div class="card-body"  id="tablaActivos">
            <!-- Contenedor para la tabla con barra de desplazamiento horizontal -->
            <div class="table-responsive">
                <table  class="table table-hover table-bordered dt-responsive nowrap">
                    <thead class="thead-dark">
                        <tr>
                            <th>Acciones</th>
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Área</th>
                            <th>Ingreso</th>
                            <th>Correo</th>
                            <th>Dirección</th>
                            <th>Contrato</th>
                            <th>Licencia Vehicular</th>
                            <th>Tipo Empleado</th>
                            <th>Salario Base</th>
                            <th>Deducciones</th>
                            <th>Salario Neto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($empleadosActivos as $empleado)
                            <tr>
                                <td>
                                    <!-- Dropdown para acciones de empleado -->
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{ $empleado['COD_EMPLEADO'] }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $empleado['COD_EMPLEADO'] }}">
                                            <li><a class="dropdown-item" href="{{ route('empleados.edit', $empleado['COD_EMPLEADO']) }}">editar</a></li>
                                            <li>
                                                <form action="{{ route('empleados.desactivar', $empleado->COD_EMPLEADO) }}" method="POST" onsubmit="return confirm('¿Está seguro de que desea desactivar este empleado?');" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger">Eliminar</button>
                                                </form>
                                            </li>
                                         </ul>
                                    </div>
                                </td>
                                <td>{{ $empleado['DNI_EMPLEADO'] }}</td>
                                <td>{{ $empleado['NOM_EMPLEADO'] }}</td>
                                <td>{{ isset($cargos[$empleado['COD_CARGO']]) ? $cargos[$empleado['COD_CARGO']]->NOM_CARGO : 'Cargo no encontrado' }}</td>
                                <td>{{ isset($areas[$empleado['COD_AREA']]) ? $areas[$empleado['COD_AREA']]->NOM_AREA : 'Área no encontrada' }}</td>
                                <td>{{ $empleado['FEC_INGRESO_EMPLEADO'] }}</td>
                                <td>{{ $empleado['CORREO_EMPLEADO'] }}</td>
                                <td>{{ $empleado['DIRECCION_EMPLEADO'] }}</td>
                                <td>{{ $empleado['CONTRATO_EMPLEADO'] == 1 ? 'Sí' : 'No' }}</td>
                                <td>{{ $empleado['LICENCIA_VEHICULAR'] == 1 ? 'Sí' : 'No' }}</td>
                                <td>{{ isset($tipo[$empleado['COD_ESTADO_EMPLEADO']]) ? $tipo[$empleado['COD_ESTADO_EMPLEADO']]->ESTADO_EMPLEADO : 'N/A' }}</td>
                                <td>{{ $empleado['SALARIO_BASE'] }}</td>
                                <td>{{ $empleado['DEDUCCIONES'] }}</td>
                                <td>{{ $empleado['SALARIO_NETO'] }}</td>
                                <td>{{ $empleado['ESTADO_EMPLEADO'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Paginación -->
            <nav id="paginationExample" class="d-flex justify-content-center mt-3">
                <button id="prevPage" class="btn btn-outline-primary me-2">Anterior</button>
                <span id="currentPage" class="align-self-center"></span>
                <button id="nextPage" class="btn btn-outline-primary ms-2">Siguiente</button>
            </nav>
        </div>
    </div>

    <!-- Card para empleados inactivos -->
    <div class="card mb-3 d-none" id="tablaInactivos">
        

        
        <div class="card-body">
            <!-- Contenedor para la tabla con barra de desplazamiento horizontal -->
            <div class="table-responsive">
                <table id="tablaInactivos" class="table table-hover table-bordered dt-responsive nowrap">
                    <thead class="thead-dark">
                        <tr>
                            <th>Acciones</th>
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Ingreso</th>
                            <th>Salida</th>
                            <th>Correo</th>
                            <th>Dirección</th>
                            <th>Contrato</th>
                            <th>Tipo Empleado</th>
                            <th>Salario Base</th>
                            <th>Deducciones</th>
                            <th>Salario Neto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($empleadosInactivos as $empleado)
                            <tr>
                                <td class="d-flex justify-content-center">
                                    <!-- Formulario para restaurar empleado inactivo -->
                                    <form action="{{ route('empleados.restaurar', $empleado->COD_EMPLEADO) }}" method="POST" onsubmit="return confirm('¿Está seguro de que desea restaurar este empleado?');">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm">Restaurar</button>
                                    </form>
                                </td>
                                <td>{{ $empleado['DNI_EMPLEADO'] }}</td>
                                <td>{{ $empleado['NOM_EMPLEADO'] }}</td>
                                <td>{{ isset($cargos[$empleado['COD_CARGO']]) ? $cargos[$empleado['COD_CARGO']]->NOM_CARGO : 'N/A' }}</td>
                                <td>{{ $empleado['FEC_INGRESO_EMPLEADO'] }}</td>
                                <td>{{ $empleado['FECHA_SALIDA'] }}</td>
                                <td>{{ $empleado['CORREO_EMPLEADO'] }}</td>
                                <td>{{ $empleado['DIRECCION_EMPLEADO'] }}</td>
                                <td>{{ $empleado['CONTRATO_EMPLEADO'] == 1 ? 'Sí' : 'No' }}</td>
                                <td>{{ isset($tipo[$empleado['COD_ESTADO_EMPLEADO']]) ? $tipo[$empleado['COD_ESTADO_EMPLEADO']]->ESTADO_EMPLEADO : 'N/A' }}</td>
                                <td>{{ $empleado['SALARIO_BASE'] }}</td>
                                <td>{{ $empleado['DEDUCCIONES'] }}</td>
                                <td>{{ $empleado['SALARIO_NETO'] }}</td>
                                <td>{{ $empleado['ESTADO_EMPLEADO'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
             <!-- Paginación -->
             <nav id="paginationExample" class="d-flex justify-content-center mt-3">
                <button id="prevPage" class="btn btn-outline-primary me-2">Anterior</button>
                <span id="currentPage" class="align-self-center"></span>
                <button id="nextPage" class="btn btn-outline-primary ms-2">Siguiente</button>
            </nav>
        </div>
    </div>

    <!-- Modal para Generar Reportes -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Selector para tipo de reporte -->
                    <div class="mb-3">
                        <label for="tipoReporte" class="form-label">Tipo de Reporte</label>
                        <select id="tipoReporte" class="form-select">
                            <option value="">Seleccione una opción</option>
                            <option value="cargo">Por Cargo</option>
                            <option value="area">Por Área</option>
                            <option value="estado">Por Estado</option>
                            <option value="general">General</option>
                        </select>
                    </div>
                    <!-- Contenedores para filtros dependiendo del tipo de reporte -->
                    <div id="contenedorCargo" class="d-none">
                        <label for="cargoSelect" class="form-label">Cargo</label>
                        <select id="cargoSelect" class="form-select">
                            @foreach ($cargos as $cargo)
                                <option value="{{ $cargo->COD_CARGO }}">{{ $cargo->NOM_CARGO }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="contenedorArea" class="d-none">
                        <label for="areaSelect" class="form-label">Área</label>
                        <select id="areaSelect" class="form-select">
                            @foreach ($areas as $area)
                                <option value="{{ $area->COD_AREA }}">{{ $area->NOM_AREA }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="contenedorEstado" class="d-none">
                        <label for="estadoSelect" class="form-label">Estado</label>
                        <select id="estadoSelect" class="form-select">
                            <option value="ACTIVO">ACTIVO</option>
                            <option value="INACTIVO">INACTIVO</option>
                        </select>
                    </div>
                    
                    <!-- Botón para generar el reporte -->
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-primary" id="generarReporteBtn">Generar Reporte</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('css')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">
    <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

   
    
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#buscar').on('keyup', function() {
        var query = $(this).val().toLowerCase();
        $('#tablaActivos tbody tr').each(function() {
            var rowText = $(this).text().toLowerCase();
            if (rowText.indexOf(query) === -1) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    // Función para paginar la tabla
    function paginateTable(tableId, rowsPerPage) {
        const table = document.getElementById(tableId);
        const rows = table.querySelectorAll('tbody tr');
        const prevButton = document.getElementById('prevPage');
        const nextButton = document.getElementById('nextPage');
        const currentPageLabel = document.getElementById('currentPage');

        let currentPage = 1;
        const rowCount = rows.length;
        const pageCount = Math.ceil(rowCount / rowsPerPage);

        // Función para mostrar solo las filas de la página actual
        function showPage(page) {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            rows.forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });
            currentPageLabel.textContent = 'Pág. ' + page;
        }

        // Eventos de navegación
        prevButton.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        });

        nextButton.addEventListener('click', function() {
            if (currentPage < pageCount) {
                currentPage++;
                showPage(currentPage);
            }
        });

        // Mostrar la primera página al cargar
        showPage(currentPage);
    }

    // Llamar a la función de paginación al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        paginateTable('tablaEjemplo', 6); // Cambiar el número de filas por página si es necesario
    });

$(document).ready(function() {
    $('#buscar').on('keyup', function() {
        var query = $(this).val().toLowerCase();
        $('#tablaInactivos tbody tr').each(function() {
            var rowText = $(this).text().toLowerCase();
            if (rowText.indexOf(query) === -1) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });
});
</script>

<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="//code.jquery.com/jquery-3.7.0.js" type="text/javascript"></script>
<script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="//cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
<script src="//cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js" type="text/javascript"></script>
<script src="//cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
    <script>
        // Función para paginar la tabla
    function paginateTable(tableId, rowsPerPage) {
        const table = document.getElementById(tableId);
        const rows = table.querySelectorAll('tbody tr');
        const prevButton = document.getElementById('prevPage');
        const nextButton = document.getElementById('nextPage');
        const currentPageLabel = document.getElementById('currentPage');

        let currentPage = 1;
        const rowCount = rows.length;
        const pageCount = Math.ceil(rowCount / rowsPerPage);

        // Función para mostrar solo las filas de la página actual
        function showPage(page) {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            rows.forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });
            currentPageLabel.textContent = 'Pág. ' + page;
        }

        // Eventos de navegación
        prevButton.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        });

        nextButton.addEventListener('click', function() {
            if (currentPage < pageCount) {
                currentPage++;
                showPage(currentPage);
            }
        });

        // Mostrar la primera página al cargar
        showPage(currentPage);
    }

    // Llamar a la función de paginación al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        paginateTable('tablaInactivos', 6);
        paginateTable('tablaActivos', 6); // Cambiar el número de filas por página si es necesario
    });
        
        $(document).ready(function() {
            // Inicializar DataTables
            $('#mitabla, #mitablaInactivos').DataTable({
                responsive: false
            });

            // Alternar entre empleados activos e inactivos
            $('#toggleInactivos').click(function() {
                $('#tablaActivos').toggleClass('d-none');
                $('#tablaInactivos').toggleClass('d-none');
                $('#tituloActivos').toggle();  
                $('#tituloInactivos').toggle(); 
                $(this).text($(this).text() === 'Ver Empleados Anteriores' ? 'Ver Empleados Activos' : 'Ver Empleados Anteriores');
            });

            // Mostrar y ocultar contenedores según la selección
            $('#tipoReporte').change(function() {
                var tipo = $(this).val();
                if (tipo === 'cargo') {
                    $('#contenedorCargo').removeClass('d-none').addClass('d-block');
                    $('#contenedorArea').removeClass('d-block').addClass('d-none');
                    $('#contenedorEstado').removeClass('d-block').addClass('d-none');
                    $('#contenedorTipo').removeClass('d-block').addClass('d-none');
                } else if (tipo === 'area') {
                    $('#contenedorArea').removeClass('d-none').addClass('d-block');
                    $('#contenedorCargo').removeClass('d-block').addClass('d-none');
                    $('#contenedorEstado').removeClass('d-block').addClass('d-none');
                    $('#contenedorTipo').removeClass('d-block').addClass('d-none');
                } else if (tipo === 'estado') {
                    $('#contenedorEstado').removeClass('d-none').addClass('d-block');
                    $('#contenedorCargo').removeClass('d-block').addClass('d-none');
                    $('#contenedorArea').removeClass('d-block').addClass('d-none');
                    $('#contenedorTipo').removeClass('d-block').addClass('d-none');
                } else if (tipo === 'tipo') {
                    $('#contenedorTipo').removeClass('d-none').addClass('d-block');
                    $('#contenedorEstado').removeClass('d-block').addClass('d-none');
                    $('#contenedorCargo').removeClass('d-block').addClass('d-none');
                    $('#contenedorArea').removeClass('d-block').addClass('d-none');
                } else {
                    $('#contenedorCargo, #contenedorArea, #contenedorEstado').removeClass('d-block').addClass('d-none');
                }
            });

            // Botón Generar Reporte
            $('#generarReporteBtn').click(function() {
        var tipo = $('#tipoReporte').val();
        var id = '';
        var estado = '';
        var tipoE = '';
        if (tipo === 'cargo') {
            id = $('#cargoSelect').val();
        } else if (tipo === 'area') {
            id = $('#areaSelect').val();
        } else if (tipo === 'estado') {
            estado = $('#estadoSelect').val();
        } else if (tipo === 'tipo') {
            tipoE = $('#tipoSelect').val();
        }

        if (tipo && (tipo === 'general' || id || estado || tipoE)) {
    var url = '/empleados/pdf?tipo=' + tipo +
        (id ? '&id=' + id : '') +
        (estado ? '&estado=' + estado : '') +
        (tipoE ? '&tipoE=' + tipoE : '');

    // Usar AJAX para verificar si hay empleados antes de abrir la nueva pestaña
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            // Si la respuesta no es 200, manejar el error
            return response.json().then(data => {
                throw new Error(data.error);
            });
        }
        // Si la respuesta es correcta, abrir el PDF en una nueva pestaña
        window.open(url, '_blank');
    })
    .catch(error => {
        // Mostrar el mensaje de error si no se encontraron empleados
        alert(error.message);
    });
} else {
    alert('Por favor seleccione un tipo de reporte y el valor correspondiente.');
}

    });
        });

        // Confirmar eliminación
        function confirmDeletion() {
            return confirm('¿Estás seguro de que deseas eliminar este empleado?');
        }
    </script>
@stop
