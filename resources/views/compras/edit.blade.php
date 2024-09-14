@extends('adminlte::page')

@section('title', 'Editar Libro Diario')

@section('content_header')
    <h1></h1>
@stop

@section('content')

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Comprar</title>
    <!-- Agregar enlaces a los archivos CSS de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 2rem;
            background-color: rgb(53, 98, 245);
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-label {
            font-weight: bold;
        }
        .form-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .btn-custom {
            width: 100%;
            
        }
        .mb-3, .mt-4, .text-black {
            margin-bottom: 1rem !important;
        }
    </style>
</head>


<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h2>REGISTRAR LIBRO DIARIO</h2>
            </div>
            <main>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('compras.update', ['COD_COMPRA' => $compras['COD_COMPRA']]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="DESC_COMPRA" class="form-label">DESCRIPCION COMPRA</label>
                        <input type="text" class="form-control" id="DESC_COMPRA" name="DESC_COMPRA"
                        value="{{ $compras['DESC_COMPRA'] }}">
                    </div>

                    <div class="mt-4 text-black">
                        <x-label for="COD_PROYECTO" value="{{ __('NOMBRE PROYECTO') }}" />
                        <select id="COD_PROYECTO" name="COD_PROYECTO" class="block mt-1 w-full" required>
                            <option value="">{{ __('Seleccione un proyecto') }}</option>
                            @foreach ($proyectos as $Proyecto)
                                <option value="{{ $Proyecto->COD_PROYECTO}}">{{ $Proyecto->NOM_PROYECTO}}</option>
                            @endforeach
                        </select>
                    </div>
                
                    <div class="mb-3">
                        <x-label for="TIP_COMPRA" value="{{ __('TIPO COMPRA') }}" />
                        <select id="TIP_COMPRA" class="block mt-1 w-full " name="TIP_COMPRA" required>
                            <option value="CREDITO" @if(strtoupper(old('TIP_COMPRA', $compras['TIP_COMPRA'])) === 'CREDITO') selected @endif>CREDITO</option>
                            <option value="CONTADO" @if(strtoupper(old('TIP_COMPRA', $compras['TIP_COMPRA'])) === 'CONTADO') selected @endif>CONTADO</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="PRECIO_VALOR" class="form-label">PRECIO </label>
                        <input type="number" class="form-control" id="PRECIO_VALOR" name="PRECIO_VALOR"
                        value="{{ $compras['PRECIO_VALOR'] }}">
                    </div>
               
                
                    <!-- Botón de Guardar Cambios -->
                    <button type="button" class="btn btn-primary btn-custom" onclick="confirmSubmit()">GUARDAR CAMBIOS</button>
                    <a href="{{ route('gastos.index') }}" class="btn btn-secondary btn-custom mt-2">CANCELAR</a>
                </form>
            </main>
        </div>    
    </div>
    
    <!-- Agregar enlaces a los archivos JS de Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmSubmit() {
            if (confirm('¿Está seguro que desea guardar este registro?')) {
                document.getElementById('compraForm').submit();
            }
        }
    </script>
</body>
</html>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
