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
    <script>
        $(document).ready(function() {
            // Opcional: Código JavaScript adicional
        });
    </script>
@endsection
