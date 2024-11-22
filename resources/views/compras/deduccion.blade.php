@extends('adminlte::page')

@section('title', 'DEDUCCIONES DE COMPRAS')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1 class="text-center mt-3">LIQUIDACIONES DE COMPRAS</h1>
@stop

@section('content')
    <!-- Botón para regresar al índice de compras -->
    <div class="mb-4">
        <a href="{{ route('compras.index') }}" class="btn btn-swarning btn btn-warning text-white hover:bg-blue-600">Regresar al listado de compras</a>
    </div>


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


    <!-- Cuadro de datos de la compra -->
    <div class="card mb-3  mb-4">
        <div class="card-header">
            <h3 class="text-center">Detalles de la Compra</h3>
            <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
                <button id="reporteBtn" class="btn btn-primary" data-url="/compras/{id}/reporte" data-compra-id="{{ $compras->COD_COMPRA }}">
                    REPORTE
                </button>
            </div>
        </div>

        
        
        <div class="card-body">
            <p><strong>Nombre del Usuario: </strong>{{$compras->usuario->Nombre_Usuario}}</p>
            <p><strong>Compra realizada: </strong> {{$compras->DESC_COMPRA}}</p>
            <p><strong>Proyecto de la compra: </strong> {{$compras->proyectos->NOM_PROYECTO}}</p>
            <p><strong>Registro de la compra: </strong> {{$compras->FEC_REGISTRO}}</p>
            <p><strong>Estado de la compra: </strong> {{$compras->estadocompras->DESC_ESTADO}}</p>
            <p><strong>Pago de compra al: </strong> {{$compras->tipocompras->DESC_TIPO}}</p>
            <p><strong>Precio Inicial: </strong> {{$compras->PRECIO_COMPRA}}</p>
            <p><strong>Precio Final: </strong> {{$pagoFinal}}</p>
            <p><strong>Fecha de pago final: </strong>{{$compras->FECHA_PAGO}} </p>
        </div>    
        
    </div>

    <!-- Campo de búsqueda -->
    <div class="mb-4">
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar permiso...">
    </div>
    
    <!-- Tabla de deducciones -->
    
    <div class="card-body" id="tabladeducciones">
        <div class="table-responsive">
            <h5 class="text-center mt-3">DEDUCCIONES DE LA COMPRA</h5>
            <h6 class="text-center mt-3" id="tituloActivos"></h6>
            <table class="table table-hover table-bordered dt-responsive nowrap">
                <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>Descripción de la Deducción</th>
                        <th>Valor de la Deducción</th>
                    </tr>
                </thead>
                <tbody id="tablaDeduccion">
                    @if(is_array($deducciones) || is_object($deducciones))
                        @foreach ($deducciones as $deducciones)
                            <tr>
                                <td>
                                    <!-- Dropdown para acciones de las deducciones -->
                                    <div class="dropdown">
                                        <button class="btn btn-secondary text-white hover:bg-blue-600" type="button" id="dropdownMenuButton{{ $deducciones['COD_DEDUCCION'] }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acción
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $deducciones['COD_DEDUCCION'] }}">
                                            <!-- Opción de deducción que abre el modal -->
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deduccionModal{{ $deducciones['COD_DEDUCCION'] }}">
                                                        EDITAR
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('compras.deduccion.destroy', ['COD_COMPRA' => $deducciones['COD_COMPRA'], 'COD_DEDUCCION' => $deducciones['COD_DEDUCCION']]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta deducción?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">ELIMINAR</button>
                                                    </form>
                                                    
                                                </li>
                                        </ul>
                                    </div>
                                </td>
                                <td>{{ $deducciones['DESC_DEDUCCION'] }}</td>
                                <td>{{ $deducciones['VALOR_DEDUCCION'] }}</td>
                            </tr>

                            <!-- Modal para la edicion de la deduccion -->
                            <div class="modal fade" id="deduccionModal{{ $deducciones['COD_DEDUCCION'] }}" tabindex="-1" aria-labelledby="deduccionModalLabel{{ $deducciones['COD_DEDUCCION'] }}" aria-hidden="true" >
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deduccionModalLabel{{ $deducciones['COD_DEDUCCION'] }}">EDITAR</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        
                                        <div class="modal-body">
                                            <!-- Formulario para agregar la deducción -->
                                            <form action="{{ route('compras.deduccion.update', ['COD_COMPRA' => $deducciones['COD_COMPRA'], 'COD_DEDUCCION' => $deducciones['COD_DEDUCCION']]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-group">
                                                    <label for="tipoDeduccion">Tipo de Deducción</label>
                                                    <select class="form-control" id="tipoDeduccion" name="tipo_deduccion">
                                                        <option value="numerico">Numérico</option>
                                                        <option value="porcentaje">Porcentaje</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="descripcionDeduccion">Descripción</label>
                                                    <input type="text" class="form-control" id="DESC_DEDUCCION" name="DESC_DEDUCCION" rows="3" required maxlength="255" value="{{old('DESC_DEDUCCION', $deducciones['DESC_DEDUCCION']) }}" required></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="valorDeduccion">Valor de Deducción</label>
                                                    <input type="number" class="form-control" id="VALOR_DEDUCCION" name="VALOR_DEDUCCION"  value="{{ old('VALOR_DEDUCCION', intval($deducciones['VALOR_DEDUCCION'])) }}" min="0" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="text-center">No se encontraron deducciones.</td>
                        </tr>
                    @endif    
                </tbody>
            </table>
        </div>
        <nav id="paginationExample" class="d-flex justify-content-center mt-3">
            <button id="prevPage" class="btn btn-outline-primary me-2">Anterior</button>
            <span id="currentPage" class="align-self-center"></span>
            <button id="nextPage" class="btn btn-outline-primary ms-2">Siguiente</button>
        </nav>
    </div>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    

        

    <!-- jQuery and DataTables scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    
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
        paginateTable('tabladeducciones', 6); // Cambiar el número de filas por página si es necesario
    });
           // Script de búsqueda mejorado
    

        document.getElementById('reporteBtn').addEventListener('click', function () {
        const compraId = this.getAttribute('data-compra-id');
        const baseUrl = this.getAttribute('data-url');

        if (compraId && baseUrl) {
            // Construir una URL relativa
            const url = baseUrl.replace('{id}', compraId);
            console.log("Abriendo en nueva pestaña:", url); // Verificar en consola
            window.open(url, '_blank'); // Abrir en nueva pestaña
        } else {
            alert('No se encontró el ID de la compra.');
        }
    });


    </script>

@stop