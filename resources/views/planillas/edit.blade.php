@extends('adminlte::page')

@section('title', 'EDITAR PLANILLA')

@section('content_header')
    <h1>EDITAR PLANILLA</h1>
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
        <form action="{{ route('planillas.update', ['COD_PLANILLA' => $planillas->COD_PLANILLA]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="COD_PLANILLA" class="form-label">NUMERO PLANILLA</label>
                <input type="number" class="form-control" id="COD_PLANILLA" name="COD_PLANILLA"
                       value="{{ $planillas->COD_PLANILLA }}" readonly>
            </div>

            <div class="mb-3">
                <label for="FECHA_PAGO" class="form-label">FECHA PAGADA</label>
                <input type="date" class="form-control" id="FECHA_PAGO" name="FECHA_PAGO"
                       value="{{ $planillas->FECHA_PAGO }}">
            </div>

            
            <div class="mb-3">
                <label for="TOTAL_PAGADO" class="form-label">TOTAL PAGADO</label>
                <input type="number" class="form-control" id="TOTAL_PAGADO" name="TOTAL_PAGADO"
                       value="{{ $planillas->TOTAL_PAGADO }}">
            </div>
            
            <!-- Botón de Guardar Cambios -->
            <button type="submit" class="btn btn-primary">GUARDAR</button>
            <a href="{{ route('planillas.index') }}" class="btn btn-secondary">CANCELAR</a>
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
