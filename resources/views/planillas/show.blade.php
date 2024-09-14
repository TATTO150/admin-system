@extends('adminlte::page')

@section('title', 'Detalle de Planilla')

@section('content_header')
    <h1>Detalle de Planilla</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-secondary">Planilla #{{ $planilla->COD_PLANILLA }}</h2>
                    <p><strong>Total Pagado:</strong> {{ $planilla->TOTAL_PAGADO }}</p>
                </div>
                <p><strong>Fecha de Pago:</strong> {{ \Carbon\Carbon::parse($planilla->FECHA_PAGADA)->format('Y-m-d') }}</p>

                <table class="table table-hover table-bordered mt-4">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Área</th>
                            <th>Cargo</th>
                            <th>Salario Base</th>
                            <th>Deducciones</th>
                            <th>Salario Neto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($empleados as $index => $empleado)
                            <tr>
                                <td>{{ $empleado->NOM_EMPLEADO }}</td>
                                <td>{{ $empleado->area->NOM_AREA }}</td>
                                <td>{{ $empleado->cargo->NOM_CARGO }}</td>
                                <td>{{ number_format($empleado->SALARIO_BASE, 2) }}</td>
                                <td>{{ number_format($empleado->DEDUCCIONES, 2) }}</td>
                                <td>{{ number_format($empleado->SALARIO_NETO, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('planillas.index') }}" class="btn btn-primary">Volver a Planillas</a>
                    <a href="{{ route('reporte.generar', ['id' => $planilla->COD_PLANILLA]) }}" class="btn btn-success" target="_blank">Generar Reporte</a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
            border: none;
        }
        .card-body {
            padding: 2rem;
        }
        .table {
            margin-bottom: 0;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .thead-dark th {
            background-color: #343a40;
            color: #fff;
        }
        .btn-primary, .btn-success {
            border-radius: 30px;
            font-weight: bold;
        }
    </style>
@stop
