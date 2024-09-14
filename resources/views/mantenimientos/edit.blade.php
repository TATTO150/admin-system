@extends('adminlte::page')

@section('title', 'Editar Mantenimiento')

@section('content_header')
    <h1>EDITAR MANTENIMIENTO</h1>
@stop

@section('content')
    <div class="container">
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
            <form action="{{ route('mantenimientos.update', ['COD_MANTENIMIENTO' => $mantenimiento['COD_MANTENIMIENTO']]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="COD_EMPLEADO" class="form-label">EMPLEADO SOLICITANTE</label>
                    <input type="text" class="form-control" id="COD_EMPLEADO" name="COD_EMPLEADO"
                           value="{{ $mantenimiento['COD_EMPLEADO']->NOM_EMPLEADO ?? 'No asignado' }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="COD_ESTADO_MANTENIMIENTO" class="form-label">ESTADO MANTENIMIENTO</label>
                    <input type="text" class="form-control" id="COD_ESTADO_MANTENIMIENTO" name="COD_ESTADO_MANTENIMIENTO"
                           value="{{ $mantenimiento['COD_ESTADO_MANTENIMIENTO']->ESTADO ?? 'No asignado' }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="COD_EQUIPO" class="form-label">NOMBRE EQUIPO</label>
                    <input type="text" class="form-control" id="COD_EQUIPO" name="COD_EQUIPO"
                           value="{{ $mantenimiento['COD_EQUIPO']->NOM_EQUIPO ?? 'No asignado' }}" readonly>
                </div>
                <div class="mb-3">
                    <label for="DESC_MANTENIMIENTO" class="form-label">DESCRIPCION MANTENIMIENTO</label>
                    <input type="text" class="form-control" id="DESC_MANTENIMIENTO" name="DESC_MANTENIMIENTO"
                           value="{{ $mantenimiento['DESC_MANTENIMIENTO']->NOM_EQUIPO ?? 'No asignado' }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="FEC_FINAL_PLANIFICADA" class="form-label">FECHA FINAL PLANIFICADA</label>
                    <input type="date" class="form-control" id="FEC_FINAL_PLANIFICADA" name="FEC_FINAL_PLANIFICADA"
                           value="{{ $mantenimiento['FEC_FINAL_PLANIFICADA'] }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="FEC_FINAL_REAL" class="form-label">FECHA FINAL REAL</label>
                    <input type="date" class="form-control" id="FEC_FINAL_REAL" name="FEC_FINAL_REAL"
                           value="{{ $mantenimiento['FEC_FINAL_REAL'] }}">
                </div>

                <!-- Botón de Guardar Cambios -->
                <button type="submit" class="btn btn-primary">GUARDAR</button>
                <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary">CANCELAR</a>
            </form>
        </main>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
