<?php $__env->startSection('title', 'Gestión de Solicitudes'); ?>
<?php $__env->startSection('plugins.Sweetalert2', true); ?>

<?php $__env->startSection('content_header'); ?>
    <h1 class="text-center">GESTIÓN DE SOLICITUDES</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mt-4">
        <!-- Mostrar errores -->
        <?php if($errors->any()): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        html: '<?php echo implode('<br>', $errors->all()); ?>',
                    });
                });
            </script>
        <?php endif; ?>

        <!-- Botón para intercambiar tablas -->
        <div class="d-flex justify-content-end mb-4">
            <button id="verOtrasSolicitudesBtn" class="btn btn-primary">Ver Otras Solicitudes</button>
        </div>

        <!-- Card que contiene la tabla -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div id="tablaSolicitudesEspera">
                    <h2>Solicitudes en Espera</h2>
                    <?php if($solicitudesEspera->isNotEmpty()): ?>
                        <div class="table-responsive">
                            <table id="tablaEspera" class="table table-hover table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ACCIÓN</th>
                                        <th>SOLICITANTE</th>
                                        <th>DESCRIPCIÓN SOLICITUD</th>
                                        <th>PROYECTO ASOCIADO</th>
                                        <th>TIPO COMPRA</th>
                                        <th>CANTIDAD CUOTAS</th>
                                        <th>PRECIO CUOTA</th>
                                        <th>PRECIO TOTAL</th>
                                        <th>ESTADO SOLICITUD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $solicitudesEspera; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $solicitud): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo e(route('gestionSolicitudes.gestionar', $solicitud->COD_COMPRA)); ?>" class="btn btn-warning btn-sm">GESTIONAR</a>
                                            </td>
                                            <td><?php echo e($usuarios[$solicitud->Id_usuario]->Usuario ?? 'N/A'); ?></td>
                                            <td><?php echo e($solicitud->DESC_COMPRA); ?></td>
                                            <td><?php echo e($proyectos[$solicitud->COD_PROYECTO]->NOM_PROYECTO ?? 'N/A'); ?></td>
                                            <td><?php echo e($tipos[$solicitud->COD_TIPO]->DESC_TIPO ?? 'N/A'); ?></td>
                                            <td><?php echo e($solicitud->PRECIO_CUOTA); ?></td>
                                            <td><?php echo e($solicitud->TOTAL_CUOTAS); ?></td>
                                            <td><?php echo e($solicitud->PRECIO_COMPRA); ?></td>
                                            <td><?php echo e($estados[$solicitud->COD_ESTADO]->DESC_ESTADO ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Paginación -->
                        <nav id="paginationEspera" class="d-flex justify-content-center">
                            <button id="prevPageEspera" class="btn btn-outline-primary me-2">Anterior</button>
                            <span id="currentPageEspera" class="align-self-center"></span>
                            <button id="nextPageEspera" class="btn btn-outline-primary ms-2">Siguiente</button>
                        </nav>
                    <?php else: ?>
                        <p>No hay solicitudes en espera.</p>
                    <?php endif; ?>
                </div>

                <div id="tablaOtrasSolicitudes" style="display: none;">
                    <h2>Otras Solicitudes</h2>
                    <div class="table-responsive">
                        <table id="tablaOtras" class="table table-hover table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>SOLICITANTE</th>
                                    <th>DESCRIPCIÓN SOLICITUD</th>
                                    <th>PROYECTO ASOCIADO</th>
                                    <th>TIPO COMPRA</th>
                                    <th>CANTIDAD CUOTAS</th>
                                    <th>PRECIO CUOTA</th>
                                    <th>PRECIO TOTAL</th>
                                    <th>ESTADO SOLICITUD</th>
                                    <th>ACCIÓN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $otrasSolicitudes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $solicitud): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($usuarios[$solicitud->Id_usuario]->Usuario ?? 'N/A'); ?></td>
                                        <td><?php echo e($solicitud->DESC_COMPRA); ?></td>
                                        <td><?php echo e($proyectos[$solicitud->COD_PROYECTO]->NOM_PROYECTO ?? 'N/A'); ?></td>
                                        <td><?php echo e($tipos[$solicitud->COD_TIPO]->DESC_TIPO ?? 'N/A'); ?></td>
                                        <td><?php echo e($solicitud->PRECIO_CUOTA); ?></td>
                                        <td><?php echo e($solicitud->TOTAL_CUOTAS); ?></td>
                                        <td><?php echo e($solicitud->PRECIO_COMPRA); ?></td>
                                        <td><?php echo e($estados[$solicitud->COD_ESTADO]->DESC_ESTADO ?? 'N/A'); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('gestionSolicitudes.gestionar', $solicitud->COD_COMPRA)); ?>" class="btn btn-warning btn-sm">GESTIONAR</a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Paginación -->
                    <nav id="paginationOtras" class="d-flex justify-content-center">
                        <button id="prevPageOtras" class="btn btn-outline-primary me-2">Anterior</button>
                        <span id="currentPageOtras" class="align-self-center"></span>
                        <button id="nextPageOtras" class="btn btn-outline-primary ms-2">Siguiente</button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="//code.jquery.com/jquery-3.7.0.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Función para paginar las tablas
        function paginateTable(tableId, paginationId, rowsPerPage) {
            const table = document.getElementById(tableId);
            const rows = table.querySelectorAll('tbody tr');
            const prevButton = document.getElementById('prevPage' + paginationId);
            const nextButton = document.getElementById('nextPage' + paginationId);
            const currentPageLabel = document.getElementById('currentPage' + paginationId);

            let currentPage = 1;
            const rowCount = rows.length;
            const pageCount = Math.ceil(rowCount / rowsPerPage);

            // Mostrar solo las filas de la página actual
            function showPage(page) {
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                rows.forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? '' : 'none';
                });
                currentPageLabel.textContent = 'Pág. ' + page;
            }

            // Eventos de paginación
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

            // Iniciar en la primera página
            showPage(currentPage);
        }

        // Paginación al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            paginateTable('tablaEspera', 'Espera', 6);
            paginateTable('tablaOtras', 'Otras', 6);

            // Alternar entre tablas
            $('#verOtrasSolicitudesBtn').click(function() {
                $('#tablaSolicitudesEspera, #tablaOtrasSolicitudes').toggle();
                if ($('#verOtrasSolicitudesBtn').text() === 'Ver Otras Solicitudes') {
                    $('#verOtrasSolicitudesBtn').text('Ver Solicitudes en Espera');
                } else {
                    $('#verOtrasSolicitudesBtn').text('Ver Otras Solicitudes');
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\carri\OneDrive\Escritorio\proyect\admin-system\resources\views/gestionSolicitudes/index.blade.php ENDPATH**/ ?>