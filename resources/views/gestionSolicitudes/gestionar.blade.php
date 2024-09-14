@extends('adminlte::page')

@section('title', 'Gestionar Solicitud')

@section('content_header')
    <h1 class="text-center">Gestionar Solicitud</h1>
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
                  
                 
                    <!-- Ejemplo de tabla -->
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
                                <td>{{ $solicitud->COD_SOLICITUD }}</td>
                            </tr>
                            <tr>
                                <td>Nombre Empleado</td>
                                <td>{{ $solicitud->empleado->NOM_EMPLEADO ?? 'No disponible' }}</td>
                            </tr>
                            <tr>
                                <td>Descripción</td>
                                <td>{{ $solicitud->DESC_SOLICITUD }}</td>
                            </tr>
                            <tr>
                                <td>Área</td>
                                <td>{{ $solicitud->area->NOM_AREA ?? 'No disponible' }}</td>
                            </tr>
                            <tr>
                                <td>Proyecto</td>
                                <td>{{ $solicitud->proyecto->NOM_PROYECTO ?? 'No disponible' }}</td>
                            </tr>
                            <tr>
                                <td>Estado Actual</td>
                                <td>{{ $solicitud->ESTADO_SOLICITUD }}</td>
                            </tr>
                            <tr>
                                <td>Presupuesto</td>
                                <td>{{ $solicitud->PRESUPUESTO_SOLICITUD }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Botones para aprobar o rechazar -->
                    <form action="{{ route('gestionSolicitudes.aprobar', $solicitud->COD_SOLICITUD) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-sm">Aprobar</button>
                    </form>

                    <form action="{{ route('gestionSolicitudes.rechazar', $solicitud->COD_SOLICITUD) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Estás seguro de que deseas rechazar esta solicitud?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm">Rechazar</button>
                    </form>
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
