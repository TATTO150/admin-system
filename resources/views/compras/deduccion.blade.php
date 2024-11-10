@extends('adminlte::page')

@section('title', 'DEDUCCIONES DE COMPRAS')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1 class="text-center mt-3">LIQUIDACIONES DE COMPRAS</h1>
@stop

@section('content')
    <!-- Botón para regresar al índice de compras -->
    <div class="mb-4">
        <a href="{{ route('compras.index') }}" class="btn btn-secondary">Regresar al listado de compras</a>
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
                <!-- Botones para crear empleado, ver inactivos y generar reporte -->
                <button id="reporteModalBtn" class="btn btn-primary bg-teal-500 text-white hover:bg-teal-600" data-toggle="modal" data-target="#pdfModal">REPORTE</button>
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

    <!-- Formulario de búsqueda -->
    <div>
        <form id="buscador-form" method="GET">
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="buscar" placeholder="Buscar..." name="buscar" value="{{ request()->input('buscar') }}">
            </div>
        </form>
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
                <tbody>
                    @foreach ($deducciones as $deducciones)
                        <tr>
                            <td>
                                <!-- Dropdown para acciones de las deducciones -->
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{ $deducciones['COD_DEDUCCION'] }}" data-bs-toggle="dropdown" aria-expanded="false">
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
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <!-- jQuery and DataTables scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTables
            $('#mitabla, #mitablaretrasados').DataTable({
                responsive: false
            });
        });
    </script>
@stop

