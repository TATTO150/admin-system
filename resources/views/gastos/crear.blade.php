@extends('adminlte::page')

@section('title', 'Crear gasto')

@section('content_header')
    <h1>CREAR NUEVO GASTO</h1>
@stop

@section('content')
   <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CREAR NUEVO GASTO</title>
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
            <form action="{{ route('gastos.insertar') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="DESC_GASTO" class="form-label">DESCRIPCIÓN GASTO</label>
                    <input type="text" class="form-control"  id="DESC_GASTO" name="DESC_GASTO" :value="old('DESC_GASTO')" >
                    
                </div>

                <div class="mb-3">
                    <label for="IMPUESTO_GASTO" class="form-label">IMPUESTO GASTO</label>
                    <input type="text" class="form-control" id="IMPUESTO_GASTO" name="IMPUESTO_GASTO">
                    
                </div>

                <div class="text-black">
                <x-label for="TIP_GASTO" value="{{ __('TIPO GASTO') }}" />
                <select id="TIP_GASTO" class="block mt-1 w-full" name="TIP_GASTO" required>
                    <option value="FINANCIERO" @if(old('TIP_GASTO') == 'FINANCIERO') selected @endif>FINANCIERO</option>
                    <option value="OPERATIVO" @if(old('TIP_GASTO') == 'OPERATIVO') selected @endif>OPERATIVO</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="FEC_REGISTRO" class="form-label">FECHA REGISTRO</label>
                    <input type="date" class="form-control" id="FEC_REGISTRO" name="FEC_REGISTRO">
                    
                </div>

                <button type="submit" class="btn btn-primary">CREAR GASTO</button>
                <a href="{{ route('gastos.index') }}" class="btn btn-secondary">CANCELAR</a>
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