<?php $__env->startSection('title', 'Gestionar Solicitud'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1 class="text-center">GESTIONAR SOLICITUD</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container d-flex justify-content-center">
        <div class="card shadow-sm p-4 mb-5 bg-white rounded" style="width: 60%;">
            <main class="mt-3">
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if(session('success')): ?>
                    <script>
                        Swal.fire({
                            title: "¡Exitoso!",
                            text: "<?php echo e(session('success')); ?>",
                            icon: "success"
                        });
                    </script>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <script>
                        Swal.fire({
                            title: "¡Error!",
                            text: "<?php echo e(session('error')); ?>",
                            icon: "error"
                        });
                    </script>
                <?php endif; ?>

                <div class="card-body">
                  
                 
                    <!-- Ejemplo de tabla -->
                    <h6>Detalles de la Solicitud:</h6>
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Detalle</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Código Solicitud</td>
                                <td><?php echo e($solicitud->COD_COMPRA); ?></td>
                            </tr>
                            <tr>
                                <td>Solicitante</td>
                                <td><?php echo e($usuarios[$solicitud->Id_usuario]->Usuario ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td>Descripción</td>
                                <td><?php echo e($solicitud->DESC_COMPRA); ?></td>
                            </tr>
                            <tr>
                                <td>Proyecto</td>
                                <td><?php echo e($solicitud->proyecto->NOM_PROYECTO ?? 'No disponible'); ?></td>
                            </tr>
                            <tr>
                                <td>Estado Actual</td>
                                <td><?php echo e($estados[$solicitud->COD_ESTADO]->DESC_ESTADO ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td>Presupuesto</td>
                                <td><?php echo e($solicitud->PRECIO_COMPRA); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Botones para aprobar o rechazar -->
                    <form action="<?php echo e(route('gestionSolicitudes.aprobar', $solicitud->COD_COMPRA)); ?>" method="POST" style="display: inline-block;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="btn btn-success btn-sm">APROBAR</button>
                    </form>

                    <form action="<?php echo e(route('gestionSolicitudes.rechazar', $solicitud->COD_COMPRA)); ?>" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Estás seguro de que deseas rechazar esta solicitud?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="btn btn-danger btn-sm">RECHAZAR</button>
                    </form>
                </div>
            </main>
        </div>
    </div>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .card {
            margin-top: 20px;
        }
        .form-label {
            font-weight: bold;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }
        .table tbody + tbody {
            border-top: 2px solid #dee2e6;
        }
        .table-bordered {
            border: 1px solid #dee2e6;
        }
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }
        .table-bordered thead th,
        .table-bordered thead td {
            border-bottom-width: 2px;
        }
        .table-hover tbody tr:hover {
            color: #212529;
            background-color: rgba(0, 0, 0, 0.075);
        }
    </style>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if(session('success')): ?>
            Swal.fire({
                title: "¡Exitoso!",
                text: "<?php echo e(session('success')); ?>",
                icon: "success"
            });
        <?php endif; ?>

        <?php if(session('error')): ?>
            Swal.fire({
                title: "¡Error!",
                text: "<?php echo e(session('error')); ?>",
                icon: "error"
            });
        <?php endif; ?>
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\carri\OneDrive\Escritorio\proyect\admin-system\resources\views/gestionSolicitudes/gestionar.blade.php ENDPATH**/ ?>