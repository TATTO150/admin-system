@extends('adminlte::page')

@section('title', 'Editar Estado de Asignación')

@section('content_header')
    <h1 class="text-center">EDITAR ESTADO DE ASIGNACIÓN</h1>
@stop

@section('content')
    <div class="container d-flex justify-content-center">
        <div class="card shadow-sm p-4 mb-5 bg-white rounded" style="width: 50%;">
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

                <form action="{{ route('estado_asignacion.update', $estado->COD_ESTADO_ASIGNACION) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="ESTADO" class="form-label">ESTADO</label>
                        <input type="text" id="ESTADO" name="ESTADO" class="form-control" value="{{ old('ESTADO', $estado->ESTADO) }}" placeholder="Ingrese el estado" required>
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-primary">ACTUALIZAR</button>
                        <a href="{{ route('estado_asignacion.index') }}" class="btn btn-secondary">CANCELAR</a>
                    </div>
                </form>
            </main>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card {
            margin-top: 20px;
        }
        .form-label {
            font-weight: bold;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
            });
        @endif
    </script>
@stop
