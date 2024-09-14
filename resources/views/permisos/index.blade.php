@extends('adminlte::page')

@section('title', 'Permisos')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1 class="text-center">MÓDULO PERMISO</h1>
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
                    <a href="{{ route('permisos.crear') }}" class="btn btn-success">NUEVO</a>
                    <a href="{{ route('permisos.pdf') }}" class="btn btn-primary" target="_blank">REPORTE</a>
                </div>
                
                <!-- Campo de búsqueda -->
                <div class="mb-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar permiso...">
                </div>
                
                <div style="overflow-x: auto;">
                    <table id="mitabla" class="table table-hover table-bordered" style="width: 100%; margin: auto;">
                        <thead class="thead-dark">
                            <tr>
                                <th>Rol</th>
                                <th>Objeto</th>
                                <th>Permiso Inserción</th>
                                <th>Permiso Eliminación</th>
                                <th>Permiso Actualización</th>
                                <th>Permiso Consultar</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaPermisos">
                            @if(is_array($permisos) || is_object($permisos))
                                @foreach ($permisos as $permiso)
                                    <tr>
                                        <td>{{ $roles[$permiso['Id_Rol']]->Rol ?? 'N/A' }}</td>
                                        <td>{{ $objetos[$permiso['Id_Objeto']]->Objeto ?? 'N/A' }}</td>
                                        <td>{{ $permiso['Permiso_Insercion'] }}</td>
                                        <td>{{ $permiso['Permiso_Eliminacion'] }}</td>
                                        <td>{{ $permiso['Permiso_Actualizacion'] }}</td>
                                        <td>{{ $permiso['Permiso_Consultar'] }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{ $permiso['COD_PERMISOS'] }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Acciones
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $permiso['COD_PERMISOS'] }}">
                                                    <li><a class="dropdown-item" href="{{ route('permisos.edit', $permiso['COD_PERMISOS']) }}">Editar</a></li>
                                                    <li>
                                                        <form action="{{ route('permisos.destroy', $permiso['COD_PERMISOS']) }}" method="POST" class="d-inline" id="delete-form-{{ $permiso['COD_PERMISOS'] }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item" onclick="confirmDeletion('{{ $permiso['COD_PERMISOS'] }}')">Eliminar</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">No se encontraron permisos.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="//code.jquery.com/jquery-3.7.0.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Guardar las filas originales para poder restaurarlas
            var originalRows = $('#tablaPermisos').html();

            // Funcionalidad de búsqueda personalizada
            $('#searchInput').on('input', function() {
                var searchTerm = $(this).val().toLowerCase().trim();
                var hasResults = false;

                // Restaurar filas originales cada vez que cambia el término de búsqueda
                $('#tablaPermisos').html(originalRows);

                if (searchTerm.length > 0) {
                    $('#tablaPermisos tr').each(function() {
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
                        $('#tablaPermisos').html(
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
                    info: "Mostrando _START_ a _END_ de _TOTAL_ permisos",
                    infoEmpty: "Mostrando 0 a 0 de 0 permisos",
                    lengthMenu: "",
                    zeroRecords: "No se encontraron coincidencias",
                },
                pageLength: 10,
                lengthChange: false, // Deshabilita el selector "Show entries"
                paging: true,
                searching: false, // Deshabilita la búsqueda interna de DataTables
                ordering: true,
                info: true,
                autoWidth: false,
            });
        });

        function confirmDeletion(COD_PERMISOS) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('permisos.destroy', '') }}/' + COD_PERMISOS,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: 'El permiso ha sido eliminado correctamente.',
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: '¡Protegido!',
                                text: 'No se puede eliminar este permiso porque está protegido.',
                            });
                        }
                    });
                }
            });
        }
    </script>
@stop
