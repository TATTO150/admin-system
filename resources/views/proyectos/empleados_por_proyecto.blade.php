@extends('adminlte::page')

@section('title', 'Empleados del Proyecto')

@section('content_header')
    <h1>Empleados Asignados al Proyecto</h1>
@stop

@section('content')
    <div class="container">

        <!-- Botón de regresar -->
        <div class="mb-4">
            <a href="{{ route('proyectos.index') }}" class="btn btn-primary">Regresar</a>
        </div>

        <!-- Mostrar detalles del proyecto -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Detalles del Proyecto</h5>
            </div>
            <div class="card-body">
                <p><strong>Nombre:</strong> {{ $proyecto->NOM_PROYECTO }}</p>
                <p><strong>Fecha de Inicio:</strong> {{ $proyecto->FEC_INICIO }}</p>
                <p><strong>Fecha Final:</strong> {{ $proyecto->FEC_FINAL }}</p>
                <p><strong>Descripción:</strong> {{ $proyecto->DESC_PROYECTO }}</p>
                <p><strong>Presupuesto Inicial:</strong> {{ $proyecto->PRESUPUESTO_INICIO }}</p>
                <p><strong>Estado:</strong> {{ $proyecto->ESTADO_PROYECTO }}</p>
            </div>
        </div>

        <!-- Mostrar lista de empleados -->
        <h2>Empleados Asignados</h2>
        <!-- Botón para abrir el modal -->
        <div class="mb-4 text-end">
            <button type="button" class="btn btn-create" data-bs-toggle="modal" data-bs-target="#empleadosModal">
                Designar Empleados
            </button>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>DNI Empleado</th>
                    <th>Nombre del Empleado</th>
                    <th>Correo Electrónico</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($empleados as $empleado)
                    <tr>
                        <td>{{ $empleado->DNI_EMPLEADO }}</td>
                        <td>{{ $empleado->NOM_EMPLEADO }}</td>
                        <td>{{ $empleado->CORREO_EMPLEADO }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal para gestionar empleados -->
    <div class="modal fade" id="empleadosModal" tabindex="-1" aria-labelledby="empleadosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="empleadosModalLabel">Gestionar Empleados</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formGestionEmpleados" method="POST" action="{{ route('proyectos.gestionarEmpleados', ['proyecto' => $proyecto->COD_PROYECTO]) }}">
                        @csrf
                        @method('POST')
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Seleccionar</th>
                                    <th>DNI Empleado</th>
                                    <th>Nombre del Empleado</th>
                                    <th>Correo Electrónico</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($empleados as $empleado)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="empleados[]" value="{{ $empleado->DNI_EMPLEADO }}">
                                        </td>
                                        <td>{{ $empleado->DNI_EMPLEADO }}</td>
                                        <td>{{ $empleado->NOM_EMPLEADO }}</td>
                                        <td>{{ $empleado->CORREO_EMPLEADO }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-report">Designar Empleados</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .button-container {
            display: flex;
            gap: 10px; /* Espacio entre los botones */
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s, box-shadow 0.3s;
            color: #fff;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-create {
            background-color: #007bff; /* Azul brillante */
        }

        .btn-report {
            background-color: #28a745; /* Verde brillante */
        }

        .btn:hover {
            background-color: #0056b3; /* Azul más oscuro para CREAR */
        }

        .btn-report:hover {
            background-color: #218838; /* Verde más oscuro para REPORTE PDF */
        }

        .btn:active {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transform: translateY(2px);
        }
    </style>
@stop

@section('js')
    <!-- Incluir SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Alerta de confirmación al enviar el formulario
        document.getElementById('formGestionEmpleados').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevenir el envío del formulario
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Está seguro de que desea designar los empleados seleccionados a este proyecto?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, designar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit(); // Enviar el formulario si se confirma
                }
            });
        });

        // Mostrar una alerta de éxito si la operación fue exitosa
        @if(session('success'))
            Swal.fire({
                title: '¡Éxito!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
@stop
