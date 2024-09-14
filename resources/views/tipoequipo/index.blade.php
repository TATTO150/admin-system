@extends('adminlte::page')

@section('title', 'Tipos de Equipo')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1 class="text-center">MÓDULO TIPO DE EQUIPO</h1>
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
                            title: 'No se puede eliminar',
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
                    <a href="{{ route('tipo_equipo.create') }}" class="btn btn-success">NUEVO</a>
                    <a href="{{ route('tipo_equipo.report') }}" class="btn btn-primary" target="_blank">REPORTE</a>
                </div>

                <div style="overflow-x: auto;">
                    <table id="mitabla" class="table table-hover table-bordered" style="width: 75%; margin: auto;">
                        <thead class="thead-dark">
                            <tr>
                                <th>Tipo de Equipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tipoEquipos as $tipoEquipo)
                                <tr>
                                    <td>{{ $tipoEquipo->TIPO_EQUIPO }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{ $tipoEquipo->COD_TIP_EQUIPO }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                Acciones
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $tipoEquipo->COD_TIP_EQUIPO }}">
                                                <li><a class="dropdown-item" href="{{ route('tipo_equipo.edit', $tipoEquipo->COD_TIP_EQUIPO) }}">Editar</a></li>
                                                <li>
                                                    <form action="{{ route('tipo_equipo.destroy', $tipoEquipo->COD_TIP_EQUIPO) }}" method="POST" class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item delete-btn">Eliminar</button>
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
        // Función para confirmar eliminación
    function confirmDeletion() {
        return Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            return result.isConfirmed;
        });
    }

    // Evento para manejar el click en el botón de eliminación
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();

        var form = $(this).closest('form');
        confirmDeletion().then((confirmed) => {
            if (confirmed) {
                form.submit();
            }
        });
    });

    // Mostrar alerta si hay un error de eliminación
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'No se puede eliminar',
            text: '{{ session('error') }}',
        });
    @endif
    </script>
@stop
