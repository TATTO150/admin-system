@extends('adminlte::page')

@section('title', 'Gestión de Solicitudes')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1>GESTIÓN DE SOLICITUDES</h1>
@stop

@section('content')
    <div class="container">
        <!-- Mostrar errores -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <main class="mt-3">
            <h2>Solicitudes en Espera</h2>
            @if($solicitudesEspera->isNotEmpty())
                <table id="tablaEspera" class="table table-hover table-bordered dt-responsive nowrap">
                    <thead class="thead-dark">
                        <tr>
                            <th>SOLICITANTE</th>
                            <th>DESCRIPCIÓN SOLICITUD</th>
                            <th>PROYECTO ASOCIADO</th>
                            <th>TIPO COMPRA</th>
                            <th>CANTIDAD CUOTAS</th>
                            <th>PRECIO CUOTA</th>
                            <th>PRECIO TOTAL</th>
                            <th>ESTADO SOLICITUD</th>
                            <th>ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($solicitudesEspera as $solicitud)
                            <tr>
                                <td>{{ $usuarios[$solicitud->Id_usuario]->Usuario ?? 'N/A' }}</td>
                                <td>{{ $solicitud->DESC_COMPRA }}</td>
                                <td>{{ $proyectos[$solicitud->COD_PROYECTO]->NOM_PROYECTO ?? 'N/A' }}</td>
                                <td>{{ $tipos[$solicitud->COD_TIPO]->DESC_TIPO ?? 'N/A' }}</td>
                                <td>{{ $solicitud->PRECIO_CUOTA }}</td>
                                <td>{{ $solicitud->TOTAL_CUOTAS }}</td>
                                <td>{{ $solicitud->PRECIO_COMPRA }}</td>
                                <td>{{ $estados[$solicitud->COD_ESTADO]->DESC_ESTADO ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('gestionSolicitudes.gestionar', $solicitud->COD_COMPRA) }}" class="btn btn-warning btn-sm">GESTIONAR</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No hay solicitudes en espera.</p>
            @endif

            <div class="mb-4 mt-4">
                <h2 class="text-center">Otras Solicitudes</h2>
            </div>
            
            <table id="tablaOtras" class="table table-hover table-bordered dt-responsive nowrap">
                <thead class="thead-dark">
                    <tr>
                        <th>SOLICITANTE</th>
                        <th>DESCRIPCIÓN SOLICITUD</th>
                        <th>PROYECTO ASOCIADO</th>
                        <th>TIPO COMPRA</th>
                        <th>CANTIDAD CUOTAS</th>
                        <th>PRECIO CUOTA</th>
                        <th>PRECIO TOTAL</th>
                        <th>ESTADO SOLICITUD</th>
                        <th>ACCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($otrasSolicitudes as $solicitud)
                        <tr>
                            <td>{{ $usuarios[$solicitud->Id_usuario]->Usuario ?? 'N/A' }}</td>
                            <td>{{ $solicitud->DESC_COMPRA }}</td>
                            <td>{{ $proyectos[$solicitud->COD_PROYECTO]->NOM_PROYECTO ?? 'N/A' }}</td>
                            <td>{{ $tipos[$solicitud->COD_TIPO]->DESC_TIPO ?? 'N/A' }}</td>
                            <td>{{ $solicitud->PRECIO_CUOTA }}</td>
                            <td>{{ $solicitud->TOTAL_CUOTAS }}</td>
                            <td>{{ $solicitud->PRECIO_COMPRA }}</td>
                            <td>{{ $estados[$solicitud->COD_ESTADO]->DESC_ESTADO ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('gestionSolicitudes.gestionar', $solicitud->COD_COMPRA) }}" class="btn btn-warning btn-sm">GESTIONAR</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </main>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .card {
            margin-top: 20px;
        }
        .form-label {
            font-weight: bold;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }
        .table tbody + tbody {
            border-top: 2px solid #dee2e6;
        }
        .table-bordered {
            border: 1px solid #dee2e6;
        }
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }
        .table-bordered thead th,
        .table-bordered thead td {
            border-bottom-width: 2px;
        }
        .table-hover tbody tr:hover {
            color: #212529;
            background-color: rgba(0, 0, 0, 0.075);
        }
    </style>
@stop

@section('js')
    <script src="//code.jquery.com/jquery-3.7.0.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>   
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTable para Solicitudes en Espera
            @if($solicitudesEspera->isNotEmpty())
                $('#tablaEspera').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json',
                    },
                    dom: 'Bfrtip',
                    buttons: [
                        { extend: 'copy', text: '<i class="bi bi-clipboard-check-fill"></i>', titleAttr: 'Copiar', className: 'btn btn-secondary' },
                        { extend: 'excel', text: '<i class="bi bi-file-earmark-spreadsheet"></i>', titleAttr: 'Exportar a Excel', className: 'btn btn-success' },
                        { extend: 'csv', text: '<i class="bi bi-filetype-csv"></i>', titleAttr: 'Exportar a CSV', className: 'btn btn-success' },
                        { extend: 'pdf', text: '<i class="bi bi-file-earmark-pdf"></i>', titleAttr: 'Exportar a PDF', className: 'btn btn-danger' },
                        { extend: 'print', text: '<i class="bi bi-printer"></i>', titleAttr: 'Imprimir', className: 'btn btn-info' },
                    ],
                });
            @endif

            // Inicializar DataTable para Otras Solicitudes
            $('#tablaOtras').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json',
                },
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copy', text: '<i class="bi bi-clipboard-check-fill"></i>', titleAttr: 'Copiar', className: 'btn btn-secondary' },
                    { extend: 'excel', text: '<i class="bi bi-file-earmark-spreadsheet"></i>', titleAttr: 'Exportar a Excel', className: 'btn btn-success' },
                    { extend: 'csv', text: '<i class="bi bi-filetype-csv"></i>', titleAttr: 'Exportar a CSV', className: 'btn btn-success' },
                    { extend: 'pdf', text: '<i class="bi bi-file-earmark-pdf"></i>', titleAttr: 'Exportar a PDF', className: 'btn btn-danger' },
                    { extend: 'print', text: '<i class="bi bi-printer"></i>', titleAttr: 'Imprimir', className: 'btn btn-info' },
                ],
            });

            // SweetAlert2 para mensajes de éxito o error
            @if(session('success'))
                Swal.fire({
                    title: "¡Exitoso!",
                    text: "{{ session('success') }}",
                    icon: "success"
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    title: "¡Error!",
                    text: "{{ session('error') }}",
                    icon: "error"
                });
            @endif
        });
    </script>
@stop

