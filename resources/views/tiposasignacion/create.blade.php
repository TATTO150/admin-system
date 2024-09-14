@extends('adminlte::page')

@section('title', 'Crear Tipo de Asignación')

@section('content_header')
    <h1 class="text-center">CREAR TIPO DE ASIGNACIÓN</h1>
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

                @if (session('success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: '{{ session('success') }}',
                        });
                    </script>
                @endif

                @if (session('error'))
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: '{{ session('error') }}',
                        });
                    </script>
                @endif

                <form action="{{ route('tiposasignacion.store') }}" method="POST" id="tipoAsignacionForm">
                    @csrf
                    <div class="mb-3">
                        <label for="TIPO_ASIGNACION" class="form-label">TIPO DE ASIGNACIÓN</label>
                        <input type="text" class="form-control" id="TIPO_ASIGNACION" name="TIPO_ASIGNACION" value="{{ old('TIPO_ASIGNACION') }}" required maxlength="20">
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-primary" id="saveBtn">GUARDAR</button>
                        <a href="{{ route('tiposasignacion.index') }}" class="btn btn-secondary">CANCELAR</a>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#TIPO_ASIGNACION').on('input', function() {
                let maxLength = 25;
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

            $('#saveBtn').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Quieres guardar los cambios?",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#tipoAsignacionForm').submit();
                    }
                });
            });
        });
    </script>
@stop