@extends('adminlte::page')

@section('title', 'Usuario')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1 class="text-center">MÓDULO USUARIO</h1>
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
                    <a href="{{ route('usuarios.crear') }}" class="btn btn-success">NUEVO</a>
                    <a href="{{ route('usuarios.pdf') }}" class="btn btn-primary" target="_blank">REPORTE</a>
                </div>
                <!-- Campo de búsqueda -->
                <div class="mb-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar usuario...">
                </div>
                
                <div style="overflow-x: auto;">
                    <table id="mitabla" class="table table-hover table-bordered" style="width: 75%; margin: auto;">
                        <thead class="thead-dark">
                            <tr>
                                <th>Usuario</th>
                                <th>Nombre Usuario</th>
                                <th>Estado Usuario</th>
                                <th>Rol</th>
                                <th>Fecha Última Conexión</th>
                                <th>Cantidad Ingresos</th>
                                <th>Fecha Vencimiento</th>
                                <th>Correo Electrónico</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tablaUsuarios">
                            @php
                                $index = 1;
                            @endphp
                            @if(is_array($usuarios) || is_object($usuarios))
                                @foreach ($usuarios as $usuario)
                                    <tr>
                                        <td>{{ $usuario['Usuario'] }}</td>
                                        <td>{{ $usuario['Nombre_Usuario'] }}</td>
                                        <td>
                                            @if($usuario->estado)
                                                {{ $usuario->estado->ESTADO }}
                                            @else
                                                {{ $usuario->Estado_Usuario }}
                                            @endif
                                        </td>
                                        <td>{{ $usuario->rol ? $usuario->rol->Rol : 'Sin rol asignado' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($usuario['Fecha_Ultima_Conexion'])->format('Y/m/d') }}</td>
                                        <td>{{ $usuario['Primer_Ingreso'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($usuario['Fecha_Vencimiento'])->format('Y/m/d') }}</td>
                                        <td>{{ $usuario['Correo_Electronico'] }}</td>
                                        <td>
                                            @if ($index != 1)
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{ $usuario['Id_usuario'] }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Acciones
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $usuario['Id_usuario'] }}">
                                                        <li><a class="dropdown-item" href="{{ route('usuarios.edit', $usuario['Id_usuario']) }}">Editar</a></li>
                                                        <li>
                                                            <form action="{{ route('usuarios.destroy', $usuario['Id_usuario']) }}" method="POST" class="d-inline" id="delete-form-{{ $usuario['Id_usuario'] }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn" onclick="confirmDelete({{ $usuario->Id_usuario }})">Eliminar</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @php
                                        $index++;
                                    @endphp
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="text-center">No se encontraron usuarios.</td>
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
    var originalRows = $('#tablaUsuarios').html();

    // Funcionalidad de búsqueda personalizada
    $('#searchInput').on('input', function() {
        var searchTerm = $(this).val().toLowerCase().trim();
        var hasResults = false;

        // Restaurar filas originales cada vez que cambia el término de búsqueda
        $('#tablaUsuarios').html(originalRows);

        if (searchTerm.length > 0) {
            $('#tablaUsuarios tr').each(function() {
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
                $('#tablaUsuarios').html(
                    '<tr><td colspan="9" class="text-center">No se encontraron coincidencias.</td></tr>'
                );
            }
        }
    });
});

        function confirmDelete(userId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Si el usuario confirma la eliminación, envía el formulario
            document.getElementById('delete-form-' + userId).submit();
        }
    });
}
    </script>
@stop
