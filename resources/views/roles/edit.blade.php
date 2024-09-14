@extends('adminlte::page')

@section('title', 'Editar Rol')

@section('content_header')
    <h1 class="text-center">EDITAR ROL</h1>
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

                <form id="edit-form" action="{{ route('roles.update', ['Id_Rol' => $roles['Id_Rol']]) }}" method="POST">
                    @csrf
                    @method('PUT')

                   

                    <div class="mb-3">
                        <label for="Rol" class="form-label">NOMBRE ROL</label>
                        <input type="text" class="form-control" id="Rol" name="Rol"
                               value="{{ $roles['Rol'] }}" >
                    </div>

                    <div class="mb-3">
                        <label for="Descripcion" class="form-label">DESCRIPCIÓN</label>
                        <textarea class="form-control" id="Descripcion" name="Descripcion" maxlength="255">{{ $roles['Descripcion'] }}</textarea>
                        <div id="charCount">0 / 255 caracteres</div> <!-- Aquí se mostrará el conteo de caracteres -->
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-primary" id="submit-button">GUARDAR</button>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">CANCELAR</a>
                    </div>
                </form>
            </main>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css">
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
        // Añadir alerta de confirmación antes de enviar el formulario
        $('#submit-button').on('click', function(event) {
            event.preventDefault(); // Evitar que se envíe el formulario inmediatamente

            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Se guardarán los cambios en el rol!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, guardar cambios',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#edit-form').submit(); // Enviar el formulario si se confirma
                }
            });
        });

        // Contador de caracteres para el campo DESCRIPCION
        $('#Descripcion').on('input', function() {
            const maxLength = $(this).attr('maxlength');
            const currentLength = $(this).val().length;
            $('#charCount').text(`${currentLength} / ${maxLength} caracteres`);
        });

        // Inicializar el contador de caracteres al cargar la página
        const initialLength = $('#Descripcion').val().length;
        const maxLength = $('#Descripcion').attr('maxlength');
        $('#charCount').text(`${initialLength} / ${maxLength} caracteres`);
    });
</script>
    </script>
@stop
