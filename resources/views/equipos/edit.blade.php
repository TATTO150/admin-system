@extends('adminlte::page')

@section('title', 'Editar Equipo')

@section('content_header')
    <h1 class="text-center">EDITAR EQUIPO</h1>
@stop

@section('content')
    <div class="container d-flex justify-content-center">
        <div class="card shadow-sm p-4 mb-5 bg-white rounded" style="width: 50%;">
            <main class="mt-3">
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

                <form action="{{ route('equipos.update', ['COD_EQUIPO' => $equipo['COD_EQUIPO']]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="NOM_EQUIPO" class="form-label">NOMBRE EQUIPO</label>
                        <input type="text" class="form-control" id="NOM_EQUIPO" name="NOM_EQUIPO" value="{{ old('NOM_EQUIPO', $equipo['NOM_EQUIPO']) }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="COD_TIP_EQUIPO" class="form-label">TIPO EQUIPO</label>
                        <select class="form-select select2" id="COD_TIP_EQUIPO" name="COD_TIP_EQUIPO" required>
                            <option value="">{{ __('Seleccione un tipo de equipo') }}</option>
                            @foreach($tipos_equipo as $tipo)
                                <option value="{{ $tipo['COD_TIP_EQUIPO'] }}" {{ old('COD_TIP_EQUIPO', $equipo['COD_TIP_EQUIPO']) == $tipo['COD_TIP_EQUIPO'] ? 'selected' : '' }}>
                                    {{ $tipo['TIPO_EQUIPO'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="DESC_EQUIPO" class="form-label">DESCRIPCIÓN EQUIPO</label>
                        <textarea class="form-control" id="DESC_EQUIPO" name="DESC_EQUIPO" rows="3" required maxlength="255" oninput="updateCharCount()">{{ old('DESC_EQUIPO', $equipo['DESC_EQUIPO']) }}</textarea>
                        <div id="charCount">0 / 255 caracteres</div>
                    </div>

                    <div class="mb-3">
                        <label for="FECHA_COMPRA" class="form-label">FECHA COMPRA</label>
                        <input type="date" class="form-control" id="FECHA_COMPRA" name="FECHA_COMPRA" value="{{ old('FECHA_COMPRA', \Carbon\Carbon::parse($equipo['FECHA_COMPRA'])->format('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="VALOR_EQUIPO" class="form-label">VALOR EQUIPO</label>
                        <input type="number" class="form-control" id="VALOR_EQUIPO" name="VALOR_EQUIPO" value="{{ old('VALOR_EQUIPO', $equipo['VALOR_EQUIPO']) }}" step="0.01" min="0" required>
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-primary">GUARDAR CAMBIOS</button>
                        <a href="{{ route('equipos.index') }}" class="btn btn-secondary">CANCELAR</a>
                    </div>
                </form>
            </main>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css">
    <!-- Incluir los scripts de Select2 -->

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

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        
        $(document).ready(function() {
            $('.select2').select2();

            const valorEquipo = document.getElementById('VALOR_EQUIPO');

            valorEquipo.addEventListener('input', function (e) {
                const value = e.target.value;
                if (value < 0) {
                    e.target.value = '';
                    Swal.fire({
                        icon: 'error',
                        title: 'Valor no válido',
                        text: 'El valor del equipo no puede ser negativo.',
                    });
                }
            });
        });
        function updateCharCount() {
        const textarea = document.getElementById('DESC_EQUIPO');
        const charCount = document.getElementById('charCount');
        const maxLength = textarea.maxLength;
        const currentLength = textarea.value.length;

        charCount.textContent = `${currentLength} / ${maxLength} caracteres`;
    }
    
    // Inicializar el conteo de caracteres al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        updateCharCount();
    });
    </script>
@stop
