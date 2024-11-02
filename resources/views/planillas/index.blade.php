@extends('adminlte::page')

@section('title', 'Planillas')

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#buscar').on('keyup', function() {
        var query = $(this).val().toLowerCase();
        $('#mitabla tbody tr').each(function() {
            var rowText = $(this).text().toLowerCase();
            if (rowText.indexOf(query) === -1) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });
});
</script>
    <script src="//code.jquery.com/jquery-3.7.0.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js" type="text/javascript"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11" type="text/javascript"></script>
    <script src="//cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js" type="text/javascript"></script>

    <script>
        // Función para paginar la tabla
    function paginateTable(tableId, rowsPerPage) {
        const table = document.getElementById(tableId);
        const rows = table.querySelectorAll('tbody tr');
        const prevButton = document.getElementById('prevPage');
        const nextButton = document.getElementById('nextPage');
        const currentPageLabel = document.getElementById('currentPage');

        let currentPage = 1;
        const rowCount = rows.length;
        const pageCount = Math.ceil(rowCount / rowsPerPage);

        // Función para mostrar solo las filas de la página actual
        function showPage(page) {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            rows.forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });
            currentPageLabel.textContent = 'Pág. ' + page;
        }

        // Eventos de navegación
        prevButton.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        });

        nextButton.addEventListener('click', function() {
            if (currentPage < pageCount) {
                currentPage++;
                showPage(currentPage);
            }
        });

        // Mostrar la primera página al cargar
        showPage(currentPage);
    }

    // Llamar a la función de paginación al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        paginateTable('mitabla', 6); // Cambiar el número de filas por página si es necesario
    });
        $(document).ready(function() {
            
    
            // Inicializar los datepickers
            $('#fecha_inicio, #fecha_fin, #mes_reporte').datepicker({
                format: 'yyyy-mm',
                viewMode: 'months',
                minViewMode: 'months',
                autoclose: true
            });
    
            // Lógica para mostrar/ocultar campos según el tipo de reporte seleccionado
            $('#tipo_reporte').on('change', function() {
                var selectedValue = $(this).val();
                
                // Ocultar campo de rango de fechas y cambiar el texto del botón
                $('#rango_fechas').hide();
                $('#generateReportButton').text('Generar Reporte');

                $('#generateReportButton').on('click', function(e) {
                e.preventDefault(); // Prevenir el envío del formulario por defecto

                // Obtener los datos del formulario
                var formData = new FormData($('#reporteForm')[0]);

                // Hacer la petición AJAX
                fetch('{{ route('planillas.generar_reporte') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Agrega el token CSRF
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        // Si la respuesta no es 200, manejar el error
                        return response.json().then(data => {
                            throw new Error(data.error);
                        });
                    }
                    // Si la respuesta es correcta, puede ser la lógica para mostrar el reporte
                    return response.blob();
                })
                .then(blob => {
                    // Si todo fue bien, abrir el reporte en una nueva pestaña (si es un archivo descargable)
                    var url = window.URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = "reporte.pdf"; // Cambiar el nombre del archivo si es necesario
                    document.body.appendChild(a);
                    a.click();
                    a.remove(); // Elimina el enlace después de hacer clic
                })
                .catch(error => {
                    // Mostrar el mensaje de error si no se encontraron empleados
                    alert(error.message);
                });
            });



                // Mostrar el campo correspondiente basado en la selección
                if (selectedValue === 'fecha') {
                    $('#rango_fechas').show();
                    $('#generateReportButton').show();
                    $('#generalReportButton').hide();
                } else if (selectedValue === 'general') {
                    $('#rango_fechas').hide();
                    $('#generateReportButton').hide();
                    $('#generalReportButton').show();
                }
            });
    
            // Inicializar datepickers y restablecer elementos de formulario cuando se muestra el modal
            $('#reporteModal').on('shown.bs.modal', function () {
                $('#fecha_inicio_reporte').datepicker({
                    format: 'yyyy-mm',
                    viewMode: 'months',
                    minViewMode: 'months',
                    autoclose: true
                });
    
                $('#fecha_fin_reporte').datepicker({
                    format: 'yyyy-mm',
                    viewMode: 'months',
                    minViewMode: 'months',
                    autoclose: true
                });
    
                $('#mes_reporte').datepicker({
                    format: 'yyyy-mm',
                    viewMode: 'months',
                    minViewMode: 'months',
                    autoclose: true
                });
    
                // Restablecer select para tipo_planilla cuando se muestra el modal
                $('#tipo_planilla_reporte').prop('selectedIndex', 0);
            });
    
            // Manejar el clic en el botón de reporte general
            $('#generalReportButton').on('click', function(e) {
            e.preventDefault(); // Prevenir la acción por defecto

            var url = '{{ route('planillas.generar_reporte_general') }}'; // La URL a la que haremos la solicitud

            // Usar fetch para hacer la petición
            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Añadir token CSRF si es necesario
                }
            })
            .then(response => {
                if (!response.ok) {
                    // Si la respuesta no es 200, manejar el error
                    return response.json().then(data => {
                        throw new Error(data.error);
                    });
                }
                // Si la respuesta es correcta, procesar el PDF u otro tipo de reporte
                return response.blob(); // Esperar el archivo (PDF, etc.)
            })
            .then(blob => {
                // Si la respuesta es correcta, abrir el reporte o descargarlo
                var url = window.URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = "reporte_general.pdf"; // Nombre del archivo a descargar
                document.body.appendChild(a);
                a.click(); // Hacer clic en el enlace de descarga
                a.remove(); // Eliminar el enlace después de la descarga
            })
            .catch(error => {
                // Mostrar el mensaje de error si ocurrió algún problema
                alert(error.message);
            });
        });

        });
    
        function confirmDeletion() {
            return Swal.fire({
                title: '¿Está seguro de eliminar esta planilla?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                return result.isConfirmed;
            });
        }
    </script>
    
        
@stop

@section('content_header')
    <h1 class="text-center">GESTIÓN DE PLANILLAS</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Mostrar errores -->
                @if ($errors->any())
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: '¡Error!',
                                html: '{!! implode('<br>', $errors->all()) !!}',
                            });
                        });
                    </script>
                @endif

                 <!-- Mostrar mensajes de éxito -->
                 @if(session('success'))
                 <script>
                     document.addEventListener('DOMContentLoaded', function() {
                         Swal.fire({
                             icon: 'success',
                             title: '¡Éxito!',
                             text: '{{ session('success') }}',
                         });
                     });
                 </script>
             @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generarPlanillaModal">
                        NUEVA
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#reporteModal">
                        REPORTE
                    </button>
                </div>

                <!-- Formulario de búsqueda -->
                <form id="buscador-form" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="buscar" placeholder="Buscar..." name="buscar" value="{{ request()->input('buscar') }}">
                    </div>
                </form>

                <!-- Tabla de planillas -->
                <table id="mitabla" class="table table-hover table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Acciones</th>
                            <th>Planilla</th>
                            <th>Fecha Generada</th>
                            <th>Mes Pagado</th>
                            <th>Tipo Planilla</th>
                            <th>Total Pagado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($planillas as $planilla)
                            <tr>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{ $planilla['COD_PLANILLA'] }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $planilla['COD_PLANILLA'] }}">
                                            <li><a class="dropdown-item" href="{{ route('planillas.show', $planilla['COD_PLANILLA']) }}">Ver detalle de planilla</a></li>
                                            <li><a class="dropdown-item" href="{{ route('reporte.generar', ['id' => $planilla->COD_PLANILLA]) }}" class="btn btn-success" target="_blank">Reporte</a></li>
                                            <li>
                                                <form action="{{ route('planillas.destroy', $planilla['COD_PLANILLA']) }}" method="POST" class="d-inline" onsubmit="return confirmDelete({{ $planilla['COD_PLANILLA'] }})">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item">ELIMINAR</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td>{{ $planilla->COD_PLANILLA }}</td>
                                <td>{{ $planilla->FECHA_GENERADA }}</td>
                                <td>{{ $planilla->MES }}</td>
                                <td>{{ $tipos_planilla[$planilla->COD_TIPO_PLANILLA] ? $tipos_planilla[$planilla->COD_TIPO_PLANILLA]->TIPO_PLANILLA : 'N/A' }}</td>
                                <td>{{ $planilla->TOTAL_PAGADO }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Paginación -->
        <nav id="paginationExample" class="d-flex justify-content-center mt-3">
            <button id="prevPage" class="btn btn-outline-primary me-2">Anterior</button>
            <span id="currentPage" class="align-self-center"></span>
            <button id="nextPage" class="btn btn-outline-primary ms-2">Siguiente</button>
        </nav>
    </div>

    <!-- Modal para generar planilla -->
    <div class="modal fade" id="generarPlanillaModal" tabindex="-1" aria-labelledby="generarPlanillaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generarPlanillaModalLabel">Generar Nueva Planilla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('planillas.generar') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Mes de pago:</label>
                            <input type="text" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo_planilla" class="form-label">Seleccione el Tipo de Planilla:</label>
                            <select name="tipo_planilla" id="tipo_planilla" class="form-control" required>
                                <option value="">Seleccione el tipo de planilla</option>
                                @foreach($tipos_planilla as $tipo)
                                    <option value="{{ $tipo->COD_TIPO_PLANILLA }}">{{ $tipo->TIPO_PLANILLA }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Generar Planilla</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para generar reporte -->
    <div class="modal fade" id="reporteModal" tabindex="-1" aria-labelledby="reporteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reporteModalLabel">Generar Reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="reporteForm" action="{{ route('planillas.generar_reporte') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tipo_reporte" class="form-label">Tipo de Reporte:</label>
                            <select name="tipo_reporte" id="tipo_reporte" class="form-control" required>
                                <option value="">Seleccione el tipo de reporte</option>
                                <option value="fecha">Por Fecha</option>
                                <option value="general">Reporte General</option>
                            </select>
                        </div>

                        <div id="rango_fechas" class="mb-3" style="display: none;">
                            <label for="fecha_inicio_reporte" class="form-label">Fecha de Inicio:</label>
                            <input type="text" id="fecha_inicio_reporte" name="fecha_inicio_reporte" class="form-control">
                            <label for="fecha_fin_reporte" class="form-label">Fecha de Fin:</label>
                            <input type="text" id="fecha_fin_reporte" name="fecha_fin_reporte" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" id="generateReportButton">Generar Reporte</button>
                        <button type="button" class="btn btn-primary" id="generalReportButton" style="display: none;">Generar Reporte General</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop