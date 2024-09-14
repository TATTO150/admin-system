@extends('adminlte::page')

@section('title', 'Estados de Usuario')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1 class="text-center">ESTADOS DE USUARIO</h1>
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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('estado_usuarios.create') }}" class="btn btn-success">NUEVO</a>
                <a href="{{ route('estado_usuarios.reporte') }}" class="btn btn-primary" target="_blank">REPORTE</a>
            </div>

            <!-- Campo de búsqueda -->
            <div class="mb-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar estado de usuario...">
            </div>

            <div style="overflow-x: auto;">
                <table id="tblEstadoUsuarios" class="table table-hover table-bordered" style="width: 75%; margin: auto;">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Estado</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaEstados">
                        @foreach($estados as $estado)
                            <tr>
                                <td>{{ $estado->COD_ESTADO }}</td>
                                <td>{{ $estado->ESTADO }}</td>
                                <td>{{ $estado->DESCRIPCION }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{ $estado->COD_ESTADO }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $estado->COD_ESTADO }}">
                                            <li><a class="dropdown-item" href="{{ route('estado_usuarios.edit', $estado->COD_ESTADO) }}">Editar</a></li>
                                            <li>
                                                <form action="{{ route('estado_usuarios.destroy', $estado->COD_ESTADO) }}" method="POST" class="d-inline" id="delete-form-{{ $estado->COD_ESTADO }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="dropdown-item" onclick="confirmDeletion('{{ $estado->COD_ESTADO }}')">Eliminar</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
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
         $(document).ready(function() {
        // Guardar las filas originales para poder restaurarlas
        var originalRows = $('#tablaEstados').html();

        // Funcionalidad de búsqueda personalizada
        $('#searchInput').on('input', function() {
            var searchTerm = $(this).val().toLowerCase().trim();
            var hasResults = false;

            // Restaurar filas originales cada vez que cambia el término de búsqueda
            $('#tablaEstados').html(originalRows);

            if (searchTerm.length > 0) {
                $('#tablaEstados tr').each(function() {
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
                    $('#tablaEstados').html(
                        '<tr><td colspan="4" class="text-center">No se encontraron coincidencias.</td></tr>'
                    );
                }
            }
        });

        // Mostrar alertas según los mensajes de sesión en caso de redirección
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}',
            });
        @endif

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
            });
        @endif
    });

    function confirmDeletion(COD_ESTADO) {
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
                // Enviar el formulario de eliminación a través de AJAX
                $.ajax({
                    url: '{{ route('estado_usuarios.destroy', '') }}/' + COD_ESTADO,
                    type: 'POST',
                    data: {
                        '_method': 'DELETE',
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Mostrar alerta según el estado de la respuesta
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: response.message,
                            }).then(() => {
                                window.location.reload(); // Recargar la página para reflejar los cambios
                            });
                        } else if (response.status === 'error') {
                            Swal.fire({
                                icon: 'error',
                                title: '¡Error!',
                                text: response.message,
                            });
                        }
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: 'Ocurrió un error inesperado. Por favor, inténtalo nuevamente.',
                        });
                    }
                });
            }
        });
    }
    </script>
   
@stop
