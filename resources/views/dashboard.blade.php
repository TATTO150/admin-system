@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>PANEL GENERAL</h1>
    <button type="button" class="btn btn-info mb-3" data-toggle="modal" data-target="#helpModal">
        AYUDA <i class="fas fa-question-circle"></i>
    </button>
</div>
@stop

@section('content')

@if ($mostrarAlerta)
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: 1.2rem; font-weight: bold;">
        <strong style="font-size: 1.5rem; text-transform: uppercase;">⚠️ Advertencia:</strong> 
        {{ session('alert') }}
        <a href="{{ route('Perfil.edit') }}" class="alert-link" style="text-decoration: underline; font-weight: bold;">Actualizar contraseña</a>.
        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

     <!-- Mostrar mensajes de éxito -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


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
    <div class="row">
    <!-- Card para la cantidad de usuarios -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $usuariosCount }}</h3>
                    <p>Usuarios Registrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('usuarios.index') }}" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card para las solicitudes pendientes de revisión -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $solicitudesPendientesCount }}</h3>
                    <p>Solicitudes Pendientes de Revisión</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <a href="{{ route('gestionSolicitudes.index', ['estado' => 'pendiente']) }}" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <!-- Card para los mantenimientos existentes -->
        <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color: #9565f7">
                <div class="inner">
                    <h3>{{ $equiposTotalCount }}</h3>
                    <p>Total de Equipos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-desktop"></i>
                </div>
                <a href="{{ route('equipos.index') }}" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

            <!-- Card para liquidacion de planillas -->
       <div class="col-lg-3 col-6">
        <div class="small-box" style="background-color: #c98ffb">
            <div class="inner">
                <h5><strong>Liquidación Mensual de Planilla</strong></h5>
                @if (now()->endOfMonth()->isToday())
                    <p>Hoy es día de pago de la planilla</p>
                @else
                    <p>Próxima liquidación: {{ now()->endOfMonth()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}</p>
                @endif
                
            </div>
            <div class="icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            @if (now()->endOfMonth()->isToday())
            <form action="{{ route('planillas.generar') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary">GENERAR PLANILLA DEL MES</button>
            </form>
            @else
                <a href="{{ route('planillas.index') }}" class="small-box-footer">
                    Mas Informacion <i class="fas fa-arrow-circle-right"></i>
                </a>
            @endif
        </div>
    </div>
        
  <!-- Card para la cantidad de proyectos con despliegue de detalles -->
<div class="col-lg-6 col-12">
    <div class="small-box" style="background-color: #007BFF;">
      <div class="inner">
        <h3>{{ $proyectosCount }}</h3>
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
            Activos: {{ $proyectosActivosCount }}
          </li>
          <li class="list-group-item" style="background-color: #343a40; color: #ffffff; border: 1px solid #ffffff;">
            Suspendidos: {{ $proyectosSuspendidosCount }}
          </li>
          <li class="list-group-item" style="background-color: #343a40; color: #ffffff; border: 1px solid #ffffff;">
            Finalizados: {{ $proyectosFinalizadosCount }}
          </li>
          <li class="list-group-item" style="background-color: #343a40; color: #ffffff; border: 1px solid #ffffff;">
            En Apertura: {{ $proyectosAperturaCount }}
          </li>
        </ul>
      </div>
      <a href="{{ route('proyectos.index') }}" class="small-box-footer">
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
                <h3 style="font-size: 2.75rem;">{{ $empleadosCount }}</h3>
                <p style="font-size: 1.4rem;">Total de Empleados</p>
            </div>
            <div class="icon" style="font-size: 3.5rem; top: 15px; right: 15px;">
                <i class="fas fa-user-tie"></i>
            </div>
            <a href="{{ route('empleados.index') }}" class="small-box-footer" style="font-size: 1.1rem; padding: 10px;">
                Más información <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Card para la cantidad de planillas -->
    <div class="col-12 mb-4">
        <div class="small-box bg-warning" style="height: 100%;">
            <div class="inner" style="padding: 25px;">
                <h3 style="font-size: 2.75rem;">{{ $planillasCount }}</h3>
                <p style="font-size: 1.4rem;">Total de Planillas</p>
            </div>
            <div class="icon" style="font-size: 3.5rem; top: 15px; right: 15px;">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <a href="{{ route('planillas.index') }}" class="small-box-footer" style="font-size: 1.1rem; padding: 10px;">
                Más información <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>
       
    </div>
<!-- Modal de Ayuda -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helpModalLabel">Manuales de Ayuda</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Accede a la documentación de ayuda específica para tu rol en la empresa:</p>
                <ul class="list-unstyled">
                    @if ($role === 'ADMINISTRADOR')
                        <li class="manual-item">
                            <span><strong><i class="fas fa-user-shield"></i> ADMINISTRADOR:</strong> Documentación completa para la gestión administrativa.</span>
                            <a href="https://drive.google.com/file/d/1h7xajlqjxc-UQnhCns6ssQUEtDeHrWTHv0GgYC4N7_Q/view?usp=sharing" target="_blank" class="btn btn-primary btn-sm ml-2">Ver Manual <i class="fas fa-eye"></i></a>
                            <a href="https://drive.google.com/uc?export=download&id=11h7xajlqjxc-UQnhCns6ssQUEtDeHrWTHv0GgYC4N7_Q" target="_blank" class="btn btn-success btn-sm">Descargar <i class="fas fa-download"></i></a>
                        </li>
                    @endif
                    @if ($role === 'PROYECTOS' || $role === 'ADMINISTRADOR')
                        <li class="manual-item">
                            <span><strong><i class="fas fa-project-diagram"></i> PROYECTO:</strong> Guía para la gestión de proyectos y tareas.</span>
                            <a href="https://drive.google.com/file/d/1uBsYsedZyRWLok4jeQnfedCY-JrufdHo0ajUgExUSPw/view?usp=sharing" target="_blank" class="btn btn-primary btn-sm ml-2">Ver Manual <i class="fas fa-eye"></i></a>
                            <a href="https://drive.google.com/uc?export=download&id=1uBsYsedZyRWLok4jeQnfedCY-JrufdHo0ajUgExUSPw" target="_blank" class="btn btn-success btn-sm">Descargar <i class="fas fa-download"></i></a>
                        </li>
                    @endif
                    @if ($role === 'INGENIERO SUPERVISOR' || $role === 'ADMINISTRADOR')
                        <li class="manual-item">
                            <span><strong><i class="fas fa-hard-hat"></i> INGENIERO SUPERVISOR:</strong> Procedimientos y regulaciones de supervisión.</span>
                            <a href="https://drive.google.com/file/d/1N7p3V79sVc4O91XQndX2GiZ7QvfXYrJHuZyaUw9ljAc/view?usp=sharing" target="_blank" class="btn btn-primary btn-sm ml-2">Ver Manual <i class="fas fa-eye"></i></a>
                            <a href="https://drive.google.com/uc?export=download&id=1N7p3V79sVc4O91XQndX2GiZ7QvfXYrJHuZyaUw9ljAc" target="_blank" class="btn btn-success btn-sm">Descargar <i class="fas fa-download"></i></a>
                        </li>
                    @endif
                    @if ($role === 'MANTENIMIENTO' || $role === 'ADMINISTRADOR')
                        <li class="manual-item">
                            <span><strong><i class="fas fa-tools"></i> MANTENIMIENTO:</strong> Manual de procedimientos para el mantenimiento.</span>
                            <a href="https://drive.google.com/file/d/1m-fQiZyJZPKxzlcFmda_ZNJcsMbZbb3-5bnolFcCF8o/view?usp=sharing" target="_blank" class="btn btn-primary btn-sm ml-2">Ver Manual <i class="fas fa-eye"></i></a>
                            <a href="https://drive.google.com/uc?export=download&id=1m-fQiZyJZPKxzlcFmda_ZNJcsMbZbb3-5bnolFcCF8o" target="_blank" class="btn btn-success btn-sm">Descargar <i class="fas fa-download"></i></a>
                        </li>
                    @endif
                    @if (!in_array($role, ['ADMINISTRADOR', 'PROYECTOS', 'INGENIERO SUPERVISOR', 'MANTENIMIENTO']))
                        <p class="text-danger">No tienes acceso a los manuales.</p>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>


@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <style>
        .manual-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .manual-item span {
            flex: 1;
        }
        .manual-item a {
            margin-left: 10px;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                            {{ $proyectosActivosCount }},
                            {{ $proyectosSuspendidosCount }},
                            {{ $proyectosFinalizadosCount }},
                            {{ $proyectosAperturaCount }}
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
                    labels: {!! json_encode($empleadosPorProyecto->pluck('NOM_PROYECTO')) !!},
                    datasets: [{
                        label: 'Cantidad de Empleados',
                        data: {!! json_encode($empleadosPorProyecto->pluck('total_empleados')) !!},
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
                    {{ $asignacionesActivasCount }},
                    {{ $asignacionesFinalizadasCount }},
                    {{ $mantenimientoActivoCount }},
                    {{ $mantenimientoFinalizadoCount }}
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
    document.addEventListener('DOMContentLoaded', function () {
        // Obtener el rol del usuario
        const role = @json($role);

        // Seleccionar solo los enlaces dentro del modal de ayuda
        document.querySelectorAll('#helpModal a').forEach(link => {
            link.addEventListener('click', function (event) {
                // Verifica si el usuario tiene permiso para ver el manual según su rol
                if ((role === 'PROYECTO' && !this.href.includes('15UspIpRsTlxH3ljQa6iz05Gq4MbRjsGEa1yWaysX2OY')) ||
                    (role === 'INGENIERO SUPERVISOR' && !this.href.includes('1zIIVJortpP9WzGxLqTYQyTaOS3he2sLBSwRRCSZNIcc')) ||
                    (role === 'MANTENIMIENTO' && !this.href.includes('1BWB2NvLyzFN7yNt5zuLIQvkMGLE4QDR57gmjbQ7n9WU'))) {
                    
                    // Mostrar alerta y prevenir la navegación
                    event.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Acceso Denegado',
                        text: 'No tienes permiso para ver este manual.'
                    });
                }
            });
        });
    });
    </script>
@stop
