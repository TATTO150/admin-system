@extends('adminlte::page')

@section('title', 'Estados de Proyecto')

@section('content_header')
    <h1>ESTADOS DE PROYECTO</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('estado_proyecto.create') }}" class="btn btn-primary">NUEVO</a>
                    <!-- Botón para el reporte PDF -->
                    <a href="{{ route('estado_proyecto.pdf') }}" class="btn btn-info" target="_blank">REPORTE</a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ESTADO PROYECTO</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = 1;
                            @endphp
                            @foreach ($estados as $estado)
                                @if ($estado->ESTADO_PROYECTO != 'APERTURA')
                                    <tr>
                                        <td>{{ $contador }}</td>
                                        <td>{{ $estado->ESTADO_PROYECTO }}</td>
                                        <td>
                                            <a href="{{ route('estado_proyecto.edit', $estado->COD_ESTADO_PROYECTO) }}" class="btn btn-warning">EDITAR</a>
                                            <form action="{{ route('estado_proyecto.destroy', $estado->COD_ESTADO_PROYECTO) }}" method="POST" style="display:inline-block;" onsubmit="return confirmDelete();">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">ELIMINAR</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @php
                                        $contador++;
                                    @endphp
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDelete() {
                event.preventDefault();
                const form = event.target;
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás recuperar este estado!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminarlo',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                });
            @endif
        </script>
    @endpush
@stop
