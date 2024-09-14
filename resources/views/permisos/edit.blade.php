@extends('adminlte::page')

@section('title', 'Editar Permisos')

@section('content_header')
    <h1 class="text-center">EDITAR PERMISO</h1>
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
                
                <form action="{{ route('permisos.update', ['COD_PERMISOS' => $permisos['COD_PERMISOS']]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="Id_Rol" class="form-label">ROL</label>
                        <select id="Id_Rol" name="Id_Rol" class="form-select select2" required>
                            <option value="">{{ __('Seleccione un rol') }}</option>
                            @foreach ($roles as $Rol)
                                <option value="{{ $Rol->Id_Rol }}" @if($Rol->Id_Rol == $permisos['Id_Rol']) selected @endif>{{ $Rol->Rol }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="Id_Objeto" class="form-label">OBJETO</label>
                        <select id="Id_Objeto" name="Id_Objeto" class="form-select select2" required>
                            <option value="">{{ __('Seleccione un objeto') }}</option>
                            @foreach ($objetos as $Objeto)
                                <option value="{{ $Objeto->Id_Objetos }}" @if($Objeto->Id_Objetos == $permisos['Id_Objeto']) selected @endif>{{ $Objeto->Objeto }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="Permiso_Insercion" class="form-label">PERMISO INSERCIÓN</label>
                        <select id="Permiso_Insercion" class="form-select" name="Permiso_Insercion" required>
                            <option value="PERMITIDO" @if($permisos['Permiso_Insercion'] == 'PERMITIDO') selected @endif>PERMITIDO</option>
                            <option value="DENEGADO" @if($permisos['Permiso_Insercion'] == 'DENEGADO') selected @endif>DENEGADO</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="Permiso_Eliminacion" class="form-label">PERMISO ELIMINACIÓN</label>
                        <select id="Permiso_Eliminacion" class="form-select" name="Permiso_Eliminacion" required>
                            <option value="PERMITIDO" @if($permisos['Permiso_Eliminacion'] == 'PERMITIDO') selected @endif>PERMITIDO</option>
                            <option value="DENEGADO" @if($permisos['Permiso_Eliminacion'] == 'DENEGADO') selected @endif>DENEGADO</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="Permiso_Actualizacion" class="form-label">PERMISO ACTUALIZACIÓN</label>
                        <select id="Permiso_Actualizacion" class="form-select" name="Permiso_Actualizacion" required>
                            <option value="PERMITIDO" @if($permisos['Permiso_Actualizacion'] == 'PERMITIDO') selected @endif>PERMITIDO</option>
                            <option value="DENEGADO" @if($permisos['Permiso_Actualizacion'] == 'DENEGADO') selected @endif>DENEGADO</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="Permiso_Consultar" class="form-label">PERMISO CONSULTAR</label>
                        <select id="Permiso_Consultar" class="form-select" name="Permiso_Consultar" required>
                            <option value="PERMITIDO" @if($permisos['Permiso_Consultar'] == 'PERMITIDO') selected @endif>PERMITIDO</option>
                            <option value="DENEGADO" @if($permisos['Permiso_Consultar'] == 'DENEGADO') selected @endif>DENEGADO</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-primary">GUARDAR</button>
                        <a href="{{ route('permisos.index') }}" class="btn btn-secondary">CANCELAR</a>
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
        .select2-container {
            width: 100% !important;
        }
    </style>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@stop
