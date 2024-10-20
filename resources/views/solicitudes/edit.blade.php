@extends('adminlte::page')

@section('title', 'Editar Solicitud')

@section('content_header')
    <h1 class="text-center">EDITAR SOLICITUD DE COMPRA</h1>
@stop

@section('content')
<div class="container">

    @if (session('success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: '{{ session('success') }}',
                        });
                    </script>
                @endif
                @if (session('error'))
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: '{{ session('error') }}',
                        });
                    </script>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
    <form action="{{ route('solicitudes.update', $compra->COD_COMPRA) }}" method="POST" id="editForm">
        @csrf
        @method('PUT')

        <!-- Descripción -->
        <div class="mb-3">
            <label for="DESC_COMPRA" class="form-label">Descripción de la Compra</label>
            <textarea class="form-control" id="DESC_COMPRA" name="DESC_COMPRA" rows="3" required>{{ $compra->DESC_COMPRA }}</textarea>
        </div>

        <!-- Proyecto -->
        <div class="mb-3">
            <label for="COD_PROYECTO" class="form-label">Proyecto Asociado</label>
            <select class="form-select select2" id="COD_PROYECTO" name="COD_PROYECTO" required>
                @foreach ($proyectos as $proyecto)
                    <option value="{{ $proyecto->COD_PROYECTO }}" {{ $compra->COD_PROYECTO == $proyecto->COD_PROYECTO ? 'selected' : '' }}>
                        {{ $proyecto->NOM_PROYECTO }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Tipo de Compra -->
        <div class="mb-3">
            <label for="COD_TIPO" class="form-label">Tipo de Compra</label>
            <select class="form-select" id="COD_TIPO" name="COD_TIPO" required>
                <option value="1" {{ $compra->COD_TIPO == 1 ? 'selected' : '' }}>Contado</option>
                <option value="2" {{ $compra->COD_TIPO == 2 ? 'selected' : '' }}>Crédito</option>
            </select>
        </div>

        <!-- Total de Cuotas -->
        <div class="mb-3" id="totalCuotasDiv">
            <label for="TOTAL_CUOTAS" class="form-label">Número Total de Cuotas</label>
            <input type="number" class="form-control" id="TOTAL_CUOTAS" name="TOTAL_CUOTAS" 
                   value="{{ $compra->TOTAL_CUOTAS }}" min="1" {{ $compra->COD_TIPO == 1 ? 'readonly' : '' }}>
        </div>

        <!-- Precio por Cuota -->
        <div class="mb-3">
            <label for="PRECIO_CUOTA" class="form-label">Precio por Cuota</label>
            <input type="number" step="0.01" class="form-control" id="PRECIO_CUOTA" name="PRECIO_CUOTA"
                   value="{{ $compra->PRECIO_CUOTA }}">
        </div>

        <!-- Fecha de Pago -->
        <div class="mb-3">
            <label for="FECHA_PAGO" class="form-label">Fecha de Pago</label>
            <input type="date" class="form-control" id="FECHA_PAGO" name="FECHA_PAGO"
                   value="{{ $compra->FECHA_PAGO }}">
        </div>

        <div class="d-grid gap-2 d-md-block">
            <button type="submit" class="btn btn-primary">GUARDAR</button>
            <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">CANCELAR</a>
        </div>
    </form>
</div>

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


@endsection
