@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1 class="text-center">EDITAR USUARIO</h1>
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

                <form action="{{ route('usuarios.update', ['Id_usuario' => $usuario['Id_usuario']]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="Usuario" class="form-label">USUARIO</label>
                        <input type="text" class="form-control" id="Usuario" name="Usuario"
                               value="{{ old('Usuario', $usuario['Usuario']) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="Nombre_Usuario" class="form-label">NOMBRE USUARIO</label>
                        <input type="text" class="form-control" id="Nombre_Usuario" name="Nombre_Usuario"
                               value="{{ old('Nombre_Usuario', $usuario['Nombre_Usuario']) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="Estado_Usuario" class="form-label">ESTADO USUARIO</label>
                        <select id="Estado_Usuario" class="form-control" name="Estado_Usuario" required>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->COD_ESTADO }}" 
                                    {{ ($usuario['Estado_Usuario'] == $estado->COD_ESTADO || $usuario['Estado_Usuario'] == $estado->ESTADO) ? 'selected' : '' }}>
                                    {{ $estado->ESTADO }}
                                </option>
                            @endforeach
                            <!-- Mostrar el estado actual si no coincide con ningún estado en la tabla de estados -->
                            @if(!in_array($usuario['Estado_Usuario'], $estados->pluck('COD_ESTADO')->toArray()))
                                <option value="{{ $usuario['Estado_Usuario'] }}" selected>{{ $usuario['Estado_Usuario'] }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="Id_Rol" class="form-label">ROL</label>
                        <select id="Id_Rol" name="Id_Rol" class="form-select select2" required>
                            <option value="">{{ __('Seleccione un rol') }}</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->Id_Rol }}" {{ old('Id_Rol', $usuario['Id_Rol']) == $role->Id_Rol ? 'selected' : '' }}>
                                    {{ $role->Rol }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="Fecha_Vencimiento" class="form-label">FECHA VENCIMIENTO</label>
                        <input type="text" class="form-control" id="Fecha_Vencimiento" name="Fecha_Vencimiento"
                               value="{{ $usuario['Fecha_Vencimiento'] }}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="Correo_Electronico" class="form-label">CORREO ELECTRÓNICO</label>
                        <input type="email" class="form-control" id="Correo_Electronico" name="Correo_Electronico"
                               value="{{ old('Correo_Electronico', $usuario['Correo_Electronico']) }}" required>
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <button type="button" id="submit-button" class="btn btn-primary">GUARDAR</button>
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
    });
    </script>
@stop
