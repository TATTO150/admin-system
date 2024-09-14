@extends('adminlte::page')

@section('title', 'Editar Empleado Planilla')

@section('content_header')
    <h1>EDITAR EMPLEADO PLANILLA</h1>
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
            <form action="{{ route('empleado_planillas.update', ['COD_EMPLEADO_PLANILLA' => $empleado_planillas['COD_EMPLEADO_PLANILLA']]) }}" method="POST">
                @csrf
                @method('PUT')
    

                <div class="mt-4 text-black">
                    <x-label for="COD_EMPLEADO" value="{{ __('NOM_EMPLEADO') }}" />
                    <select id="COD_EMPLEADO" name="COD_EMPLEADO" class="block mt-1 w-full" required>
                        <option value="">{{ __('Seleccione un empleado') }}</option>
                        @foreach ($empleados as $empleado)
                            <option value="{{ $empleado->COD_EMPLEADO }}">{{ $empleado->NOM_EMPLEADO }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4 text-black">
                    <x-label for="COD_PLANILLA" value="{{ __('PLANILLA') }}" />
                    <select id="COD_PLANILLA" name="COD_PLANILLA" class="block mt-1 w-full" required>
                        <option value="">{{ __('Seleccione una planilla') }}</option>
                        @foreach ($planillas as $planilla)
                            <option value="{{ $planilla->COD_PLANILLA }}">{{ $planilla->COD_PLANILLA }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                   <label for="SALARIO_BASE" class="form-check-label">SALARIO BASE</label>
                   <input type="number" class="form-check-input" id="SALARIO_BASE" name="SALARIO_BASE"
                          value="{{ $empleado_planillas['SALARIO_BASE'] }}"step="0.01">
                </div>

                <div class="mb-3">
                   <label for="DEDUCCIONES" class="form-check-label">DEDUCCIONES</label>
                   <input type="number" class="form-check-input" id="DEDUCCIONES" name="DEDUCCIONES"
                       value="{{ $empleado_planillas['DEDUCCIONES'] }}"step="0.01">
                </div>
                
                <!-- Botón de Guardar Cambios -->
                <button type="submit" class="btn btn-primary">GUARDAR CAMBIOS</button>
                <a href="{{ route('empleado_planillas.index') }}" class="btn btn-secondary">CANCELAR</a>
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
