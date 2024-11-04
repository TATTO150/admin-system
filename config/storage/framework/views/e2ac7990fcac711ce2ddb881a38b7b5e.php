<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>PANEL GENERAL</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php if($mostrarAlerta): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: 1.2rem; font-weight: bold;">
        <strong style="font-size: 1.5rem; text-transform: uppercase;">⚠️ Advertencia:</strong> 
        <?php echo e(session('alert')); ?>

        <a href="<?php echo e(route('Perfil.edit')); ?>" class="alert-link" style="text-decoration: underline; font-weight: bold;">Actualizar contraseña</a>.
        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

     <!-- Mostrar mensajes de éxito -->
    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>


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
    <div class="row">
    <!-- Card para la cantidad de usuarios -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo e($usuariosCount); ?></h3>
                    <p>Usuarios Registrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="<?php echo e(route('usuarios.index')); ?>" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card para las solicitudes pendientes de revisión -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo e($solicitudesPendientesCount); ?></h3>
                    <p>Solicitudes Pendientes de Revisión</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <a href="<?php echo e(route('gestionSolicitudes.index', ['estado' => 'pendiente'])); ?>" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <!-- Card para los mantenimientos existentes -->
        <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color: #9565f7">
                <div class="inner">
                    <h3><?php echo e($equiposTotalCount); ?></h3>
                    <p>Total de Equipos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-desktop"></i>
                </div>
                <a href="<?php echo e(route('equipos.index')); ?>" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

            <!-- Card para liquidacion de planillas -->
       <div class="col-lg-3 col-6">
        <div class="small-box" style="background-color: #c98ffb">
            <div class="inner">
                <h5><strong>Liquidación Mensual de Planilla</strong></h5>
                <?php if(now()->endOfMonth()->isToday()): ?>
                    <p>Hoy es día de pago de la planilla</p>
                <?php else: ?>
                    <p>Próxima liquidación: <?php echo e(now()->endOfMonth()->locale('es')->translatedFormat('d \d\e F \d\e Y')); ?></p>
                <?php endif; ?>
                
            </div>
            <div class="icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <?php if(now()->endOfMonth()->isToday()): ?>
            <form action="<?php echo e(route('planillas.generar')); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-primary">GENERAR PLANILLA DEL MES</button>
            </form>
            <?php else: ?>
                <a href="<?php echo e(route('planillas.index')); ?>" class="small-box-footer">
                    Mas Informacion <i class="fas fa-arrow-circle-right"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
        
  <!-- Card para la cantidad de proyectos con despliegue de detalles -->
<div class="col-lg-6 col-12">
    <div class="small-box" style="background-color: #007BFF;">
      <div class="inner">
        <h3><?php echo e($proyectosCount); ?></h3>
        <p>Total de Proyectos</p>
      </div>
      <div class="icon">
        <i class="fas fa-folder"></i>
      </div>
      <button class="small-box-footer" type="button" data-toggle="collapse" data-target="#projectDetails">
        Ver detalles <i class="fas fa-arrow-circle-down"></i>
      </button>
      <div class="collapse show" id="projectDetails">
        <ul class="list-group mt-2">
          <li class="list-group-item" style="background-color: #343a40; color: #ffffff; border: 1px solid #ffffff;">
            Activos: <?php echo e($proyectosActivosCount); ?>

          </li>
          <li class="list-group-item" style="background-color: #343a40; color: #ffffff; border: 1px solid #ffffff;">
            Suspendidos: <?php echo e($proyectosSuspendidosCount); ?>

          </li>
          <li class="list-group-item" style="background-color: #343a40; color: #ffffff; border: 1px solid #ffffff;">
            Finalizados: <?php echo e($proyectosFinalizadosCount); ?>

          </li>
          <li class="list-group-item" style="background-color: #343a40; color: #ffffff; border: 1px solid #ffffff;">
            En Apertura: <?php echo e($proyectosAperturaCount); ?>

          </li>
        </ul>
      </div>
      <a href="<?php echo e(route('proyectos.index')); ?>" class="small-box-footer">
        Más información <i class="fas fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>
  
<!-- Gráfico de proyectos por estado -->
<div class="col-lg-6 col-12">
  <div class="card bg-dark text-white">
    <div class="card-header">
      <h3 class="card-title">Proyectos por Estado</h3>
    </div>
    <div class="card-body">
      <canvas id="projectsChart"></canvas>
    </div>
  </div>
</div>


<!-- Gráfico de asignación por estado -->
<div class="col-lg-6 col-12 text-white">
    <div class="card bg-dark">
        <div class="card-header">
            <h3 class="card-title">Asignación por Estado</h3>
        </div>
        <div class="card-body">
            <canvas id="assignmentChart"></canvas>
        </div>
    </div>
</div> 

<div class="row">
    <!-- Card para la cantidad de empleados -->
    <div class="col-12 mb-4">
        <div class="small-box bg-info text-white" style="height: 100%;">
            <div class="inner" style="padding: 25px;">
                <h3 style="font-size: 2.75rem;"><?php echo e($empleadosCount); ?></h3>
                <p style="font-size: 1.4rem;">Total de Empleados</p>
            </div>
            <div class="icon" style="font-size: 3.5rem; top: 15px; right: 15px;">
                <i class="fas fa-user-tie"></i>
            </div>
            <a href="<?php echo e(route('empleados.index')); ?>" class="small-box-footer" style="font-size: 1.1rem; padding: 10px;">
                Más información <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Card para la cantidad de planillas -->
    <div class="col-12 mb-4">
        <div class="small-box bg-warning" style="height: 100%;">
            <div class="inner" style="padding: 25px;">
                <h3 style="font-size: 2.75rem;"><?php echo e($planillasCount); ?></h3>
                <p style="font-size: 1.4rem;">Total de Planillas</p>
            </div>
            <div class="icon" style="font-size: 3.5rem; top: 15px; right: 15px;">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <a href="<?php echo e(route('planillas.index')); ?>" class="small-box-footer" style="font-size: 1.1rem; padding: 10px;">
                Más información <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>
       
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('vendor/adminlte/dist/css/adminlte.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('vendor/adminlte/dist/js/adminlte.min.js')); ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script>
        $(function() {
            // Gráfico de proyectos por estado (doughnut)
            var ctxProjects = document.getElementById('projectsChart').getContext('2d');
            var projectsChart = new Chart(ctxProjects, {
                type: 'doughnut',
                data: {
                    labels: ['Activos', 'Suspendidos', 'Finalizados', 'En Apertura'],
                    datasets: [{
                        data: [
                            <?php echo e($proyectosActivosCount); ?>,
                            <?php echo e($proyectosSuspendidosCount); ?>,
                            <?php echo e($proyectosFinalizadosCount); ?>,
                            <?php echo e($proyectosAperturaCount); ?>

                        ],
                        backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#007bff'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Proyectos por Estado'
                        },
                        datalabels: {
                            formatter: (value, ctx) => {
                                let sum = 0;
                                let dataArr = ctx.chart.data.datasets[0].data;
                                dataArr.map(data => {
                                    sum += data;
                                });
                                let percentage = (value * 100 / sum).toFixed(2) + "%";
                                return value + " (" + percentage + ")";
                            },
                            color: '#fff',
                        }
                    }
                }
            });

            // Gráfico de empleados por proyecto (bar)
            var ctxEmployees = document.getElementById('employeesChart').getContext('2d');
            var employeesChart = new Chart(ctxEmployees, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($empleadosPorProyecto->pluck('NOM_PROYECTO')); ?>,
                    datasets: [{
                        label: 'Cantidad de Empleados',
                        data: <?php echo json_encode($empleadosPorProyecto->pluck('total_empleados')); ?>,
                        backgroundColor: 'rgba(0, 0, 139, 0.8)',
                        borderColor: 'rgba(0, 0, 139, 1)',
                        borderWidth: 1,
                        barThickness: 30, // Grosor mínimo de barras
                        maxBarThickness: 50 // Grosor máximo de barras
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                autoSkip: false,
                                maxRotation: 90,
                                minRotation: 0,
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        title: {
                            display: true,
                            text: 'Empleados por Proyecto'
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            formatter: Math.round,
                            color: 'black',
                        }
                    }
                }
            });
        });

        var ctxAssignment = document.getElementById('assignmentChart').getContext('2d');
    var assignmentChart = new Chart(ctxAssignment, {
        type: 'doughnut',
        data: {
            labels: ['Asignación Activa', 'Asignación Finalizada', 'Mantenimiento Activo', 'Mantenimiento Finalizado'],
            datasets: [{
                data: [
                    <?php echo e($asignacionesActivasCount); ?>,
                    <?php echo e($asignacionesFinalizadasCount); ?>,
                    <?php echo e($mantenimientoActivoCount); ?>,
                    <?php echo e($mantenimientoFinalizadoCount); ?>

                ],
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Asignaciones de Equipos por Estado'
                },
                datalabels: {
                    formatter: (value, ctx) => {
                        let sum = 0;
                        let dataArr = ctx.chart.data.datasets[0].data;
                        dataArr.map(data => {
                            sum += data;
                        });
                        let percentage = (value * 100 / sum).toFixed(2) + "%";
                        return value + " (" + percentage + ")";
                    },
                    color: '#fff',
                }
            }
        }
    });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\carri\OneDrive\Escritorio\proyect\admin-system\resources\views/dashboard.blade.php ENDPATH**/ ?>