@extends('adminlte::page')

@section('title', 'LIQUIDACIÓN DE COMPRAS')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1 class="text-center mt-3" id="tituloActivos">LIQUIDACIONES DE COMPRAS</h1>
    <h3 id="tituloretrasados" class="text-center mt-3" style="display: none;">Restrasadas</h3>

@stop

@section('content')
    
        <!-- Mostrar errores -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
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

    <div class="card mb-3 mt-4">
        <div class="card-header">
           <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
                <button id="reporteModalBtn" class="btn btn-primary  bg-teal-500 text-white hover:bg-teal-600" data-toggle="modal" data-target="#reportModal">REPORTE</button>
            </div>
            
        
            <!-- Formulario de búsqueda -->
        <form id="buscador-form" method="GET">
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="buscar" placeholder="Buscar..." name="buscar" value="{{ request()->input('buscar') }}">
            </div>
        </form>
        </div>
        

     <!-- Compras liquidadas -->        
        <div class="card-body"  id="tablapago">
            <!-- Contenedor para la tabla con barra de desplazamiento horizontal -->
            <div class="table-responsive"><h5 class="text-center mt-3" id="titulopago">COMPRAS REALIZADAS</h5>
                <h6 class="text-center mt-3" id="tituloActivos"></h6>
                    
                <table  class="table table-hover table-bordered dt-responsive nowrap" id="tablacompras">
                    
                    <thead class="thead-dark">
                        <tr>
                            <th>ACCIÓN</th>
                            <th>USUARIO</th>
                            <th>DESCRIPCION COMPRA</th>
                            <th>PROYECTO ASIGNADO</th>
                            <th>FECHA REGISTRO</th>
                            <th>FECHA PAGO</th>
                            <th>ESTADO COMPRA</th>
                            <th>TIPO COMPRA</th>
                            <th>PRECIO INICIAL</th>
                            <th>DEDUCCIONES</th>
                            <th>PRECIO FINAL</th>
                            <th>LIQUIDADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compras as $compras)
                            <tr>
                                <td>
                                    <!-- Dropdown para acciones de las deducciones -->
                                    <div class="dropdown">
                                        <button class="btn btn-secondary  text-white hover:bg-blue-600" type="button" id="dropdownMenuButton{{ $compras['COD_COMPRA'] }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acción
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $compras['COD_COMPRA'] }}">
                                            <!-- Opción de deducción que abre el modal -->
                                            <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#deduccionModal{{ $compras['COD_COMPRA'] }}">Agregar Deducción</a></li>
                                            <li><a class="dropdown-item" href="{{ route('compras.deduccion', $compras->COD_COMPRA) }}">Detalles</a></li>
                                        </ul>
                                    </div>
                                </td>
                                
                                <td>{{ isset($usuarios[$compras['Id_usuario']]) ? $usuarios[$compras['Id_usuario']]->Nombre_Usuario : 'Usuario no encontrado' }}</td>
                                <td>{{ $compras['DESC_COMPRA'] }}</td>
                                <td>{{ isset($proyectos[$compras['COD_PROYECTO']]) ? $proyectos[$compras['COD_PROYECTO']]->NOM_PROYECTO : 'Proyecto no encontrado' }}</td>
                                <td>{{ $compras['FEC_REGISTRO'] }}</td>
                                <td>{{ $compras['FECHA_PAGO'] }}</td>
                                <td>{{ isset($estadocompras[$compras['COD_ESTADO']]) ? $estadocompras[$compras['COD_ESTADO']]->DESC_ESTADO: 'Estado no encontrada' }}</td>
                                <td>{{ isset($tipocompras[$compras['COD_TIPO']]) ? $tipocompras[$compras['COD_TIPO']]->DESC_TIPO : 'Compra no encontrada' }}</td>
                                <td>{{ number_format($compras->PRECIO_COMPRA, 2) }}</td>
                                <td>{{ number_format($compras->totalDeducciones, 2) }}</td>
                                <td>{{ number_format($compras->precioFinal, 2)}}</td>
                                <td>{{ $compras['LIQUIDEZ_COMPRA'] == 1 ? 'Sí' : 'No' }}</td>
                            </tr>

                            <!-- Modal para deducción -->
                            <div class="modal fade" id="deduccionModal{{ $compras['COD_COMPRA'] }}" tabindex="-1" aria-labelledby="deduccionModalLabel{{ $compras['COD_COMPRA'] }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deduccionModalLabel{{ $compras['COD_COMPRA'] }}">Agregar Deducción</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        
                                        <div class="modal-body">
                                            <!-- Formulario para agregar la deducción -->
                                            <form action="{{ route('compras.agregar', $compras['COD_COMPRA']) }}" method="POST">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="tipoDeduccion">Tipo de Deducción</label>
                                                    <select class="form-control" id="tipoDeduccion" name="tipo_deduccion" required>
                                                        <option value="numerico">Numérico</option>
                                                        <option value="porcentaje">Porcentaje</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="valorDeduccion">Valor de Deducción</label>
                                                    <input type="number" class="form-control" id="valorDeduccion" name="valor_deduccion" placeholder="Ingrese el valor de deducción">
                                                </div>
                                                <div class="form-group">
                                                    <label for="descripcionDeduccion">Descripción</label>
                                                    <textarea class="form-control" id="descripcionDeduccion" name="descripcion_deduccion" placeholder="Descripción de la deducción"></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

         <!-- Compras por liquidar --> 
            <div class="table-responsive">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-center mt-3" id="tituloActivos">LIQUIDACIONES POR REALIZAR</h5>
                    <form id="formLiquidar">
                        @csrf
                        <button type="button" id="submitButton" class="btn btn-success text-white">Liquidar</button>
                    </form>
                </div>
            
                <table class="table table-hover table-bordered dt-responsive nowrap" id="tablaLiquidacion">
                    <thead class="thead-dark">
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>USUARIO</th>
                            <th>DESCRIPCION COMPRA</th>
                            <th>PROYECTO ASIGNADO</th>
                            <th>FECHA REGISTRO</th>
                            <th>ESTADO COMPRA</th>
                            <th>TIPO COMPRA</th>
                            <th>FECHA PAGO</th>
                            <th>PRECIO COMPRA</th>
                            <th>PRECIO CUOTA</th>
                            <th>PRECIO NETO</th>
                            <th>CUOTAS PAGADAS</th>
                            <th>TOTAL CUOTAS</th>
                            <th>LIQUIDADO</th>
                        </tr>
                    </thead>
                    <tbody id="tablaLiquidaciones">
                        @foreach ($Liquidaciones as $compras)
                        <tr data-compra-id="{{ $compras->COD_COMPRA }}">
                            <td>
                                <input type="checkbox" class="checkbox-compra" value="{{ $compras->COD_COMPRA }}">
                            </td>
                            <td>{{ isset($usuarios[$compras['Id_usuario']]) ? $usuarios[$compras['Id_usuario']]->Nombre_Usuario : 'Usuario no encontrado' }}</td>
                            <td>{{ $compras['DESC_COMPRA'] }}</td>
                            <td>{{ isset($proyectos[$compras['COD_PROYECTO']]) ? $proyectos[$compras['COD_PROYECTO']]->NOM_PROYECTO : 'Proyecto no encontrado' }}</td>
                            <td>{{ $compras['FEC_REGISTRO'] }}</td>
                            <td>{{ isset($estadocompras[$compras['COD_ESTADO']]) ? $estadocompras[$compras['COD_ESTADO']]->DESC_ESTADO : 'Estado no encontrado' }}</td>
                            <td>{{ isset($tipocompras[$compras['COD_TIPO']]) ? $tipocompras[$compras['COD_TIPO']]->DESC_TIPO : 'Tipo no encontrado' }}</td>
                            <td>{{ $compras['FECHA_PAGO'] }}</td>
                            <td>{{ $compras['PRECIO_COMPRA'] }}</td>
                            <td>{{ $compras['PRECIO_CUOTA'] }}</td>
                            <td>{{ $compras['PRECIO_NETO'] }}</td>
                            <td>{{ $compras['CUOTAS_PAGADAS'] }}</td>
                            <td>{{ $compras['TOTAL_CUOTAS'] }}</td>
                            <td>{{ $compras['LIQUIDEZ_COMPRA'] == 1 ? 'Sí' : 'No' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <nav id="paginationExample" class="d-flex justify-content-center mt-3">
                <button id="prevPage" class="btn btn-outline-primary me-2">Anterior</button>
                <span id="currentPage" class="align-self-center"></span>
                <button id="nextPage" class="btn btn-outline-primary ms-2">Siguiente</button>
            </nav>
            <!-- Modal para Generar Reporte -->
            <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reportModalLabel">Generar Reporte</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Contenedor para mostrar mensajes de error dentro del modal -->
                            <div id="errorContainer" class="alert alert-danger d-none"></div>
                            

                            <form id="reporteForm" method="POST" action="" >
                                @csrf
                                
                                <div class="form-group">
                                    <label for="reportType" class="form-label">Tipo de Reporte</label>
                                    <select id="reportType" name="reportType" class="form-select" required>
                                        <option value="">Seleccione un tipo de reporte</option>
                                        <option value="general">General</option>
                                        <option value="liquidado">Liquidados</option>
                                        <option value="Noliquidado">No Liquidados</option>
                                    </select>
                                </div>
                                
                                    <button type="submit" class="btn btn-primary" id="generarReporteBtn">Generar</button>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Creación del check y función -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const selectAllCheckbox = document.getElementById('selectAll');
                    const checkboxes = document.querySelectorAll('.checkbox-compra');
                    const submitButton = document.getElementById('submitButton');
                    const formLiquidar = document.getElementById('formLiquidar');
            
                    // Seleccionar o deseleccionar todos los checkboxes
                    selectAllCheckbox.addEventListener('change', function () {
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = selectAllCheckbox.checked;
                        });
                    });
            
                    // Enviar datos al backend
                    submitButton.addEventListener('click', function () {
                        const selectedValues = Array.from(checkboxes)
                            .filter(checkbox => checkbox.checked)
                            .map(checkbox => checkbox.value);
            
                        console.log('Valores seleccionados:', selectedValues); // Depuración en consola
            
                        if (selectedValues.length === 0) {
                            alert('Debe seleccionar al menos una compra para liquidar.');
                            return;
                        }
            
                        const formData = new FormData(formLiquidar);
                        formData.append('compraSeleccionada', JSON.stringify(selectedValues));
            
                        fetch("{{ route('compras.liquidar') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP status ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Respuesta del servidor:', data);
                        if (data.success) {
                            alert(data.message || '¿Seguro que desea liquidar las compras seleccionadas?');
                            window.location.reload();
                        } else {
                            alert(data.message || 'Error al realizar la liquidación.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(`Error al procesar la solicitud: ${error.message}`);
                    });

                    });
                });
            </script>

        </div>                  
@stop

@section('css')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">
    <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@stop

@section('js')



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

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
        paginateTable('tablaLiquidacion', 6); // Cambiar el número de filas por página si es necesario
    });

    // Llamar a la función de paginación al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        paginateTable('tablacompras', 6); // Cambiar el número de filas por página si es necesario
    });
$(document).ready(function() {
    $('#buscar').on('keyup', function() {
        var query = $(this).val().toLowerCase();
        $('#tablapago tbody tr').each(function() {
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="//code.jquery.com/jquery-3.7.0.js" type="text/javascript"></script>
<script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="//cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
<script src="//cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js" type="text/javascript"></script>
<script src="//cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
<script>
        
        $(document).ready(function () {
    // Inicializar DataTables para ambas tablas
    $('#mitabla, #mitablaretrasados').DataTable({
        responsive: false
    });
    });
    

    document.addEventListener('DOMContentLoaded', function () {
    const generarReporteBtn = document.getElementById('generarReporteBtn');
    const reportTypeSelect = document.getElementById('reportType');

    generarReporteBtn.addEventListener('click', function (event) {
        event.preventDefault(); // Evitar el comportamiento predeterminado del formulario.

        const selectedOption = reportTypeSelect.value;

        if (!selectedOption) {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Por favor, seleccione un tipo de reporte antes de generar.',
            });
            return;
        }

        // Define las rutas dependiendo del valor seleccionado.
        let url;
        switch (selectedOption) {
            case 'general':
                url = "{{ route('compras.pdf') }}"; // Ruta para el reporte general.
                break;
            case 'liquidado':
                url = "{{ route('compras.pdfLiquidado') }}"; // Ruta para el reporte de liquidados.
                break;
            case 'Noliquidado':
                url = "{{ route('compras.pdfNoLiquidado') }}"; // Ruta para el reporte no liquidados.
                break;
            default:
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Tipo de reporte no válido.',
                });
                return;
        }

        // Abrir el PDF en una nueva pestaña
        window.open(url, '_blank');
    });
    });

</script>
@stop