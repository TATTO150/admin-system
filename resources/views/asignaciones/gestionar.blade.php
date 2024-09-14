@extends('adminlte::page')

@section('title', 'Gestionar Asignación')

@section('content_header')
    <h1>Gestionar Asignación</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                @if(session('success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: '{{ session('success') }}',
                        });
                    </script>
                @endif
                @if(session('error'))
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: '{{ session('error') }}',
                        });
                    </script>
                @endif

                <form id="gestionar-form" method="POST" action="{{ route('asignaciones.gestionar', $asignacion->COD_ASIGNACION_EQUIPO) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="equipo">Equipo</label>
                        <input type="text" id="equipo" class="form-control" value="{{ $asignacion->equipo->NOM_EQUIPO }}" disabled>
                    </div>

                    <div class="form-group">
                        <label for="empleado">Empleado</label>
                        <input type="text" id="empleado" class="form-control" value="{{ $asignacion->empleado->NOM_EMPLEADO }}" disabled>
                    </div>

                    @if ($asignacion->COD_PROYECTO != 0)
                        <div class="form-group">
                            <label for="proyecto">Proyecto</label>
                            <input type="text" id="proyecto" class="form-control" value="{{ $asignacion->proyectos->NOM_PROYECTO }}" disabled>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" class="form-control" disabled>{{ $asignacion->DESCRIPCION }}</textarea>
                    </div>

                    @if ($asignacion->COD_ESTADO_ASIGNACION != 3 && $asignacion->COD_ESTADO_ASIGNACION != 2)
                        <div class="form-group">
                            <label for="estado">Cambiar Estado de Asignación</label>
                            <select id="estado" name="estado" class="form-control">
                                @foreach($estadosAsignacion as $estado)
                                    @if ($estado->COD_ESTADO_ASIGNACION != 3) <!-- No mostrar estado inactivo -->
                                        <option value="{{ $estado->COD_ESTADO_ASIGNACION }}" {{ $asignacion->COD_ESTADO_ASIGNACION == $estado->COD_ESTADO_ASIGNACION ? 'selected' : '' }}>
                                            {{ $estado->ESTADO }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="finalizar" name="finalizar">
                            <label class="form-check-label" for="finalizar">Marcar como Finalizado</label>
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary mt-3" id="actualizar-estado-btn">Actualizar Estado</button>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const finalizarCheckbox = document.getElementById('finalizar');
            const estadoSelect = document.getElementById('estado');

            finalizarCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    estadoSelect.disabled = true;
                } else {
                    estadoSelect.disabled = false;
                }
            });
        });
    </script>
@stop