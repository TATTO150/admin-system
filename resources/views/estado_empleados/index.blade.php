@extends('adminlte::page')

@section('title', 'Tipos de Empleado')

@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1 class="text-center">TIPOS DE EMPLEADO</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Mostrar errores -->
                @if ($errors->any())
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: '¡Error!',
                                html: '{!! implode('<br>', $errors->all()) !!}',
                            });
                        });
                    </script>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('estado_empleados.create') }}" class="btn btn-success">NUEVO</a>
                    <a href="{{ route('estado_empleados.pdf') }}" class="btn btn-primary" target="_blank">REPORTE</a>
                </div>

                <!-- Formulario de búsqueda -->
                <form id="buscador-form" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="buscar" placeholder="Buscar..." name="buscar" value="{{ request()->input('buscar') }}">
                    </div>
                </form>

                <table id="mitabla" class="table table-hover table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ACCIÓN</th>
                            <th>TIPO EMPLEADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(is_array($estados) || is_object($estados))
                            @foreach ($estados as $estado)
                                <tr>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{ $estado->COD_ESTADO_EMPLEADO }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                Acciones
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $estado->COD_ESTADO_EMPLEADO }}">
                                                <li><a class="dropdown-item" href="{{ route('estado_empleados.edit', $estado->COD_ESTADO_EMPLEADO) }}">EDITAR</a></li>
                                                <li>
                                                    <form action="{{ route('estado_empleados.destroy', $estado->COD_ESTADO_EMPLEADO) }}" method="POST" class="d-inline" onsubmit="return confirmDelete({{ $estado->COD_ESTADO_EMPLEADO }})">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item">ELIMINAR</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>{{ $estado->ESTADO_EMPLEADO }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="2" class="text-center">No se encontraron estados de empleado.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Paginación -->
        <nav id="paginationExample" class="d-flex justify-content-center mt-3 mb-5">
            <button id="prevPage" class="btn btn-outline-primary me-2">Anterior</button>
            <span id="currentPage" class="align-self-center"></span>
            <button id="nextPage" class="btn btn-outline-primary ms-2">Siguiente</button>
        </nav>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
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
        paginateTable('mitabla', 6); // Cambiar el número de filas por página si es necesario
    });
$(document).ready(function() {
    $('#buscar').on('keyup', function() {
        var query = $(this).val().toLowerCase();
        $('#mitabla tbody tr').each(function() {
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
    <script src="//code.jquery.com/jquery-3.6.0.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmDelete(codEstadoEmpleado) {
            return Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                return result.isConfirmed;
            });
        }

        new DataTable('#mitabla', {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json',
            },
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copy', text: '<i class="bi bi-clipboard-check-fill"></i>', titleAttr: 'Copiar', className: 'btn btn-secondary' },
                { extend: 'excel', text: '<i class="bi bi-file-earmark-spreadsheet"></i>', titleAttr: 'Exportar a Excel', className: 'btn btn-success' },
                { extend: 'csv', text: '<i class="bi bi-filetype-csv"></i>', titleAttr: 'Exportar a csv', className: 'btn btn-success' },
                { extend: 'pdf', text: '<i class="bi bi-file-earmark-pdf"></i>', titleAttr: 'Exportar a PDF', className: 'btn btn-danger' },
                { extend: 'print', text: '<i class="bi bi-printer"></i>', titleAttr: 'Imprimir', className: 'btn btn-info' },
            ],
        });
    </script>
@stop

