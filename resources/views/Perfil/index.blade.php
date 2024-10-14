@extends('adminlte::page')

@section('title', 'Editar Perfil')

@section('content_header')
    <h1>Editar Perfil</h1>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        {{-- Mostrar mensajes de éxito --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Mostrar mensajes de error --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Formulario de actualización de perfil --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Actualizar Información de Perfil</h3>
            </div>
            <form action="{{ route('Perfil.update') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="Correo_Electronico">Correo Electrónico</label>
                        <input type="email" name="Correo_Electronico" id="Correo_Electronico" class="form-control @error('Correo_Electronico') is-invalid @enderror" value="{{ old('Correo_Electronico', $user->Correo_Electronico) }}" required>
                        @error('Correo_Electronico')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="Usuario">Nombre de Usuario</label>
                        <input type="text" name="Usuario" id="Usuario" class="form-control @error('Usuario') is-invalid @enderror" value="{{ old('Usuario', $user->Usuario) }}" required>
                        @error('Usuario')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="Nombre_Usuario">Nombre Completo</label>
                        <input type="text" name="Nombre_Usuario" id="Nombre_Usuario" class="form-control @error('Nombre_Usuario') is-invalid @enderror" value="{{ old('Nombre_Usuario', $user->Nombre_Usuario) }}" required>
                        @error('Nombre_Usuario')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                </div>
            </form>
        </div>
         {{-- Formulario de cambio de contraseña --}}
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Cambiar Contraseña</h3>
    </div>
    <form action="{{ route('Perfil.updatePassword') }}" method="POST">
        @csrf
        <div class="card-body">
            {{-- Contraseña actual --}}
            <div class="form-group">
                <label for="current_password">Contraseña Actual</label>
                <div class="input-group">
                    <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                    <div class="input-group-append">
                        <span class="input-group-text" onclick="togglePasswordVisibility('current_password', this)">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                </div>
                @error('current_password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Nueva contraseña --}}
            <div class="form-group">
                <label for="new_password">Nueva Contraseña</label>
                <div class="input-group">
                    <input type="password" name="new_password" id="new_password" class="form-control @error('new_password') is-invalid @enderror" required>
                    <div class="input-group-append">
                        <span class="input-group-text" onclick="togglePasswordVisibility('new_password', this)">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                </div>
                @error('nueva contraseña')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Confirmar nueva contraseña --}}
            <div class="form-group">
                <label for="new_password_confirmation">Confirmar Nueva Contraseña</label>
                <div class="input-group">
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control @error('new_password_confirmation') is-invalid @enderror" required>
                    <div class="input-group-append">
                        <span class="input-group-text" onclick="togglePasswordVisibility('new_password_confirmation', this)">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                </div>
                @error('new_password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
        </div>
    </form>
</div>

         <!-- Botón para activar la autenticación de dos factores -->
    @if(is_null($user->two_factor_secret))
    <form action="{{ route('Perfil.enable2fa') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary">Activar Autenticación en Dos Factores</button>
    </form>
    @else
        <form action="{{ route('Perfil.disable2fa') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">Desactivar Autenticación en Dos Factores</button>
        </form>
    @endif
    </div>
</div>
@endsection

@section('css')
    <style>
        .card-title {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
    </style>
@endsection

@section('js')
    {{-- Script para alternar la visibilidad de las contraseñas --}}
<script>
    function togglePasswordVisibility(fieldId, eyeIcon) {
        var passwordField = document.getElementById(fieldId);
        var icon = eyeIcon.querySelector('i');

        // Cambiar entre tipo 'password' y 'text'
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection
