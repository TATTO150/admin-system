@extends('adminlte::page')

@section('title', 'Bitácora')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1>MODULO BITACORA</h1>
@stop

@section('content')
<div class="container">
    <main class="mt-3">
        <a href="{{ route('bitacora.pdf') }}" class="btn btn-primary mb-3" target="_blank">REPORTE</a>

        <table id="mitabla" class="table table-striped">
            <thead>
                <tr>
                    <th>USUARIO</th>
                    <th>OBJETOS</th>                  
                    <th>DESCRIPCION</th>
                    <th>FECHA</th>
                </tr>
            </thead>
            <tbody id="tablaBitacora">
                @if($bitacoras)
                    @foreach ($bitacoras as $bitacora)
                        <tr>
                            <td>{{ $bitacora->user->Nombre_Usuario ?? 'N/A' }}</td>
                            <td>{{ $bitacora->Objeto->Objeto ?? 'Sin Objeto' }}</td>
                            <td>{{ $bitacora->Descripcion }}</td>
                            <td>{{ $bitacora->Fecha }}</td>
                        </tr>
                    @endforeach    
                @endif
            </tbody>
        </table>
    </main>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        /* Estilo general de la página */
        .container {
            padding: 20px;
            background-color: #f4f4f4; /* Fondo gris claro */
            border-radius: 10px; /* Bordes redondeados para el contenedor */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra sutil */
        }

        h1 {
            font-size: 2.2rem;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Estilo para las tablas */
        .table {
            margin-bottom: 0;
            border-radius: 8px;
            overflow: hidden;
            background-color: #ffffff; /* Fondo blanco para la tabla */
        }

        .table thead th {
            background-color: #343a40; /* Gris oscuro */
            color: #ffffff;
            text-align: center;
            padding: 12px;
        }

        .table tbody tr:hover {
            background-color: #e9ecef; /* Gris claro al pasar el cursor */
        }

        .table tbody td {
            vertical-align: middle;
            padding: 10px;
            text-align: center;
        }

        /* Estilo para los botones */
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 6px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
@stop

@section('js')
    <script src="//code.jquery.com/jquery-3.7.0.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Inicializar DataTables
            $('#mitabla').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json',
                    search: "Buscar en bitácora:", // Texto del campo de búsqueda
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    zeroRecords: "No se encontraron coincidencias",
                },
                pageLength: 10, // Número de filas por página
                lengthChange: false, // Deshabilita el selector "Mostrar entradas"
                paging: true, // Habilita la paginación
                searching: true, // Habilita la búsqueda
                ordering: true, // Habilita la ordenación de columnas
                info: true, // Muestra información de paginación
                autoWidth: false, // Evita el ajuste automático del ancho de las columnas
            });
        });
    </script>
@stop
