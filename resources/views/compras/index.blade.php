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
                <!-- Botones para crear empleado, ver inactivos y generar reporte -->
                <button id="toggleretrasados" class="btn btn-warning text-white hover:bg-blue-600">RETARDO DE LIQUIDEZ</button>
                <button id="reporteModalBtn" class="btn btn-primary  bg-teal-500 text-white hover:bg-teal-600" data-toggle="modal" data-target="#pdfModal">REPORTE</button>
            </div>
            
        
            <!-- Formulario de búsqueda -->
        <form id="buscador-form" method="GET">
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="buscar" placeholder="Buscar..." name="buscar" value="{{ request()->input('buscar') }}">
            </div>
        </form>
        </div>
        

     <!-- Compras liquidadas y por liquidar -->        
        <div class="card-body"  id="tablapago">
            <!-- Contenedor para la tabla con barra de desplazamiento horizontal -->
            <div class="table-responsive"><h5 class="text-center mt-3" id="titulopago">COMPRAS REALIZADAS</h5>
                <h6 class="text-center mt-3" id="tituloActivos"></h6>
                    
                <table  class="table table-hover table-bordered dt-responsive nowrap">
                    
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
                        @foreach ($compras as $compra)
    <tr>
        <td>
            <!-- Dropdown para acciones de las deducciones -->
            <div class="dropdown">
                <button class="btn btn-secondary text-white hover:bg-blue-600" type="button" id="dropdownMenuButton{{ $compra['COD_COMPRA'] }}" data-bs-toggle="dropdown" aria-expanded="false">
                    Acción
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $compra['COD_COMPRA'] }}">
                    <!-- Opción de deducción que abre el modal -->
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deduccionModal{{ $compra['COD_COMPRA'] }}">Agregar Deducción</a></li>
                    <li><a class="dropdown-item" href="{{ route('compras.deduccion', $compra['COD_COMPRA']) }}">Detalles</a></li>
                </ul>
            </div>
        </td>

        <td>{{ isset($usuarios[$compra['Id_usuario']]) ? $usuarios[$compra['Id_usuario']]->Nombre_Usuario : 'Usuario no encontrado' }}</td>
        <td>{{ $compra['DESC_COMPRA'] }}</td>
        <td>{{ isset($proyectos[$compra['COD_PROYECTO']]) ? $proyectos[$compra['COD_PROYECTO']]->NOM_PROYECTO : 'Proyecto no encontrado' }}</td>
        <td>{{ $compra['FEC_REGISTRO'] }}</td>
        <td>{{ $compra['FECHA_PAGO'] }}</td>
        <td>{{ isset($estadocompras[$compra['COD_ESTADO']]) ? $estadocompras[$compra['COD_ESTADO']]->DESC_ESTADO : 'Estado no encontrado' }}</td>
        <td>{{ isset($tipocompras[$compra['COD_TIPO']]) ? $tipocompras[$compra['COD_TIPO']]->DESC_TIPO : 'Compra no encontrada' }}</td>
        <td>{{ number_format($compra->PRECIO_COMPRA, 2) }}</td>
        <td>{{ number_format($compra->totalDeducciones, 2) }}</td>
        <td>{{ number_format($compra->precioFinal, 2) }}</td>
        <td>{{ $compra['LIQUIDEZ_COMPRA'] == 1 ? 'Sí' : 'No' }}</td>
    </tr>

    <!-- Modal para deducción -->
    <div class="modal fade" id="deduccionModal{{ $compra['COD_COMPRA'] }}" tabindex="-1" aria-labelledby="deduccionModalLabel{{ $compra['COD_COMPRA'] }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deduccionModalLabel{{ $compra['COD_COMPRA'] }}">Agregar Deducción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulario para agregar la deducción -->
                    <form action="{{ route('compras.agregar', $compra['COD_COMPRA']) }}" method="POST">
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

            <div class="table-responsive">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-center mt-3" id="tituloActivos">LIQUIDACIONES POR REALIZAR</h5>
                    
                    <form  id="formLiquidar">
                        @csrf
                        <a href="#" class="btn btn-success" id="LiquidarSelect">Liquidar</a>
                    </form>
                    
                    
                </div>
                
                
                <table class="table table-hover table-bordered dt-responsive nowrap">
                    <thead class="thead-dark">
                        <tr>
                            <th> <input type="checkbox" name="" id="selectAll"> 
                            </th>
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
                        <tr id="compraCod{{ $compras->COD_COMPRA }}">
                            <td><input type="checkbox" name="ids" class="checkbox-compra" value="{{ $compras->COD_COMPRA }}">
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
            
        </div>
         
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(function(e){
            $("#selectAll").click(function(){
                $('.checkbox-compra').prop('checked',$(this).prop('checked'));
            });

            $('#LiquidarSelect').click(function(e){
                e.preventDefault();
                var all_cod = [];
                $('input:checkbox[name=ids]:checked').each(function(){
                    all_cod.push($(this).val());
                });

            })
        });
    </script> 
<!-- ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
    <div class="card mb-3 d-none" id="tablaretrasados">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-left mb-2">
                <form id="formLiquidacion">
                    @csrf
                    <input type="hidden" name="compras_seleccionadas" id="comprasRetrasoSeleccionadas">
                    <button type="submit" class="btn btn-success text-white hover:bg-blue-600">Liquidar</button>
                </form>
            </div>
            
            <!-- Contenedor para la tabla con barra de desplazamiento horizontal -->
            <div class="table-responsive">
                <table id="tablaretrasados" class="table table-hover table-bordered dt-responsive nowrap">
                    <thead class="thead-dark">
                        <tr>
                            <th><input type="checkbox" id="selecAll"> </th>
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
                    <tbody id="tablaretrasados">
                        @foreach ($retraso as $compras)
                        <tr data-compra-id="{{ $compras->COD_COMPRA }}">
                            <td><input type="checkbox" class="checkbox-compras" value="{{ $compras->COD_COMPRA }}">
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
        </div>
        <script>
            
           

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
<script>
$(document).ready(function() {
    $('#buscar').on('keyup', function() {
        var query = $(this).val().toLowerCase();
        $('#tablaretrasados tbody tr').each(function() {
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
        
    $(document).ready(function() {
        // Inicializar DataTables
        $('#mitabla, #mitablaretrasados').DataTable({
            responsive: false
        });

        // Alternar entre empleados activos e inactivos
        $('#toggleretrasados').click(function() {
            $('#tablapago').toggleClass('d-none');
            $('#tablaretrasados').toggleClass('d-none');
            $('#titulopago').toggle();  
            $('#tituloretrasados').toggle(); 
            $(this).text($(this).text() === 'Ver Compras y Liquidaciones' ? 'Ver Retardo de Liquidez' : 'Ver Compras y Liquidaciones');
        });
    }); 
</script>
@stop
