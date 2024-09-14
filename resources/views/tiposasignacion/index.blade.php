@extends('adminlte::page')

@section('title', 'Tipos de Asignación')

@section('content_header')
    <h1 class="text-center">Tipos de Asignación</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
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

                <!-- Botón para crear un nuevo tipo de asignación -->
                <div class="d-flex justify-content-between mb-4">
                    <a href="{{ route('tiposasignacion.create') }}" class="btn btn-success">NUEVO</a>
                    <a href="{{ route('tipo_asignacion.report') }}" class="btn btn-primary" target="_blank">REPORTE</a>
                </div>

                <!-- Tabla de tipos de asignación -->
                <table id="mitabla" class="table table-hover table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tipo de Asignación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tiposAsignacion as $tipo)
                            <tr>
                                <td>{{ $tipo->COD_TIPO_ASIGNACION }}</td>
                                <td>{{ $tipo->TIPO_ASIGNACION }}</td>
                                <td>
                                    <a href="{{ route('tiposasignacion.edit', $tipo->COD_TIPO_ASIGNACION) }}" class="btn btn-warning btn-sm">Editar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
       $(document).ready(function() {
    $('#mitabla').DataTable({
        paging: true,            // Activar la paginación
        searching: false,        // Desactivar la barra de búsqueda
        info: false,             // Desactivar la información de registros
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json',
            paginate: {
                first: 'Primero',
                last: 'Último',
                next: 'Siguiente',
                previous: 'Anterior'
            },
            lengthMenu: "Mostrar _MENU_ registros por página",
            zeroRecords: "No se encontraron resultados",
            infoEmpty: "No hay registros disponibles",
            infoFiltered: "(filtrado de _MAX_ registros totales)"
        }
    });
});

    </script>
@stop