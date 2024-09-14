@extends('adminlte::page')
@section('title', 'Empleado Proyecto')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1>EMPLEADO PROYECTOS</h1>
@stop

@section('content')
<div class="container">

    <div class="card mt-3">
        <div class="card-header">
        </div>
        <div class="card-body">
            <table id="mitabla" class="table table-hover table-bordered">
                <thead class="thead-dark">
                    <tr>

                        <th>DNI</th>
                        <th>NOMBRE PROYECTO</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($empleadoProyectos as $empleadoProyecto)
                        <tr>
                            <td>{{ $empleadoProyecto->DNI_EMPLEADO }}</td>
                            <td>{{ $empleadoProyecto->proyectos->NOM_PROYECTO ?? 'Sin nombre' }}</td>
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection