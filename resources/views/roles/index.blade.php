@extends('adminlte::page')

@section('title', 'Roles')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1 class="text-center">MÓDULO DE ROLES</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm" style="width: 90%; margin: auto;">
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

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('roles.crear') }}" class="btn btn-success">NUEVO</a>
                    <a href="{{ route('roles.pdf') }}" class="btn btn-primary" target="_blank">REPORTE</a>
                </div>
                
                <!-- Campo de búsqueda -->
                <div class="mb-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar rol...">
                </div>
                
                <div style="overflow-x: auto;">
                    <table id="mitabla" class="table table-hover table-bordered" style="width: 75%; margin: auto;">
                        <thead class="thead-dark">
                            <tr>
                                <th>Accion</th>
                                <th>Rol</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody id="tablaRoles">
                            @if(is_array($roles) || is_object($roles))
                                @foreach ($roles as $rol)
                                    <tr>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{ $rol['Id_Rol'] }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Acciones
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $rol['Id_Rol'] }}">
                                                    <li><a class="dropdown-item" href="{{ route('roles.edit', $rol['Id_Rol']) }}">Editar</a></li>
                                                    <li>
                                                        <form action="{{ route('roles.destroy', $rol['Id_Rol']) }}" method="POST" class="d-inline delete-form" data-rol="{{ $rol['Rol'] }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item btn-delete">Eliminar</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td>{{ $rol['Rol'] }}</td>
                                        <td>{{ $rol['Descripcion'] }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center">No se encontraron roles.</td>
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
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .card {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .table thead.thead-dark th {
            background-color: #343a40;
            color: white;
            text-align: center;
        }

        .table tbody td {
            text-align: center;
            vertical-align: middle;
        }

        .dropdown-menu {
            min-width: 100px;
        }

        .btn {
            border-radius: 20px;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
    </style>
@stop

@section('js')
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
            // Guardar las filas originales para poder restaurarlas
            var originalRows = $('#tablaRoles').html();

            // Funcionalidad de búsqueda personalizada
            $('#searchInput').on('input', function() {
                var searchTerm = $(this).val().toLowerCase().trim();
                var hasResults = false;

                // Restaurar filas originales cada vez que cambia el término de búsqueda
                $('#tablaRoles').html(originalRows);

                if (searchTerm.length > 0) {
                    $('#tablaRoles tr').each(function() {
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
                        $('#tablaRoles').html(
                            '<tr><td colspan="3" class="text-center">No se encontraron coincidencias.</td></tr>'
                        );
                    }
                }
            });
        });

        $(document).ready(function () {
            // Manejar el click en el botón de eliminar
            $('.btn-delete').on('click', function () {
                const form = $(this).closest('form');
                const rol = form.data('rol');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `El rol "${rol}" será eliminado de manera permanente.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@stop
