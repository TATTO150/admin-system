@extends('adminlte::page')

@section('title', 'Editar Estado de Usuario')

@section('content_header')
    <h1 class="text-center">CREAR ESTADO DE USUARIO</h1>
@stop

@section('content')
    <div class="container d-flex justify-content-center">
        <div class="card shadow-sm p-4 mb-5 bg-white rounded" style="width: 50%;">
            <main class="mt-3">
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
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('estado_usuarios.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="ESTADO" class="form-label">ESTADO</label>
                        <input type="text" name="ESTADO" id="ESTADO" class="form-control" value="{{ old('ESTADO') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="DESCRIPCION" class="form-label">DESCRIPCIÓN</label>
                        <textarea name="DESCRIPCION" id="DESCRIPCION" class="form-control" required maxlength="255">{{ old('DESCRIPCION') }}</textarea>
                        <div id="charCount">0 / 255 caracteres</div> <!-- Añadido el contador de caracteres -->
                    </div>
                    <div class="d-grid gap-2 d-md-block">
                        <button type="button" id="submit-button" class="btn btn-primary">GUARDAR</button>
                        <a href="{{ route('estado_usuarios.index') }}" class="btn btn-secondary">CANCELAR</a>
                    </div>
                </form>
            </main>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css">
    <style>
        .card {
            margin-top: 20px;
        }
        .form-label {
            font-weight: bold;
        }
        .select2-container {
            width: 100% !important;
        }
    </style>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
          $(document).ready(function() {
            $('.select2').select2();

            $('#submit-button').on('click', function(event) {
                event.preventDefault(); // Evita que el formulario se envíe inmediatamente

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Se guardarán los cambios realizados!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si el usuario confirma, envía el formulario
                        $(this).closest('form').submit();
                    }
                });
            });

            // Contador de caracteres para el campo DESCRIPCION
            $('#DESCRIPCION').on('input', function() {
                const maxLength = $(this).attr('maxlength');
                const currentLength = $(this).val().length;
                $('#charCount').text(`${currentLength} / ${maxLength} caracteres`);
            });

            // Inicializar el contador de caracteres al cargar la página
            const initialLength = $('#DESCRIPCION').val().length;
            const maxLength = $('#DESCRIPCION').attr('maxlength');
            $('#charCount').text(`${initialLength} / ${maxLength} caracteres`);
        });
    </script>
@stop
