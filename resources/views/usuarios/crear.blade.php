@extends('adminlte::page')

@section('title', 'Crear Usuario')

@section('content_header')
    <h1 class="text-center">CREAR NUEVO USUARIO</h1>
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

                <form id="userForm" action="{{ route('usuarios.insertar') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="Usuario" class="form-label">USUARIO</label>
                        <input type="text" class="form-control" id="Usuario" name="Usuario" value="{{ old('Usuario') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="Nombre_Usuario" class="form-label">NOMBRE USUARIO</label>
                        <input type="text" class="form-control" id="Nombre_Usuario" name="Nombre_Usuario" value="{{ old('Nombre_Usuario') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="Estado_Usuario" class="form-label">ESTADO USUARIO</label>
                        <select id="Estado_Usuario" class="form-select select2" name="Estado_Usuario" required>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->COD_ESTADO }}" @if(old('Estado_Usuario') == $estado->COD_ESTADO) selected @endif>
                                    {{ $estado->ESTADO }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="Id_Rol" class="form-label">ROL</label>
                        <select id="Id_Rol" class="form-select select2" name="Id_Rol" required>
                            <option value="">{{ __('Seleccione un rol') }}</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->Id_Rol }}" @if(old('Id_Rol') == $role->Id_Rol) selected @endif>{{ $role->Rol }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="Correo_Electronico" class="form-label">CORREO ELECTRÓNICO</label>
                        <input type="email" class="form-control" id="Correo_Electronico" name="Correo_Electronico" value="{{ old('Correo_Electronico') }}" required>
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-primary" id="saveButton">GUARDAR</button>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">CANCELAR</a>
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

            $('#saveButton').on('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Deseas guardar este usuario?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#userForm').submit(); // Enviar el formulario si el usuario confirma
                    }
                });
            });
        });
    </script>
@stop
