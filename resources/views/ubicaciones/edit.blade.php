@extends('adminlte::page')

@section('title', 'Editar Ubicacion')

@section('content_header')
    <h1>EDITAR UBICACION</h1>
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
            <form action="{{ route('ubicaciones.update', ['COD_UBICACION' => $ubicacion['COD_UBICACION']]) }}" method="POST">
                @csrf
                @method('PUT')

                </div>
                <div class="mb-3">
                    <label for="NOM_UBICACION" class="form-label">NOMBRE UBICACION</label>
                    <input type="text" class="form-control" id="NOM_UBICACION" name="NOM_UBICACION"
                           value="{{ old('NOM_UBICACION',$ubicacion['NOM_UBICACION']) }}">
                </div>

                <div class="mb-3">
                    <label for="DESCRIPCION" class="form-label">DESCRIPCION</label>
                    <input type="text" class="form-control" id="DESCRIPCION" name="DESCRIPCION"
                           value="{{ $ubicacion['DESCRIPCION'] }}">
                </div>
                
                <!-- Botón de Guardar Cambios -->
                <button type="submit" class="btn btn-primary">GUARDAR</button>
                <a href="{{ route('ubicaciones.index') }}" class="btn btn-secondary">CANCELAR</a>
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
