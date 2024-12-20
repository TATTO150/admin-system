@extends('adminlte::page')

@section('title', 'Gestionar Solicitud')

@section('content_header')
    <h1 class="text-center">GESTIONAR SOLICITUD</h1>
@stop

@section('content')
    <div class="container d-flex justify-content-center">
        <div class="card shadow-sm p-4 mb-5 bg-white rounded" style="width: 60%;">
            <main class="mt-3">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <script>
                        Swal.fire({
                            title: "¡Exitoso!",
                            text: "{{ session('success') }}",
                            icon: "success"
                        });
                    </script>
                @endif

                @if (session('error'))
                    <script>
                        Swal.fire({
                            title: "¡Error!",
                            text: "{{ session('error') }}",
                            icon: "error"
                        });
                    </script>
                @endif

                <div class="card-body">
                    <h6>Detalles de la Solicitud:</h6>
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Detalle</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Código Solicitud</td>
                                <td>{{ $solicitud->COD_COMPRA }}</td>
                            </tr>
                            <tr>
                                <td>Solicitante</td>
                                <td>{{ $usuarios[$solicitud->Id_usuario]->Usuario ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Descripción</td>
                                <td>{{ $solicitud->DESC_COMPRA }}</td>
                            </tr>
                            <tr>
                                <td>Proyecto</td>
                                <td>{{ $solicitud->proyecto->NOM_PROYECTO ?? 'No disponible' }}</td>
                            </tr>
                            <tr>
                                <td>Estado Actual</td>
                                <td>{{ $estados[$solicitud->COD_ESTADO]->DESC_ESTADO ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Presupuesto</td>
                                <td>{{ $solicitud->PRECIO_COMPRA }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Botones para aprobar o rechazar -->
                    <form action="{{ route('gestionSolicitudes.aprobar', $solicitud->COD_COMPRA) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-sm">APROBAR</button>
                    </form>

                    <!-- Botón para abrir el modal de rechazo -->
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal">
                        RECHAZAR
                    </button>

                    <!-- Modal de rechazo -->
                    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="rejectModalLabel">Rechazar Solicitud</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('gestionSolicitudes.rechazar', $solicitud->COD_COMPRA) }}" method="POST" id="rejectForm">
                                        @csrf
                                        @method('PATCH')
                                        <div class="form-group">
                                            <label for="motivo">Especifique el motivo de rechazo:</label>
                                            <textarea name="motivo" id="motivo" class="form-control" required></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" form="rejectForm" class="btn btn-danger">Enviar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
@stop

@section('css')
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
    </script>
@stop
