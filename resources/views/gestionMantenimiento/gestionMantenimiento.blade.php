@extends('adminlte::page')

@section('title', 'Gestionar Mantenimiento')

@section('content_header')
    <h1>Gestionar Mantenimiento</h1>
@stop

@section('content')
    <div class="container">
        <main class="mt-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Mantenimiento #{{ $mantenimiento->COD_MANTENIMIENTO }}</h5>
                    <p><strong>Empleado:</strong> {{ $mantenimiento->empleado->NOM_EMPLEADO ?? 'No disponible' }}</p>
                    <p><strong>Equipo:</strong> {{ $mantenimiento->equipo->NOM_EQUIPO ?? 'No disponible' }}</p>
                    <p><strong>Descripción:</strong> {{ $mantenimiento->DESC_MANTENIMIENTO }}</p>
                    <p><strong>Fecha de Ingreso:</strong> {{ $mantenimiento->FEC_INGRESO }}</p>
                    <p><strong>Fecha Final Planificada:</strong> {{ $mantenimiento->FEC_FINAL_PLANIFICADA }}</p>
                    <p><strong>Fecha Final Real:</strong> {{ $mantenimiento->FEC_FINAL_REAL }}</p>
                    <p><strong>Estado Actual:</strong> {{ $mantenimiento->estado_mantenimiento->ESTADO ?? 'No disponible' }}</p>

                    <!-- Formulario para actualizar el estado del mantenimiento -->
                    <form action="{{ route('mantenimientos.actualizarEstado', $mantenimiento->COD_MANTENIMIENTO) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label for="estado">Actualizar Estado</label>
                            <select id="estado" name="estado" class="form-control">
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->COD_ESTADO_MANTENIMIENTO }}" 
                                        {{ $mantenimiento->COD_ESTADO_MANTENIMIENTO == $estado->COD_ESTADO_MANTENIMIENTO ? 'selected' : '' }}>
                                        {{ $estado->ESTADO }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Actualizar Estado</button>
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
