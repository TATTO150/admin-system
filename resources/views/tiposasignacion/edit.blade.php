@extends('adminlte::page')

@section('title', 'Editar Tipo de Asignación')

@section('content_header')
    <h1 class="text-center">Editar Tipo de Asignación</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('tiposasignacion.update', $tipoAsignacion->COD_TIPO_ASIGNACION) }}" method="POST" id="tipoAsignacionForm">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="TIPO_ASIGNACION">Tipo de Asignación</label>
                        <input type="text" name="TIPO_ASIGNACION" id="TIPO_ASIGNACION" class="form-control @error('TIPO_ASIGNACION') is-invalid @enderror" value="{{ old('TIPO_ASIGNACION', $tipoAsignacion->TIPO_ASIGNACION) }}" required maxlength="20">
                        @error('TIPO_ASIGNACION')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-success" id="saveBtn">Actualizar</button>
                    <a href="{{ route('tiposasignacion.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#TIPO_ASIGNACION').on('input', function() {
                let maxLength = 20;
                let value = $(this).val();

                if (value.length > maxLength) {
                    $(this).val(value.substring(0, maxLength));
                    Swal.fire({
                        icon: 'warning',
                        title: 'Límite alcanzado',
                        text: 'No puedes ingresar más de 20 caracteres.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });

            $('#tipoAsignacionForm').on('submit', function(e) {
                $('#saveBtn').attr('disabled', true); // Desactivar el botón de guardar
                $('#saveBtn').text('Guardando...'); // Cambiar el texto del botón
            });
        });
    </script>
@stop

