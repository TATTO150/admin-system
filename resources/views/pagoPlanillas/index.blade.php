@extends('adminlte::page')

@section('title', 'Planillas')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1>MODULO PLANILLA</h1>
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
            <h2>Planillas en Día de Pago</h2>
            @if($planillasHoy->isNotEmpty())
                <table id="mitablaHoy" class="table table-striped">
                    <thead>
                        <tr>
                            <th>NUMERO PLANILLA</th>
                            <th>PROYECTO</th>
                            <th>ESTADO PLANILLA</th>
                            <th>FECHA PAGO</th>
                            <th>TOTAL PAGAR</th>
                            <th>ACCION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($planillasHoy as $planilla)
                            @php
                                $proyecto = $proyectos->firstWhere('COD_PROYECTO', $planilla->COD_PROYECTO);
                                $fecha_pago = \Carbon\Carbon::parse($planilla->FECHA_PAGO);
                            @endphp
                            <tr>
                                <td>{{ $planilla->COD_PLANILLA }}</td>
                                <td>{{ $proyecto->NOM_PROYECTO ?? 'No asignado' }}</td>
                                <td>{{ $planilla->ESTADO_PLANILLA }}</td>
                                <td>{{ $fecha_pago->format('Y/m/d') }}</td>
                                <td>{{ $planilla->TOTAL_PAGAR }}</td>
                                <td>
                                    <a href="{{ route('pagoPlanillas.gestionar', $planilla->COD_PROYECTO) }}" class="btn btn-primary btn-sm">Gestionar</a>
                                    <a href="{{ route('pagoPlanillas.destroy', $planilla->COD_PLANILLA) }}" class="btn btn-primary btn-sm">Eliminar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No hay planillas para hoy.</p>
            @endif

            <h2>Planillas Generales</h2>
            <table id="mitablaGenerales" class="table table-striped">
                <thead>
                    <tr>
                        <th>NUMERO PLANILLA</th>
                        <th>PROYECTO</th>
                        <th>ESTADO PLANILLA</th>
                        <th>FECHA PAGO</th>
                        <th>TOTAL PAGAR</th>
                        <th>ACCION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($planillasGenerales as $planilla)
                        @php
                            $proyecto = $proyectos->firstWhere('COD_PROYECTO', $planilla->COD_PROYECTO);
                            $fecha_pago = \Carbon\Carbon::parse($planilla->FECHA_PAGO);
                        @endphp
                        <tr>
                            <td>{{ $planilla->COD_PLANILLA }}</td>
                            <td>{{ $proyecto->NOM_PROYECTO ?? 'No asignado' }}</td>
                            <td>{{ $planilla->ESTADO_PLANILLA }}</td>
                            <td>{{ $fecha_pago->format('Y/m/d') }}</td>
                            <td>{{ $planilla->TOTAL_PAGAR }}</td>
                            <td>
                                <a href="{{ route('pagoPlanillas.gestionar', $planilla->COD_PROYECTO) }}" class="btn btn-primary btn-sm">Gestionar</a>
                                <form action="{{ route('pagoPlanillas.destroy', $planilla->COD_PLANILLA) }}" method="POST" style="display: inline-block;" onsubmit="return confirmDeletion()">
                                    @csrf
                                    @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
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
            // Inicializar DataTable para Planillas en Día de Pago
            @if($planillasHoy->isNotEmpty())
                $('#mitablaHoy').DataTable({
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
            @endif

            // Inicializar DataTable para Planillas Generales
            $('#mitablaGenerales').DataTable({
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

            // SweetAlert2 para mensajes de éxito o error
            @if(session('success'))
                Swal.fire({
                    title: "Exitoso!",
                    text: "{{ session('success') }}",
                    icon: "success"
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    title: "Error!",
                    text: "{{ session('error') }}",
                    icon: "error"
                });
            @endif
        });
    </script>
@stop
