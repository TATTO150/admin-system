@extends('adminlte::page')

@section('title', 'Crear Cargos')

@section('content_header')
    <h1>Crear Nuevo Cargo</h1>
@stop

@section('content')
   <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo proyecto</title>
    <!-- Agregar enlaces a los archivos CSS de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
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
            <form action="{{ route('cargos.insertar') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="NOM_CARGO" class="form-label">NOMBRE CARGOS</label>
                    <input type="text" class="form-control"  id="NOM_CARGO" name="NOM_CARGO" :value="old('NOM_CARGO')" >
                </div>
                <div class="mb-3">
                    <label for="SALARIOS" class="form-label">SALARIOS</label>
                    <input type="number" class="form-control" id="SALARIOS" name="SALARIOS">
                    
                </div>
                <div class="mb-3">
                    <label for="FUNCION_PRINCIPAL" class="form-label">FUNCION PRINCIPAL</label>
                    <input type="text" class="form-control" id="FUNCION_PRINCIPAL" name="FUNCION_PRINCIPAL">
                    
                </div>

                <button type="submit" class="btn btn-primary">CREAR CARGO</button>
                <a href="{{ route('cargos.index') }}" class="btn btn-secondary">CANCELAR</a>
            </form>
        </main>
    </div>
    
    <!-- Agregar enlaces a los archivos JS de Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop