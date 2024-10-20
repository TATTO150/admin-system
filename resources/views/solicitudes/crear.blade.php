@extends('adminlte::page')

@section('title', 'Crear Compra')

@section('content_header')
    <h1 class="text-center">CREAR NUEVA SOLICITUD DE COMPRA</h1>
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

                <form action="{{ route('solicitudes.insertar') }}" method="POST" id="compraForm">
                    @csrf

                    <!-- Descripción de la Compra -->
                    <div class="mb-3">
                        <label for="DESC_COMPRA" class="form-label">{{ __('Descripción de la Solicitud') }}</label>
                        <textarea class="form-control" id="DESC_COMPRA" name="DESC_COMPRA" rows="3" required>{{ old('DESC_COMPRA') }}</textarea>
                    </div>

                    <!-- Proyecto -->
                    <div class="mb-3">
                        <label for="COD_PROYECTO" class="form-label">{{ __('Proyecto Asociado') }}</label>
                        <select class="form-select select2" id="COD_PROYECTO" name="COD_PROYECTO" required>
                            <option value="">{{ __('Seleccione un proyecto') }}</option>
                            @foreach ($proyectos as $proyecto)
                                <option value="{{ $proyecto->COD_PROYECTO }}" {{ old('COD_PROYECTO') == $proyecto->COD_PROYECTO ? 'selected' : '' }}>
                                    {{ $proyecto->NOM_PROYECTO }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipo de Compra -->
                    <div class="mb-3">
                        <label for="COD_TIPO" class="form-label">{{ __('Tipo de Compra') }}</label>
                        <select class="form-select select2" id="COD_TIPO" name="COD_TIPO" required>
                            <option value="">{{ __('Seleccione un tipo de compra') }}</option>
                            <option value="1">Contado</option>
                            <option value="2">Crédito</option>
                        </select>
                    </div>

                    <!-- Campos dinámicos (Ocultos por defecto) -->
                    <div id="camposCreditoContado" style="display: none;">
                        <!-- Total de Cuotas -->
                        <div class="mb-3" id="totalCuotasDiv" style="display: none;">
                            <label for="TOTAL_CUOTAS" class="form-label">{{ __('Número Total de Cuotas') }}</label>
                            <input type="number" class="form-control" id="TOTAL_CUOTAS" name="TOTAL_CUOTAS" value="{{ old('TOTAL_CUOTAS', 1) }}" min="1">
                        </div>

                        <!-- Método de Ingreso -->
                        <div class="mb-3" id="metodoIngresoDiv" style="display: none;">
                            <label class="form-label">{{ __('Seleccionar Método de Ingreso') }}</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodoIngreso" id="precioCompraRadio" value="precioCompra">
                                <label class="form-check-label" for="precioCompraRadio">{{ __('Ingresar Precio Total') }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodoIngreso" id="precioCuotaRadio" value="precioCuota">
                                <label class="form-check-label" for="precioCuotaRadio">{{ __('Ingresar Precio por Cuota') }}</label>
                            </div>
                        </div>

                        <!-- Precio Total -->
                        <div class="mb-3" id="precioCompraDiv" style="display: none;">
                            <label for="PRECIO_COMPRA" class="form-label">{{ __('Precio Total de la Compra') }}</label>
                            <input type="number" step="0.01" class="form-control" id="PRECIO_COMPRA" name="PRECIO_COMPRA" value="{{ old('PRECIO_COMPRA') }}">
                        </div>

                        <!-- Precio por Cuota -->
                        <div class="mb-3" id="precioCuotaDiv" style="display: none;">
                            <label for="PRECIO_CUOTA" class="form-label">{{ __('Precio por Cuota') }}</label>
                            <input type="number" step="0.01" class="form-control" id="PRECIO_CUOTA" name="PRECIO_CUOTA" value="{{ old('PRECIO_CUOTA') }}">
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-primary">GUARDAR</button>
                        <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">CANCELAR</a>
                    </div>
                </form>
            </main>
        </div>
    </div>
@stop

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

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#COD_TIPO').on('change', function() {
                const tipo = $(this).val();
                $('#camposCreditoContado').toggle(tipo !== '');
                $('#totalCuotasDiv, #metodoIngresoDiv, #precioCompraDiv, #precioCuotaDiv').hide();

                if (tipo === '1') { // Contado
                    $('#precioCompraDiv').show();
                } else if (tipo === '2') { // Crédito
                    $('#totalCuotasDiv, #metodoIngresoDiv').show();
                }
            });

            $('input[name="metodoIngreso"]').on('change', function() {
                const metodo = $(this).val();
                $('#precioCompraDiv').toggle(metodo === 'precioCompra');
                $('#precioCuotaDiv').toggle(metodo === 'precioCuota');
            });

            $('#compraForm').on('submit', function(e) {
                e.preventDefault();
                let metodo = $('input[name="metodoIngreso"]:checked').val();
                let totalCuotas = parseInt($('#TOTAL_CUOTAS').val()) || 1;

                if (metodo === 'precioCuota') {
                    let precioCuota = parseFloat($('#PRECIO_CUOTA').val()) || 0;
                    $('#PRECIO_COMPRA').val((precioCuota * totalCuotas).toFixed(2));
                }

                this.submit();
            });
        });
    </script>
@stop
