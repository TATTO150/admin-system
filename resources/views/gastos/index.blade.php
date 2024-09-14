@extends('adminlte::page')

@section('title', 'Libro Diario')

@section('content_header')
    <h1>Registro de Compras</h1>
@stop

@section('content')
    <div class="container">
        <main class="mt-3">

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reporteModal">Generar Reporte</button>
            <table id="mitabla" class="table table-hover table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>DESCRIPCIÓN COMPRA</th>
                        <th>PROYECTO</th>
                        <th>FECHA REGISTRO</th>
                        <th>ESTADO DE COMPRA</th>
                        <th>PRECIO</th>
                    </tr>
                </thead>
                <tbody>
                    @if(is_array($gastos) || is_object($gastos))
                        @foreach ($gastos as $gasto)
                            <tr>
                                <td>{{ $gasto->compra->DESC_COMPRA ?? 'No disponible' }}</td>
                                <td>{{ $gasto->proyecto->NOM_PROYECTO ?? 'No disponible' }}</td>
                                <td>{{ $gasto->FEC_REGISTRO }}</td>
                                <td>{{ $gasto->compra->TIP_COMPRA ?? 'No disponible' }}</td>
                                <td>{{ $gasto->SUBTOTAL }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">No se encontraron gastos.</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total General</strong></td>
                        <td><strong>{{ $totalGastos }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </main>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="reporteModal" tabindex="-1" aria-labelledby="reporteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reporteModalLabel">Seleccionar Tipo de Reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Mostrar errores aquí -->
                    @if ($errors->any())
                        <div class="alert alert-danger" id="errorAlert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="reporteForm" method="GET" action="" target="_self">
                        @csrf

                        <!-- Campo oculto para forzar la misma pestaña si hay errores -->
                        @if(session('forceSameTab'))
                            <input type="hidden" id="forceSameTab" value="true">
                        @endif

                        <div class="mb-3">
                            <label for="reporteTipo" class="form-label">Tipo de Reporte</label>
                            <select id="reporteTipo" name="reporteTipo" class="form-select" required>
                                <option value="">Seleccione un tipo de reporte</option>
                                <option value="fecha">Por Fecha</option>
                                <option value="proyecto">Por Proyecto</option>
                                <option value="hoy">Por el Día de Hoy</option>
                                <option value="general">Reporte General</option>
                            </select>
                        </div>
                        <div id="filtrosAdicionales"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="generarReporte">Generar Reporte</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="//code.jquery.com/jquery-3.7.0.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Verifica si hay errores y si se debe abrir un modal específico
            @if ($errors->any() && session('modal') && session('forceSameTab'))
                var modal = '{{ session('modal') }}';
                    $('#reporteModal').modal('show');
                    $('#reporteTipo').val(modal).trigger('change');
            @endif

            // Mostrar modal si hay errores
            @if ($errors->any() && session('modal'))
                var tipoReporte = '{{ old('reporteTipo', session('modal')) }}'; // Mantener el tipo de reporte seleccionado

                // Abre el modal existente
                $('#reporteModal').modal('show');

                // Mantener los valores anteriores en los campos adicionales
                var filtros = $('#filtrosAdicionales');
                filtros.empty(); // Limpiar filtros adicionales antes de rellenar

                if (tipoReporte === 'proyecto') {
                    filtros.append(`
                        <div class="mb-3">
                            <label for="proyecto" class="form-label">Proyecto</label>
                            <select id="proyecto" name="proyecto" class="form-select" required>
                                <option value="">Seleccione un proyecto</option>
                                @foreach ($proyectos as $proyecto)
                                    <option value="{{ $proyecto->COD_PROYECTO }}" {{ old('proyecto') == $proyecto->COD_PROYECTO ? 'selected' : '' }}>{{ $proyecto->NOM_PROYECTO }}</option>
                                @endforeach
                            </select>
                        </div>
                    `);
                } else if (tipoReporte === 'mes') {
                    filtros.append(`
                        <div class="mb-3">
                            <label for="mes" class="form-label">Mes</label>
                            <select id="mes" name="mes" class="form-select" required>
                                <option value="">Seleccione un mes</option>
                                <option value="1" {{ old('mes') == 1 ? 'selected' : '' }}>Enero</option>
                                <option value="2" {{ old('mes') == 2 ? 'selected' : '' }}>Febrero</option>
                                <option value="3" {{ old('mes') == 3 ? 'selected' : '' }}>Marzo</option>
                                <option value="4" {{ old('mes') == 4 ? 'selected' : '' }}>Abril</option>
                                <option value="5" {{ old('mes') == 5 ? 'selected' : '' }}>Mayo</option>
                                <option value="6" {{ old('mes') == 6 ? 'selected' : '' }}>Junio</option>
                                <option value="7" {{ old('mes') == 7 ? 'selected' : '' }}>Julio</option>
                                <option value="8" {{ old('mes') == 8 ? 'selected' : '' }}>Agosto</option>
                                <option value="9" {{ old('mes') == 9 ? 'selected' : '' }}>Septiembre</option>
                                <option value="10" {{ old('mes') == 10 ? 'selected' : '' }}>Octubre</option>
                                <option value="11" {{ old('mes') == 11 ? 'selected' : '' }}>Noviembre</option>
                                <option value="12" {{ old('mes') == 12 ? 'selected' : '' }}>Diciembre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="anio" class="form-label">Año</label>
                            <input type="number" id="anio" name="anio" class="form-control" value="{{ old('anio') }}" required>
                        </div>
                    `);
                } else if (tipoReporte === 'año') {
                    filtros.append(`
                        <div class="mb-3">
                            <label for="anio" class="form-label">Año</label>
                            <input type="number" id="anio" name="anio" class="form-control" value="{{ old('anio') }}" required>
                        </div>
                    `);
                } else if (tipoReporte === 'fecha') {
                    filtros.append(`
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" id="fecha" name="fecha" class="form-control" value="{{ old('fecha') }}" required>
                        </div>
                    `);
                }

                // Rellenar el selector de tipo de reporte automáticamente en el modal
                $('#reporteTipo').val(tipoReporte).trigger('change');
            @endif

            // Manejo del cambio en el tipo de reporte
            $('#reporteTipo').on('change', function() {
                var tipo = $(this).val();
                var filtros = $('#filtrosAdicionales');
                filtros.empty(); // Limpiar campos adicionales cada vez que se cambia el tipo de reporte

                // Limpiar errores anteriores
                $('#errorAlert').remove();
                $('#reporteForm').find('.is-invalid').removeClass('is-invalid');

                if (tipo === 'proyecto') {
                    filtros.append(`
                        <div class="mb-3">
                            <label for="proyecto" class="form-label">Proyecto</label>
                            <select id="proyecto" name="proyecto" class="form-select" required>
                                <option value="">Seleccione un proyecto</option>
                                @foreach ($proyectos as $proyecto)
                                    <option value="{{ $proyecto->COD_PROYECTO }}">{{ $proyecto->NOM_PROYECTO }}</option>
                                @endforeach
                            </select>
                        </div>
                    `);
                } else if (tipo === 'mes') {
                    filtros.append(`
                        <div class="mb-3">
                            <label for="mes" class="form-label">Mes</label>
                            <select id="mes" name="mes" class="form-select" required>
                                <option value="">Seleccione un mes</option>
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="anio" class="form-label">Año</label>
                            <input type="number" id="anio" name="anio" class="form-control" required>
                        </div>
                    `);
                } else if (tipo === 'año') {
                    filtros.append(`
                        <div class="mb-3">
                            <label for="anio" class="form-label">Año</label>
                            <input type="number" id="anio" name="anio" class="form-control" required>
                        </div>
                    `);
                } else if (tipo === 'fecha') {
                    filtros.append(`
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" id="fecha" name="fecha" class="form-control" required>
                        </div>
                    `);
                }
            });

            // Manejo del clic en "Generar Reporte"
            $('#generarReporte').on('click', function() {
                var forceSameTab = $('#forceSameTab').val();
                var tipo = $('#reporteTipo').val();
                var form = $('#reporteForm');
                var action = '';

                switch (tipo) {
                    case 'proyecto':
                        action = '{{ route("gastos.reporte.proyecto") }}';
                        break;
                    case 'hoy':
                        action = '{{ route("gastos.reporte.hoy") }}';
                        break;
                    case 'año':
                        action = '{{ route("gastos.reporte.ano") }}';
                        break;
                    case 'mes':
                        action = '{{ route("gastos.reporte.mes") }}';
                        break;
                    case 'general':
                        action = '{{ route("gastos.pdf") }}';
                        break;
                    case 'fecha':
                        action = '{{ route("gastos.reporte.fecha") }}';
                        break;
                    default:
                        alert('Seleccione un tipo de reporte válido.');
                        return;
                }

                form.attr('action', action);

                var isValid = true;
                form.find('select, input').each(function() {
                    if (!this.checkValidity()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (isValid) {
                    form.attr('target', '_self'); // Mantener en la misma pestaña si es válido
                } else {
                    form.attr('target', ''); // Mantener en la misma pestaña si hay errores
                }

                form.submit(); // Envía el formulario
            });

            // Restablecer el formulario cuando se cierra el modal
            $('#reporteModal').on('hidden.bs.modal', function () {
                $('#reporteForm')[0].reset(); // Restablecer el formulario
                $('#filtrosAdicionales').empty(); // Limpiar filtros adicionales
                $('#errorAlert').remove(); // Remover cualquier alerta de error
                $('#reporteForm').find('.is-invalid').removeClass('is-invalid'); // Remover clases de error en los campos
            });

            // Inicialización de DataTable
            new DataTable('#mitabla', {
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json',
                },
                dom: 'Bfrtip',
                buttons: [
                ],
            });
        });
    </script>
@stop
