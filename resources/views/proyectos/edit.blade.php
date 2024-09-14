@extends('adminlte::page')

@section('title', 'Editar Proyecto')

@section('content_header')
    <h1>EDITAR PROYECTO</h1>
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
            <form action="{{ route('proyectos.update', ['COD_PROYECTO' => $proyectos['COD_PROYECTO']]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="NOM_PROYECTO" class="form-label">NOMBRE PROYECTO</label>
                    <input type="text" class="form-control" id="NOM_PROYECTO" name="NOM_PROYECTO"
                           value="{{ old('NOM_PROYECTO', $proyectos['NOM_PROYECTO']) }}" readonly>
                </div>
                
                <div class="mb-3">
                    <label for="FEC_INICIO" class="form-label">FECHA DE INICIO</label>
                    <input type="date" class="form-control" id="FEC_INICIO" name="FEC_INICIO"
                           value="{{ old('FEC_INICIO', $proyectos['FEC_INICIO']) }}">
                </div>

                <div class="mb-3">
                    <label for="DESC_PROYECTO" class="form-label">DESCRIPCION PROYECTO</label>
                    <input type="text" class="form-control" id="DESC_PROYECTO" name="DESC_PROYECTO"
                           value="{{ old('DESC_PROYECTO', $proyectos['DESC_PROYECTO']) }}">
                    <small id="descProyectoCounter" class="form-text text-muted"></small>
                </div>

                <div class="mb-3">
                    <label for="PRESUPUESTO_INICIO" class="form-label">PRESUPUESTO INICIO</label>
                    <input type="text" class="form-control" id="PRESUPUESTO_INICIO" name="PRESUPUESTO_INICIO"
                           value="{{ old('PRESUPUESTO_INICIO', $proyectos['PRESUPUESTO_INICIO']) }}">
                </div>
                
                <div class="mb-3">
                    <label for="FEC_FINAL" class="form-label">FECHA FINAL</label>
                    <input type="date" class="form-control" id="FEC_FINAL" name="FEC_FINAL"
                           value="{{ old('FEC_FINAL', $proyectos['FEC_FINAL']) }}">
                </div>
                
                <div class="mb-3">
    <x-label for="ESTADO_PROYECTO" value="{{ __('ESTADO PROYECTO') }}" />
    <select id="ESTADO_PROYECTO" class="form-control" name="ESTADO_PROYECTO" required>
        @foreach($estadosProyecto as $estado)
            <option value="{{ $estado->ESTADO_PROYECTO }}" 
                @if(strtoupper(old('ESTADO_PROYECTO', $proyectos['ESTADO_PROYECTO'])) === strtoupper($estado->ESTADO_PROYECTO)) 
                    selected 
                @endif>
                {{ $estado->ESTADO_PROYECTO }}
            </option>
        @endforeach
    </select>
</div>

                
                <!-- Botón de Guardar Cambios -->
                <button type="submit" class="btn btn-primary">GUARDAR</button>
                <a href="{{ route('proyectos.index') }}" class="btn btn-secondary">CANCELAR</a>
            </form>
        </main>
    </div>
@stop

@section('css')
    <style>
        /* Estilo para la cabecera */
        .content-header h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
        }

        /* Estilo para el contenedor principal */
        .container {
            max-width: 800px;
            margin: auto;
        }

        /* Estilo para el formulario */
        form {
            background: #f9f9f9;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Estilo para los campos del formulario */
        .form-label {
            font-weight: 500;
            color: #333;
        }

        /* Estilo para los inputs y selects */
        .form-control {
            border-radius: 4px;
            border: 1px solid #ccc;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
        }

        /* Estilo para los botones */
        .btn {
            border-radius: 4px;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
            margin-left: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Estilo para los mensajes de error */
        .alert {
            border-radius: 4px;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .alert li {
            margin-bottom: 0.5rem;
        }
    </style>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const descProyectoInput = document.getElementById('DESC_PROYECTO');
            const descProyectoCounter = document.getElementById('descProyectoCounter');
            const maxLength = 255;

            descProyectoInput.addEventListener('input', function() {
                const currentLength = descProyectoInput.value.length;
                if (currentLength >= maxLength) {
                    descProyectoCounter.textContent = `Has alcanzado el límite máximo de ${maxLength} caracteres.`;
                } else {
                    descProyectoCounter.textContent = '';
                }
            });
        });
    </script>
@stop
