@extends('adminlte::page')

@section('title', 'Editar Gasto')

@section('content_header')
    <h1>EDITAR GASTO</h1>
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
            <form action="{{ route('gastos.update', ['COD_GASTO' => $gastos['COD_GASTO']]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="COD_GASTO" class="form-label">CODIGO GASTO</label>
                    <input type="text" class="form-control" id="COD_GASTO" name="COD_GASTO"
                           value="{{ $gastos['COD_GASTO'] }}" readonly>
                </div>
                <div class="mb-3">
                    <label for="DESC_GASTO" class="form-label">DESCRIPCION GASTO</label>
                    <input type="text" class="form-control" id="DESC_GASTO" name="DESC_GASTO"
                           value="{{ $gastos['DESC_GASTO'] }}" >
                </div>


                <div class="mb-3">
                    <label for="IMPUESTO_GASTO" class="form-label">IMPUESTO GASTO</label>
                    <input type="number" class="form-control" id="IMPUESTO_GASTO" name="IMPUESTO_GASTO"
                           value="{{ $gastos['IMPUESTO_GASTO'] }}">
                </div>

                
                <div class="mb-3">
    <x-label for="TIP_GASTO" value="{{ __('TIPO GASTO') }}" />
    <select id="TIP_GASTO" class="block mt-1 w-full " name="TIP_GASTO" required>
        <option value="FINANCIERO" @if(strtoupper(old('TIP_GASTO', $gastos['TIP_GASTO'])) === 'FINANCIERO') selected @endif>FINANCIERO</option>
        <option value="OPERATIVO" @if(strtoupper(old('TIP_GASTO', $gastos['TIP_GASTO'])) === 'OPERATIVO') selected @endif>OPERATIVO</option>
    </select> 
</div>
                <div class="mb-3">
                    <label for="FEC_REGISTRO" class="form-label">FECHA REGISTRO</label>
                    <input type="text" class="form-control" id="FEC_REGISTRO" name="FEC_REGISTRO"
                           value="{{ $gastos['FEC_REGISTRO'] }}">
                </div>
                
                <!-- Botón de Guardar Cambios -->
                <button type="submit" class="btn btn-primary">GUARDAR CAMBIOS</button>
                <a href="{{ route('gastos.index') }}" class="btn btn-secondary">CANCELAR</a>
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
