

<?php $__env->startSection('title', 'LIQUIDACIÓN DE COMPRAS'); ?>
<?php $__env->startSection('plugins.Sweetalert2', true); ?>

<?php $__env->startSection('content_header'); ?>
    <h1 class="text-center mt-3" id="tituloActivos">LIQUIDACIONES DE COMPRAS</h1>
    <h3 id="tituloretrasados" class="text-center mt-3" style="display: none;">Lidaciones vencidas</h3>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    
        <!-- Mostrar errores -->
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>


        <!-- Mostrar mensajes de éxito -->
    <?php if(session('success')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '<?php echo e(session('success')); ?>',
            });
        });
    </script>
    <?php endif; ?>

    <div class="card mb-3 mt-4">
        <div class="card-header">
           <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
                <!-- Botones para crear empleado, ver inactivos y generar reporte -->
                <button id="toggleretrasados" class="btn btn-warning text-white hover:bg-blue-600">RETARDO DE LIQUIDEZ</button>
                <button id="reporteModalBtn" class="btn btn-primary bg-teal-500 text-white hover:bg-teal-600" data-toggle="modal" data-target="#pdfModal">REPORTE</button>
            </div>
            
        
            <!-- Formulario de búsqueda -->
        <form id="buscador-form" method="GET">
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="buscar" placeholder="Buscar..." name="buscar" value="<?php echo e(request()->input('buscar')); ?>">
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
                            <th></th>
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
                        <?php $__currentLoopData = $compras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compras): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <!-- Dropdown para acciones de las deducciones -->
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton<?php echo e($compras['COD_COMPRA']); ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acción
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo e($compras['COD_COMPRA']); ?>">
                                            <!-- Opción de deducción que abre el modal -->
                                            <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#deduccionModal<?php echo e($compras['COD_COMPRA']); ?>">Agregar Deducción</a></li>
                                            <li><a class="dropdown-item" href="<?php echo e(route('compras.deduccion', $compras->COD_COMPRA)); ?>">Detalles</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td><?php echo e(isset($usuarios[$compras['Id_usuario']]) ? $usuarios[$compras['Id_usuario']]->Nombre_Usuario : 'Usuario no encontrado'); ?></td>
                                <td><?php echo e($compras['DESC_COMPRA']); ?></td>
                                <td><?php echo e(isset($proyectos[$compras['COD_PROYECTO']]) ? $proyectos[$compras['COD_PROYECTO']]->NOM_PROYECTO : 'Proyecto no encontrado'); ?></td>
                                <td><?php echo e($compras['FEC_REGISTRO']); ?></td>
                                <td><?php echo e($compras['FECHA_PAGO']); ?></td>
                                <td><?php echo e(isset($estadocompras[$compras['COD_ESTADO']]) ? $estadocompras[$compras['COD_ESTADO']]->DESC_ESTADO: 'Estado no encontrada'); ?></td>
                                <td><?php echo e(isset($tipocompras[$compras['COD_TIPO']]) ? $tipocompras[$compras['COD_TIPO']]->DESC_TIPO : 'Compra no encontrada'); ?></td>
                                <td><?php echo e(number_format($compras->PRECIO_COMPRA, 2)); ?></td>
                                <td><?php echo e(number_format($compras->totalDeducciones, 2)); ?></td>
                                <td><?php echo e(number_format($compras->precioFinal, 2)); ?></td>
                                <td><?php echo e($compras['LIQUIDEZ_COMPRA'] == 1 ? 'Sí' : 'No'); ?></td>
                            </tr>

                            <!-- Modal para deducción -->
                            <div class="modal fade" id="deduccionModal<?php echo e($compras['COD_COMPRA']); ?>" tabindex="-1" aria-labelledby="deduccionModalLabel<?php echo e($compras['COD_COMPRA']); ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deduccionModalLabel<?php echo e($compras['COD_COMPRA']); ?>">Agregar Deducción</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        
                                        <div class="modal-body">
                                            <!-- Formulario para agregar la deducción -->
                                            <form action="<?php echo e(route('compras.agregar', $compras['COD_COMPRA'])); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
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


                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="table-responsive">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-center mt-3" id="tituloActivos">LIQUIDACIONES POR REALIZAR</h5>
                    <!-- Botón de filtrado con buscador dinámico -->
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" id="buscarLiquidacion" placeholder="Buscar...">
                        <button class="btn btn-primary" id="btnLiquidar">Liquidar</button>
                    </div>
                </div>
            
                <table class="table table-hover table-bordered dt-responsive nowrap">
                    <thead class="thead-dark">
                        <tr>
                            <th><button id="checkAllBtn" class="btn btn-link p-0">Seleccionar</button></th> <!-- Botón para seleccionar/deseleccionar todas las filas -->
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
                        <?php $__currentLoopData = $Liquidaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compras): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr data-compra-id="<?php echo e($compras->COD_COMPRA); ?>">
                            <td><input type="checkbox" class="checkItem"></td> <!-- Check para cada fila -->
                            <td><?php echo e(isset($usuarios[$compras['Id_usuario']]) ? $usuarios[$compras['Id_usuario']]->Nombre_Usuario : 'Usuario no encontrado'); ?></td>
                            <td><?php echo e($compras['DESC_COMPRA']); ?></td>
                            <td><?php echo e(isset($proyectos[$compras['COD_PROYECTO']]) ? $proyectos[$compras['COD_PROYECTO']]->NOM_PROYECTO : 'Proyecto no encontrado'); ?></td>
                            <td><?php echo e($compras['FEC_REGISTRO']); ?></td>
                            <td><?php echo e(isset($estadocompras[$compras['COD_ESTADO']]) ? $estadocompras[$compras['COD_ESTADO']]->DESC_ESTADO : 'Estado no encontrado'); ?></td>
                            <td><?php echo e(isset($tipocompras[$compras['COD_TIPO']]) ? $tipocompras[$compras['COD_TIPO']]->DESC_TIPO : 'Tipo no encontrado'); ?></td>
                            <td><?php echo e($compras['FECHA_PAGO']); ?></td>
                            <td><?php echo e($compras['PRECIO_COMPRA']); ?></td>
                            <td><?php echo e($compras['PRECIO_CUOTA']); ?></td>
                            <td><?php echo e($compras['PRECIO_NETO']); ?></td>
                            <td><?php echo e($compras['CUOTAS_PAGADAS']); ?></td>
                            <td><?php echo e($compras['TOTAL_CUOTAS']); ?></td>
                            <td><?php echo e($compras['LIQUIDEZ_COMPRA'] == 1 ? 'Sí' : 'No'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <script>
                // Función para seleccionar/deseleccionar todas las filas
                document.getElementById('checkAllBtn').addEventListener('click', function() {
                    let checkItems = document.querySelectorAll('.checkItem');
                    let allChecked = Array.from(checkItems).every(check => check.checked);
                    
                    checkItems.forEach(check => check.checked = !allChecked); // Marca/desmarca todos los checkboxes
                    this.textContent = allChecked ? "Seleccionar" : "Deseleccionar"; // Cambia el texto del botón
                });
            
                // Actualiza el botón según los checkboxes individuales
                document.querySelectorAll('.checkItem').forEach(item => {
                    item.addEventListener('change', function() {
                        let checkItems = document.querySelectorAll('.checkItem');
                        let allChecked = Array.from(checkItems).every(check => check.checked);
                        document.getElementById('checkAllBtn').textContent = allChecked ? "Deseleccionar" : "Seleccionar";
                    });
                });
            
                // Filtrado dinámico de la tabla
                document.getElementById('buscarLiquidacion').addEventListener('keyup', function() {
                    let searchTerm = this.value.toLowerCase();
                    let rows = document.querySelectorAll('#tablaLiquidaciones tr');
                    rows.forEach(row => {
                        let rowText = row.innerText.toLowerCase();
                        row.style.display = rowText.includes(searchTerm) ? '' : 'none';
                    });
                });
                document.getElementById('btnLiquidar').addEventListener('click', function() {
    let selectedItems = Array.from(document.querySelectorAll('.checkItem:checked'))
        .map(item => item.closest('tr').getAttribute('data-compra-id'));

    if (selectedItems.length > 0) {
        if (confirm(`¿Deseas liquidar las ${selectedItems.length} compras seleccionadas?`)) {
            console.log('Enviando solicitud de liquidación');
            fetch('<?php echo e(route("compras.liquidar")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({ compras: selectedItems })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la solicitud: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Recargar la página para reflejar los cambios
                } else {
                    alert(data.message || "Hubo un problema al procesar las liquidaciones.");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Hubo un problema con la solicitud de liquidación. Revisa la consola para más detalles.");
            });
        }
    } else {
        alert("Selecciona al menos una compra para liquidar.");
    }
});

               
            </script>
            
        </div>
    </div>

    <!-- Card para liquidaciones tardadas -->
    <div class="card mb-3 d-none" id="tablaretrasados">
        <div class="card-body">
            <!-- Contenedor para la tabla con barra de desplazamiento horizontal -->
            <div class="table-responsive">
                <table id="tablaretrasados" class="table table-hover table-bordered dt-responsive nowrap">
                    <thead class="thead-dark">
                        <tr>
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
                            <th>LIQUIDEZ</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\carri\OneDrive\Escritorio\proyect\admin-system\resources\views/compras/index.blade.php ENDPATH**/ ?>