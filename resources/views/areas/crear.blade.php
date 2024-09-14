@extends('adminlte::page')

@section('title', 'Crear Area')

@section('content_header')
    <h1>INGRESAR NUEVA AREA</h1>
@stop

@section('content')
   <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INGRESAR AREA</title>
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
            <form action="{{ route('areas.insertar') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="NOM_AREA" class="form-label">NOMBRE AREA</label>
                    <input type="text" class="form-control"  id="NOM_AREA" name="NOM_AREA" :value="old('NOM_AREA')" >
                </div>
                
                <button type="submit" class="btn btn-primary">AGREGAR AREA</button>
                <a href="{{ route('areas.index') }}" class="btn btn-secondary">CANCELAR</a>
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