@extends('adminlte::page')

@section('title', 'Editar Planilla')

@section('content_header')
    <h1>Editar Planilla</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-secondary">Planilla #{{ $planillas->COD_PLANILLA }}</h2>
                    <p><strong>Total Pagado:</strong> {{ $planillas->TOTAL_PAGADO }}</p>
                </div>
                <p><strong>Fecha de Pago:</strong> {{ \Carbon\Carbon::parse($planillas->FECHA_PAGADA)->format('Y-m-d') }}</p>

                <form action="{{ route('empleados.eliminar') }}" method="POST">
                    @csrf
                    <table class="table table-hover table-bordered mt-4">
                        <thead class="thead-dark">
                            <tr>
                                <th></th>
                                <th>Nombre</th>
                                <th>Área</th>
                                <th>Cargo</th>
                                <th>Salario Base</th>
                                <th>Deducciones</th>
                                <th>Salario Neto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($empleados as $empleado)
                                <tr>
                                    <td><input type="checkbox" name="empleados[]" value="{{ $empleado->id }}"></td>
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
                    <button type="submit" class="btn btn-danger">Eliminar Seleccionados</button>
                    <a href="{{ route('planillas.index') }}" class="btn btn-primary">Volver a Planillas</a>
                </form>
                
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
