@extends('adminlte::page')

@section('title', 'Editar Cargo')

@section('content_header')
    <h1>EDITAR CARGO</h1>
@stop

@section('content')
    <div class="container">
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
            <form action="{{ route('cargos.update', ['COD_CARGO' => $cargos['COD_CARGO']]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="NOM_CARGO" class="form-label">NOMBRE CARGO</label>
                    <input type="text" class="form-control" id="NOM_CARGO" name="NOM_CARGO"
                           value="{{ $cargos['NOM_CARGO'] }}">
                </div>

                <div class="mb-3">
                    <label for="SALARIOS" class="form-label">SALARIOS</label>
                    <input type="number" class="form-control" id="SALARIOS" name="SALARIOS"
                           value="{{ $cargos['SALARIOS'] }}">
                </div>

                <div class="mb-3">
                    <label for="FUNCION_PRINCIPAL" class="form-label">FUNCION PRINCIPAL</label>
                    <input type="text" class="form-control" id="FUNCION_PRINCIPAL" name="FUNCION_PRINCIPAL"
                           value="{{ $cargos['FUNCION_PRINCIPAL'] }}">
                </div>

                
                <!-- Botón de Guardar Cambios -->
                <button type="submit" class="btn btn-primary">GUARDAR CAMBIOS</button>
                <a href="{{ route('cargos.index') }}" class="btn btn-secondary">CANCELAR</a>
            </form>
        </main>
        
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
