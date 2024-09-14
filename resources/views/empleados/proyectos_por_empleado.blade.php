<!-- resources/views/empleados/proyectos.blade.php -->

@extends('adminlte::page')

@section('title', 'Proyectos del Empleado')

@section('content_header')
    <h1>Proyectos del Empleado</h1>
@stop

@section('content')
    <div class="container">
        <!-- Mostrar detalles del empleado -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Detalles del Empleado</h5>
            </div>
            <div class="card-body">
                <p><strong>DNI:</strong> {{ $empleado->DNI_EMPLEADO }}</p>
                <p><strong>Nombre:</strong> {{ $empleado->NOM_EMPLEADO }}</p>
                <p><strong>Correo Electrónico:</strong> {{ $empleado->CORREO_EMPLEADO }}</p>
                <p><strong>Dirección:</strong> {{ $empleado->DIRECCION_EMPLEADO }}</p>
                <p><strong>Fecha de Ingreso:</strong> {{ $empleado->FEC_INGRESO_EMPLEADO }}</p>
                <!-- Agrega más campos si es necesario -->
            </div>
        </div>

        <!-- Mostrar lista de proyectos -->
        <h2>Proyectos Asignados</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre del Proyecto</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha Final</th>
                    <th>Descripción del Proyecto</th>
                    <th>Presupuesto Inicial</th>
                    <th>Estado del Proyecto</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($proyectos as $proyecto)
                    <tr>
                        <td>{{ $proyecto->NOM_PROYECTO }}</td>
                        <td>{{ $proyecto->FEC_INICIO }}</td>
                        <td>{{ $proyecto->FEC_FINAL }}</td>
                        <td>{{ $proyecto->DESC_PROYECTO }}</td>
                        <td>{{ $proyecto->PRESUPUESTO_INICIO }}</td>
                        <td>{{ $proyecto->ESTADO_PROYECTO }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
@stop

@section('js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
@stop
