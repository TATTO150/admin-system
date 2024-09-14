@extends('adminlte::page')

@section('title', 'Gestionar Pago de Planilla')

@section('content_header')
    <h1>Gestionar Planilla</h1>
@stop

@section('content')
    <div class="container">
        <main class="mt-3">
            <div class="card">
                
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                    <h5 class="card-title">Planilla #{{ $planilla->COD_PLANILLA }}</h5>
                    <p><strong>Proyecto:</strong> {{ $proyecto->NOM_PROYECTO ?? 'No disponible' }}</p>
                    <p><strong>Estado Actual:</strong> {{ $planilla->ESTADO_PLANILLA }}</p>
                    <p><strong>Periodo de pago:</strong> {{ $planilla->PERIODO_PAGO }}</p>
                    <p><strong>Fecha de Pago:</strong> {{ \Carbon\Carbon::parse($planilla->FECHA_PAGO)->format('Y/m/d') }}</p>
                    <p><strong>Total a Pagar:</strong> {{ $planilla->TOTAL_PAGAR }}</p>

                    <!-- Listado de empleados en el proyecto de la planilla -->
                    <h6>Empleados en esta Planilla:</h6>

                    <!-- Botón para añadir nuevo empleado -->
                    <a href="{{ route('empleados.generarPlanillaVista', ['COD_PROYECTO' => $planilla->COD_PROYECTO]) }}" class="btn btn-primary mb-3">Añadir Nuevo Empleado</a>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre del Empleado</th>
                                <th>Cargo</th>
                                <th>Salario Base</th>
                                <th>Deducciones</th>
                                <th>Salario Neto</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($empleados as $empleado)
                                <tr>
                                    <td>{{ $empleado->NOM_EMPLEADO }}</td>
                                    <td>{{ $empleado->cargo->NOM_CARGO ?? 'Cargo no disponible' }}</td>
                                    <td>{{ $empleado->SALARIO_BASE }}</td>
                                    <td>{{ $empleado->DEDUCCIONES }}</td>
                                    <td>{{ $empleado->SALARIO_NETO }}</td>
                                    <td>
                                        <a href="{{ route('empleados.edit', $empleado->COD_EMPLEADO) }}" class="btn btn-info btn-sm">Editar</a>
                                        <form action="{{ route('empleados.destroy', $empleado->COD_EMPLEADO) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Botones para marcar como pagado o mantener pendiente -->
                    <form action="{{ route('pagoPlanillas.pagar', $planilla->COD_PLANILLA) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-sm">Marcar como Pagado</button>
                    </form>

                   <a href="{{ route('planillas.edit', $planilla['COD_PLANILLA']) }}" class="btn btn-warning btn-sm">EDITAR PLANILLA</a>

                    <!-- Botón para generar reporte PDF -->
                    <form action="{{ route('planillaIndividual.pdf', $planilla->COD_PROYECTO) }}" method="GET" style="display: inline-block;" target="_blank">
                        <button type="submit" class="btn btn-secondary btn-sm mb-3" style="margin-top: 15px;">Generar Reporte PDF</button>
                    </form>



                </div>
            </div>
        </main>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
    </script>
@stop
