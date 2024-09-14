@extends('adminlte::page')

@section('title', 'Areas')

@section('plugins.Sweetalert2', true)

@section('content_header')
<h1 class="text-center">AREAS</h1>
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
                    <a href="{{ route('areas.crear') }}" class="btn btn-success">NUEVA</a>
                    <a href="{{ route('areas.pdf') }}" class="btn btn-primary" target="_blank">REPORTE</a>
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
                            <th>NOMBRE ÁREA</th>
                            <th>ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(is_array($areas) || is_object($areas))
                            @foreach ($areas as $area)
                                <tr>
                                    <td>{{ $area['NOM_AREA'] }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{ $area['COD_AREA'] }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                Acciones
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $area['COD_AREA'] }}">
                                                <li><a class="dropdown-item" href="{{ route('areas.edit', $area['COD_AREA']) }}">EDITAR</a></li>
                                                <li>
                                                    <form action="{{ route('areas.destroy', $area['COD_AREA']) }}" method="POST" class="d-inline" onsubmit="return confirmDelete({{ $area['COD_AREA'] }})">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item">ELIMINAR</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="2" class="text-center">No se encontraron áreas.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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
        function confirmDelete(codArea) {
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
