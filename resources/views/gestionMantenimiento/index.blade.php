@extends('adminlte::page')

@section('title', 'Gestion Mantenimiento')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1>GESTION MANTENIMIENTO</h1>
@stop

@section('content')
    <div class="container-fluid">
        <main class="mt-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('mantenimientos.pdf') }}" class="btn btn-primary">PDF</a>
                <!-- Botón para regresar a mantenimientos.index -->
                <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Mantenimientos
                </a>
            </div>

            @foreach ($mantenimientosPorEstado as $estado => $mantenimientos)
                <h2>{{ $estado }}</h2>
                <table id="mitabla_{{ str_replace(' ', '_', strtolower($estado)) }}" class="table table-striped">
                    <thead>
                        <tr>
                            <th>EMPLEADO SOLICITANTE</th>
                            <th>EQUIPO</th>
                            <th>DESCRIPCION MANTENIMIENTO</th>
                            <th>FECHA INGRESO</th>
                            <th>FECHA FINAL PLANIFICADA</th>
                            <th>FECHA FINAL REAL</th>
                            <th>ESTADO MANTENIMIENTO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mantenimientos as $mantenimiento)
                            <tr>
                                <td>{{ $mantenimiento->empleado->NOM_EMPLEADO ?? 'No asignado' }}</td>
                                <td>{{ $mantenimiento->equipo->NOM_EQUIPO ?? 'No asignado' }}</td>
                                <td>{{ $mantenimiento->DESC_MANTENIMIENTO }}</td>
                                <td>{{ $mantenimiento->FEC_INGRESO }}</td>
                                <td>{{ $mantenimiento->FEC_FINAL_PLANIFICADA }}</td>
                                <td>{{ $mantenimiento->FEC_FINAL_REAL }}</td>
                                <td>{{ $mantenimiento->estado_mantenimiento->ESTADO ?? 'No asignado' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        </main>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
    <!-- Agrega el CDN de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
    <script>
        $(document).ready(function() {
            @foreach ($mantenimientosPorEstado as $estado => $mantenimientos)
                new DataTable('#mitabla_{{ str_replace(' ', '_', strtolower($estado)) }}', {
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json',
                    },
                    dom: 'Bfrtip',
                    buttons: [
                        { extend: 'copy', text: '<i class="bi bi-clipboard-check-fill"></i>', titleAttr: 'Copiar', className: 'btn btn-secondary' },
                        { extend: 'excel', text: '<i class="bi bi-file-earmark-spreadsheet"></i>', titleAttr: 'Exportar a Excel', className: 'btn btn-success' },
                        { extend: 'csv', text: '<i class="bi bi-filetype-csv"></i>', titleAttr: 'Exportar a csv', className: 'btn btn-success' },
                        { extend: 'pdf', text: '<i class="bi bi-file-earmark-pdf"></i>', titleAttr: 'Exportar a PDF', className: 'btn btn-danger' },
                        { extend: 'print', text: '<i class="bi bi-printer'></i>', titleAttr: 'Imprimir', className: 'btn btn-info' },
                    ],
                });
            @endforeach
        });
    </script>
@stop
