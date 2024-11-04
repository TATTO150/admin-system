<?php $__env->startSection('title', 'Libro Diario'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>Registro de Compras</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
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
                    <?php if(is_array($gastos) || is_object($gastos)): ?>
                        <?php $__currentLoopData = $gastos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gasto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($gasto->compra->DESC_COMPRA ?? 'No disponible'); ?></td>
                                <td><?php echo e($gasto->proyecto->NOM_PROYECTO ?? 'No disponible'); ?></td>
                                <td><?php echo e($gasto->FEC_REGISTRO); ?></td>
                                <td><?php echo e($gasto->compra->TIP_COMPRA ?? 'No disponible'); ?></td>
                                <td><?php echo e($gasto->SUBTOTAL); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No se encontraron gastos.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total General</strong></td>
                        <td><strong><?php echo e($totalGastos); ?></strong></td>
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
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger" id="errorAlert">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form id="reporteForm" method="GET" action="" target="_self">
                        <?php echo csrf_field(); ?>

                        <!-- Campo oculto para forzar la misma pestaña si hay errores -->
                        <?php if(session('forceSameTab')): ?>
                            <input type="hidden" id="forceSameTab" value="true">
                        <?php endif; ?>

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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
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
            <?php if($errors->any() && session('modal') && session('forceSameTab')): ?>
                var modal = '<?php echo e(session('modal')); ?>';
                    $('#reporteModal').modal('show');
                    $('#reporteTipo').val(modal).trigger('change');
            <?php endif; ?>

            // Mostrar modal si hay errores
            <?php if($errors->any() && session('modal')): ?>
                var tipoReporte = '<?php echo e(old('reporteTipo', session('modal'))); ?>'; // Mantener el tipo de reporte seleccionado

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
                                <?php $__currentLoopData = $proyectos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proyecto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($proyecto->COD_PROYECTO); ?>" <?php echo e(old('proyecto') == $proyecto->COD_PROYECTO ? 'selected' : ''); ?>><?php echo e($proyecto->NOM_PROYECTO); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    `);
                } else if (tipoReporte === 'mes') {
                    filtros.append(`
                        <div class="mb-3">
                            <label for="mes" class="form-label">Mes</label>
                            <select id="mes" name="mes" class="form-select" required>
                                <option value="">Seleccione un mes</option>
                                <option value="1" <?php echo e(old('mes') == 1 ? 'selected' : ''); ?>>Enero</option>
                                <option value="2" <?php echo e(old('mes') == 2 ? 'selected' : ''); ?>>Febrero</option>
                                <option value="3" <?php echo e(old('mes') == 3 ? 'selected' : ''); ?>>Marzo</option>
                                <option value="4" <?php echo e(old('mes') == 4 ? 'selected' : ''); ?>>Abril</option>
                                <option value="5" <?php echo e(old('mes') == 5 ? 'selected' : ''); ?>>Mayo</option>
                                <option value="6" <?php echo e(old('mes') == 6 ? 'selected' : ''); ?>>Junio</option>
                                <option value="7" <?php echo e(old('mes') == 7 ? 'selected' : ''); ?>>Julio</option>
                                <option value="8" <?php echo e(old('mes') == 8 ? 'selected' : ''); ?>>Agosto</option>
                                <option value="9" <?php echo e(old('mes') == 9 ? 'selected' : ''); ?>>Septiembre</option>
                                <option value="10" <?php echo e(old('mes') == 10 ? 'selected' : ''); ?>>Octubre</option>
                                <option value="11" <?php echo e(old('mes') == 11 ? 'selected' : ''); ?>>Noviembre</option>
                                <option value="12" <?php echo e(old('mes') == 12 ? 'selected' : ''); ?>>Diciembre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="anio" class="form-label">Año</label>
                            <input type="number" id="anio" name="anio" class="form-control" value="<?php echo e(old('anio')); ?>" required>
                        </div>
                    `);
                } else if (tipoReporte === 'año') {
                    filtros.append(`
                        <div class="mb-3">
                            <label for="anio" class="form-label">Año</label>
                            <input type="number" id="anio" name="anio" class="form-control" value="<?php echo e(old('anio')); ?>" required>
                        </div>
                    `);
                } else if (tipoReporte === 'fecha') {
                    filtros.append(`
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" id="fecha" name="fecha" class="form-control" value="<?php echo e(old('fecha')); ?>" required>
                        </div>
                    `);
                }

                // Rellenar el selector de tipo de reporte automáticamente en el modal
                $('#reporteTipo').val(tipoReporte).trigger('change');
            <?php endif; ?>

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
                                <?php $__currentLoopData = $proyectos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proyecto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($proyecto->COD_PROYECTO); ?>"><?php echo e($proyecto->NOM_PROYECTO); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                        action = '<?php echo e(route("gastos.reporte.proyecto")); ?>';
                        break;
                    case 'hoy':
                        action = '<?php echo e(route("gastos.reporte.hoy")); ?>';
                        break;
                    case 'año':
                        action = '<?php echo e(route("gastos.reporte.ano")); ?>';
                        break;
                    case 'mes':
                        action = '<?php echo e(route("gastos.reporte.mes")); ?>';
                        break;
                    case 'general':
                        action = '<?php echo e(route("gastos.pdf")); ?>';
                        break;
                    case 'fecha':
                        action = '<?php echo e(route("gastos.reporte.fecha")); ?>';
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\carri\OneDrive\Escritorio\proyect\admin-system\resources\views/gastos/index.blade.php ENDPATH**/ ?>