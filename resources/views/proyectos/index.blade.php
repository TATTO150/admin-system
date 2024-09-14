@extends('adminlte::page')

@section('title', 'Proyectos')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1>PROYECTO</h1>
@stop

@section('content')
    <div class="container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif


 <!-- Botón para ver proyectos inactivos -->
 <form action="{{ route('proyectos.index') }}" method="GET" class="mb-3">
        <button type="submit" name="ver_inactivos" value="{{ $verInactivos ? 'false' : 'true' }}" class="btn btn-primary">
            {{ $verInactivos ? 'OCULTAR INACTIVOS' : 'VER INACTIVOS' }}
        </button>
        <!-- Puedes añadir otros filtros o controles aquí -->
    </form>



   <!--  <form method="GET" action="{{ route('proyectos.index') }}">
    <div class="form-group">
        <label for="estado_proyecto">Filtrar por Estado del Proyecto:</label>
        <select name="estado_proyecto" id="estado_proyecto" class="form-control">
            <option value="">Todos los Estados</option>
            @foreach($estadosProyecto as $estado)
                <option value="{{ $estado->ESTADO_PROYECTO }}" {{ request('estado_proyecto') == $estado->ESTADO_PROYECTO ? 'selected' : '' }}>
                    {{ $estado->ESTADO_PROYECTO }}
                </option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Filtrar</button>
</form>-->


        <main class="mt-3">
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <a href="{{ route('proyectos.crear') }}" class="btn btn-primary">NUEVO</a>

                   <!-- Botón desplegable para reportes PDF -->

                <!--    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            REPORTE PDF
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('proyectos.pdf') }}" target="_blank">REPORTE GENERAL</a></li>
                            <li><a class="dropdown-item" href="{{ route('proyectos.pdf.fecha') }}" target="_blank">REPORTE FECHA</a></li>
                            <li><a class="dropdown-item" href="{{ route('proyectos.pdf.estado') }}" target="_blank">REPORTE ESTADO</a></li>
                        </ul>
                    </div> -->

<!-- MODAL DE LOS REPORTES -->
<!-- Botón para abrir el modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reporteModal">
    REPORTES
</button>

<!-- Modal -->
<div class="modal fade" id="reporteModal" tabindex="-1" aria-labelledby="reporteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reporteModalLabel">Generar Reporte PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Opción de Reporte General -->
                <h6>
                    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#reporteGeneral">
                        Reporte General
                    </button>
                </h6>
                <div class="collapse" id="reporteGeneral">
                    <form action="{{ route('proyectos.pdf') }}" method="GET" target="_blank">
                        <button type="submit" class="btn btn-primary btn-block mb-3">Generar Reporte General</button>
                    </form>
                </div>

                <!-- Opción de Reporte por Fecha -->
                <h6>
                    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#reporteFecha">
                        Reporte por Fecha
                    </button>
                </h6>
                <div class="collapse" id="reporteFecha">
                    <form action="{{ route('proyectos.pdf.fecha') }}" method="GET" target="_self">
                        <div class="form-group mb-3">
                            <label for="mes">Mes</label>
                            <select name="mes" id="mes" class="form-select" required>
                                <option value="01">Enero</option>
                                <option value="02">Febrero</option>
                                <option value="03">Marzo</option>
                                <option value="04">Abril</option>
                                <option value="05">Mayo</option>
                                <option value="06">Junio</option>
                                <option value="07">Julio</option>
                                <option value="08">Agosto</option>
                                <option value="09">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="anio">Año</label>
                            <input type="number" name="anio" id="anio" class="form-control" min="2000" max="{{ \Carbon\Carbon::now()->year }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Generar Reporte por Fecha</button>
                    </form>
                </div>

                <!-- Opción de Reporte por Estado -->
<h6>
    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#reporteEstado">
        Reporte por Estado
    </button>
</h6>

<div class="collapse" id="reporteEstado">
    <form action="{{ route('proyectos.pdf.estado') }}" method="GET" target="_blank">
        <select name="estado" class="form-select mb-3" required>
            <option value="TODOS">TODOS</option>
            @foreach ($estadosProyecto as $estado)
                <option value="{{ $estado->ESTADO_PROYECTO }}">{{ $estado->ESTADO_PROYECTO }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-block">Generar Reporte por Estado</button>
    </form>
</div>
            </div>
        </div>
    </div>
</div>
 <!--FIN DEL MODAL-->
    </div>

        
    
    
                <!-- CSS DEL DISEÑO DE MODAL REPORTES -->
<!-- <style>
    .button-container {
        display: flex;
        gap: 10px; /* Espacio entre los botones */
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s, box-shadow 0.3s;
        color: #fff;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-create {
        background-color: #007bff; /* Azul brillante */
    }

    .btn-report {
        background-color: #28a745; /* Verde brillante */
    }

    .btn:hover {
        background-color: #0056b3; /* Azul más oscuro para CREAR */
    }

    .btn-report:hover {
        background-color: #218838; /* Verde más oscuro para REPORTE PDF */
    }

    .btn:active {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        transform: translateY(2px);
    }
</style> -->

                <!-- Formulario de filtro -->
                <form action="{{ route('proyectos.index') }}" method="GET" class="form-inline">
    <div class="form-group mr-2">
        <label for="estado_proyecto" class="mr-2">ESTADO:</label>
        <select name="estado_proyecto" id="estado_proyecto" class="form-control">
            <option value="">TODOS</option>
            @foreach ($estadosProyecto as $estado)
                @if ($estado->ESTADO_PROYECTO !== 'INACTIVO')
                    <option value="{{ $estado->ESTADO_PROYECTO }}" {{ request('estado_proyecto') == $estado->ESTADO_PROYECTO ? 'selected' : '' }}>
                        {{ $estado->ESTADO_PROYECTO }}
                    </option>
                @endif
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">FILTRAR</button>
</form>
            </div>
            
            <div id="proyectosVencidos" style="display: none;">
                <h2>PROYECTOS QUE CUMPLIERON LA FECHA PROYECTOS</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>NOMBRE PROYECTO</th>
                            <th>FECHA INICIO</th>
                            <th>FECHA FINAL</th>
                            <th>DESCRIPCION PROYECTO</th>
                            <th>PRESUPUESTO INICIO</th>
                            <th>ESTADO PROYECTO</th>
                            <th>ACCION</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($proyectosVencidos as $proyecto)
    <tr>
        <td>{{ $proyecto->NOM_PROYECTO }}</td>
        <td>{{ $proyecto->FEC_INICIO }}</td>
        <td>{{ $proyecto->FEC_FINAL }}</td>
        <td>{{ $proyecto->DESC_PROYECTO }}</td>
        <td>{{ $proyecto->PRESUPUESTO_INICIO }}</td>
        <td style="color: {{ $obtenerColorEstado($proyecto->ESTADO_PROYECTO) }}">
            {{ $proyecto->ESTADO_PROYECTO }}
        </td>
        <td>
            <form action="{{ route('proyectos.finalizar', $proyecto->COD_PROYECTO) }}" method="POST" style="display: inline-block;">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-warning btn-sm">FINALIZAR</button>
            </form>
        </td>
    </tr>
@endforeach
                    </tbody>
                </table>
            </div>

            
<!-- ESTE CSS ES PARA LA CONFIGURACION DE LA DESCRIPCION -->
            <style>
    .table td {
        word-wrap: break-word;
        overflow-wrap: break-word;
        max-width: 150px;
        white-space: normal;
    }

    .table td .descripcion {
        position: relative;
        max-width: 300px; /* Ajusta según sea necesario */
    }

    .table td .descripcion .fecha-oculta {
        display: none;
        white-space: normal; /* Permite el salto de línea */
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .table td .descripcion .btn-fecha,
    .table td .descripcion .btn-menos {
        display: block;
        text-align: center;
        margin: 5px 0;
        font-size: 0.9rem;
        text-decoration: underline;
        color: #007bff;
        cursor: pointer;
    }

    .table td .descripcion .btn-menos {
        display: none;
    }

    .table td .descripcion .descripcion-completa {
        display: none;
    }
</style>


<h2>TODOS LOS PROYECTOS</h2>
<table id="mitabla" class="table table-striped">
    <thead>
        <tr>
            <th>NOMBRE PROYECTO</th>
            <th>FECHA INICIO</th>
            <th>FECHA FINAL</th>
            <th>DESCRIPCION PROYECTO</th>
            <th>PRESUPUESTO INICIO</th>
            <th>ESTADO PROYECTO</th>
            <th>ACCION</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($proyectos as $proyecto)
        <tr>
            <td>{{ $proyecto->NOM_PROYECTO }}</td>
            <td>{{ $proyecto->FEC_INICIO }}</td>
            <td>{{ $proyecto->FEC_FINAL }}</td>
            <td>
                @php
                    $descripcion = $proyecto->DESC_PROYECTO;
                    $descripcion_truncada = strlen($descripcion) > 30 ? substr($descripcion, 0, 30) . '...' : $descripcion;
                @endphp
                <div class="descripcion">
                    <span class="descripcion-corta" id="descripcionCorta{{ $proyecto->COD_PROYECTO }}">{{ $descripcion_truncada }}</span>
                    @if(strlen($proyecto->DESC_PROYECTO) > 30)
                    <span class="btn-fecha" id="btnMostrarMas{{ $proyecto->COD_PROYECTO }}" onclick="mostrarDescripcionCompleta({{ $proyecto->COD_PROYECTO }})">Mostrar más</span>
                    <span class="descripcion-completa" id="descripcionCompleta{{ $proyecto->COD_PROYECTO }}">{{ $proyecto->DESC_PROYECTO }}</span>
                    <span class="btn-menos" id="btnMenos{{ $proyecto->COD_PROYECTO }}" onclick="ocultarDescripcionCompleta({{ $proyecto->COD_PROYECTO }})">Mostrar menos</span>
                    @endif
                </div>
            </td>
            <td>{{ $proyecto->PRESUPUESTO_INICIO }}</td>
            <td style="color: {{ $obtenerColorEstado($proyecto->ESTADO_PROYECTO) }}">
                {{ $proyecto->ESTADO_PROYECTO }}
              </td>     
             <td>


                @if($proyecto->ESTADO_PROYECTO !== 'INACTIVO')
                <!-- Dropdown Button -->
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{ $proyecto->COD_PROYECTO }}" data-bs-toggle="dropdown" aria-expanded="false">
                        Acciones
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $proyecto->COD_PROYECTO }}">
                        @if($proyecto->ESTADO_PROYECTO === 'SUSPENDIDO')
                            <li><a class="dropdown-item" href="#" onclick="confirmActivation({{ $proyecto->COD_PROYECTO }})">ACTIVAR</a></li>
                            <li>
                                <form action="{{ route('proyectos.destroy', $proyecto->COD_PROYECTO) }}" method="POST" class="d-inline" onsubmit="return confirmDeletion()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item">ELIMINAR</button>
                                </form>
                            </li>
                        @elseif($proyecto->ESTADO_PROYECTO === 'APERTURA' || $proyecto->ESTADO_PROYECTO === 'FINALIZADO')
                            <li>
                                <form action="{{ route('proyectos.destroy', $proyecto->COD_PROYECTO) }}" method="POST" class="d-inline" onsubmit="return confirmDeletion()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item">ELIMINAR</button>
                                </form>
                            </li>
                            @if($proyecto->ESTADO_PROYECTO !== 'FINALIZADO')
                                <li><a class="dropdown-item" href="{{ route('proyectos.edit', $proyecto->COD_PROYECTO) }}">EDITAR</a></li>
                                <li><button class="dropdown-item" onclick="openModal({{ $proyecto->COD_PROYECTO }})">ASIGNAR EMPLEADO</button></li>
                                <li><a class="dropdown-item" href="{{ route('proyectos.empleados', $proyecto->COD_PROYECTO) }}">VER EMPLEADOS</a></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('reporte.proyecto.general', ['proyectoId' => $proyecto->COD_PROYECTO]) }}" target="_blank">GENERAR PDF</a></li>
                        @else
                            @if($proyecto->ESTADO_PROYECTO !== 'FINALIZADO')
                                <li><a class="dropdown-item" href="{{ route('proyectos.edit', $proyecto->COD_PROYECTO) }}">EDITAR</a></li>
                                <li><button class="dropdown-item" onclick="openModal({{ $proyecto->COD_PROYECTO }})">ASIGNAR EMPLEADO</button></li>
                                <li><a class="dropdown-item" href="{{ route('proyectos.empleados', $proyecto->COD_PROYECTO) }}">VER EMPLEADOS</a></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('reporte.proyecto.general', ['proyectoId' => $proyecto->COD_PROYECTO]) }}" target="_blank">GENERAR PDF</a></li>
                        @endif
                    </ul>
                </div>
                @endif

                
              </td>
              </tr>
                @endforeach
               </tbody>
</table>



<script>
    function mostrarDescripcionCompleta(id) {
        var descripcionCompleta = document.getElementById('descripcionCompleta' + id);
        var btnMostrarMas = document.getElementById('btnMostrarMas' + id);
        var btnMostrarMenos = document.getElementById('btnMenos' + id);
        
        if (descripcionCompleta) {
            descripcionCompleta.style.display = 'inline';
        }
        if (btnMostrarMas) {
            btnMostrarMas.style.display = 'none';
        }
        if (btnMostrarMenos) {
            btnMostrarMenos.style.display = 'inline';
        }
    }

    function ocultarDescripcionCompleta(id) {
        var descripcionCompleta = document.getElementById('descripcionCompleta' + id);
        var btnMostrarMas = document.getElementById('btnMostrarMas' + id);
        var btnMostrarMenos = document.getElementById('btnMenos' + id);
        
        if (descripcionCompleta) {
            descripcionCompleta.style.display = 'none';
        }
        if (btnMostrarMas) {
            btnMostrarMas.style.display = 'inline';
        }
        if (btnMostrarMenos) {
            btnMostrarMenos.style.display = 'none';
        }
    }
</script>


<script>
    // Función para confirmar la activación del proyecto
    function confirmActivation(proyectoId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Quieres activar este proyecto?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, activar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('/proyectos/activar/' + proyectoId, {_token: '{{ csrf_token() }}'}, function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Activado!',
                            response.message,
                            'success'
                        ).then(() => {
                            location.reload(); // Opcional: recarga la página para actualizar el estado
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            'Error al activar el proyecto',
                            'error'
                        );
                    }
                });
            }
        });
    }

    // Función para confirmar la eliminación del proyecto
    function confirmDeletion(proyectoId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Quieres eliminar este proyecto?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('/proyectos/eliminar/' + proyectoId, {_token: '{{ csrf_token() }}'}, function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Eliminado!',
                            response.message,
                            'success'
                        ).then(() => {
                            location.reload(); // Opcional: recarga la página para actualizar la lista
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            'Error al eliminar el proyecto',
                            'error'
                        );
                    }
                });
            }
        });
    }
 </script>


 </tbody>
            </table>
        </main>
 </div>

<!-- Modal -->
<div class="modal fade" id="modalAsignarEmpleado" tabindex="-1" role="dialog" aria-labelledby="modalAsignarEmpleadoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAsignarEmpleadoLabel">ASIGNAR EMPLEADO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('proyectos.asignarEmpleado') }}" method="POST">
                    @csrf
                    <input type="hidden" name="proyecto_id" id="proyecto_id">
                    
                    <!-- Buscador de empleados -->
                    <div class="form-group mb-3">
                        <label for="searchEmpleado">Buscar Empleados</label>
                        <input type="text" id="searchEmpleado" class="form-control" placeholder="Buscar empleados...">
                    </div>
                    
                    <!-- Lista de empleados con desplazamiento -->
                    <div class="form-group">
                        <label for="empleados">Seleccionar Empleados</label>
                        <div id="empleadosList" class="list-group" style="max-height: 300px; overflow-y: auto;">
                            @foreach ($empleados as $empleado)
                                <div class="list-group-item">
                                    <input type="checkbox" class="empleado-checkbox" name="empleados[]" value="{{ $empleado->COD_EMPLEADO }}">
                                    {{ $empleado->NOM_EMPLEADO }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">ASIGNAR</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    function openModal(proyectoId, empleadosAsignados = []) {
        document.getElementById('proyecto_id').value = proyectoId;
        var myModal = new bootstrap.Modal(document.getElementById('modalAsignarEmpleado'));
        myModal.show();

        // Esperar a que el modal se abra para asegurar que los elementos estén en el DOM
        $('#modalAsignarEmpleado').on('shown.bs.modal', function () {
            // Seleccionar checkboxes pre-asignados
            $('.empleado-checkbox').each(function() {
                const checkboxValue = $(this).val();
                $(this).prop('checked', empleadosAsignados.includes(checkboxValue));
            });
        });
    }

    // Buscador de empleados
    $(document).ready(function() {
        $('#searchEmpleado').on('input', function() {
            var query = $(this).val().toLowerCase();
            $('#empleadosList .list-group-item').each(function() {
                var itemText = $(this).text().toLowerCase();
                $(this).toggle(itemText.indexOf(query) > -1);
            });
        });
    });

</script>




@section('css')
    <style>
       /* Estilo general de la página */
.container {
    padding: 20px;
    background-color: #f4f4f4; /* Fondo gris claro */
    border-radius: 10px; /* Bordes redondeados para el contenedor */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra sutil */
}

h1 {
    font-size: 2.2rem;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}

h2 {
    font-size: 1.6rem;
    color: #444;
    margin-top: 20px;
    margin-bottom: 15px;
}

/* Estilo para el formulario de filtro */
.form-inline {
    margin-bottom: 20px;
    padding: 15px;
    background-color: #ffffff; /* Fondo blanco para el formulario */
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15); /* Sombra más sutil */
}

.form-control {
    border-radius: 6px;
    border: 1px solid #ddd; /* Borde gris claro */
}

.form-group label {
    font-weight: 600;
}

/* Estilo para las tablas */
.table {
    margin-bottom: 0;
    border-radius: 8px;
    overflow: hidden;
    background-color: #ffffff; /* Fondo blanco para la tabla */
}

.table thead th {
    background-color: #343a40; /* Gris oscuro */
    color: #ffffff;
    text-align: center;
    padding: 12px;
}

.table tbody tr:hover {
    background-color: #e9ecef; /* Gris claro al pasar el cursor */
}

.table tbody td {
    vertical-align: middle;
    padding: 10px;
    text-align: center;
}

/* Estilo para los botones */
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    border-radius: 6px;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
}

.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    border-radius: 6px;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}


/* Estilo para el modal de reportes*/
/*.modal-content {
    border-radius: 10px;
}

.modal-header {
    background-color: #007bff;
    color: #ffffff;
    border-bottom: none;
    border-radius: 10px 10px 0 0;
}

.modal-title {
    font-size: 1.3rem;
}

.modal-body {
    padding: 25px;
}*/

/* Estilo para la lista de empleados */
.list-group-item {
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    margin-bottom: 8px;
    background-color: #f8f9fa; /* Fondo gris muy claro */
}

.list-group-item input[type="checkbox"] {
    margin-right: 12px;
}

    </style>
@stop
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@stop


@section('js')
@section('js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.3.0/js/dataTables.buttons.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.3.0/js/buttons.html5.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.3.0/js/buttons.print.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.3.0/js/buttons.colVis.min.js"></script>
    <script src="//cdn.datatables.net/buttons/2.3.0/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@stop

