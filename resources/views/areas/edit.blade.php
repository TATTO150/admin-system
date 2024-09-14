@extends('adminlte::page')

@section('title', 'Editar Area')

@section('content_header')
    <h1>EDITAR AREA</h1>
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
            <form action="{{ route('areas.update', ['COD_AREA' => $areas['COD_AREA']]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="NOM_AREA" class="form-label">NOMBRE AREA</label>
                    <input type="text" class="form-control" id="NOM_AREA" name="NOM_AREA"
                           value="{{ $areas['NOM_AREA'] }}" >
                </div>
                
                <!-- Botón de Guardar Cambios -->
                <button type="submit" class="btn btn-primary">GUARDAR CAMBIOS</button>
                <a href="{{ route('areas.index') }}" class="btn btn-secondary">CANCELAR</a>
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
