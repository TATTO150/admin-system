@extends('adminlte::page')

@section('title', 'Solicitudes')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1 class="text-center">SOLICITUDES</h1>
@stop

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Mensajes de éxito -->
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

                <!-- Mensajes de error -->
                @if(session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: '¡Error!',
                                text: '{{ session('error') }}',
                            });
                        });
                    </script>
                @endif

                <!-- Mostrar errores de validación -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('solicitudes.crear') }}" class="btn btn-success">NUEVO</a>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reportModal">Reporte</button>
                </div>
                <!-- Filtro y búsqueda -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <!-- Formulario de búsqueda activa -->
    <form id="searchForm" class="form-inline">
        <div class="form-group mr-2">
            <label for="searchInput" class="mr-2">Buscar:</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Ingrese un término de búsqueda">
        </div>
    </form>
</div>

            <table id="mitabla" class="table table-hover table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>SOLICITANTE</th>
                        <th>DESCRIPCIÓN COMPRA</th>
                        <th>PROYECTO</th>
                        <th>ESTADO SOLICITUD</th>
                        <th>TIPO COMPRA</th>
                        <th>TOTAL CUOTAS</th>
                        <th>PRECIO CUOTA</th>
                        <th>PRECIO COMPRA</th>
                        <th>ACCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($compras as $compra)
                        <tr>
                            <td>{{ $usuarios[$compra->Id_usuario]->Usuario ?? 'N/A' }}</td>
                            <td>{{ $compra->DESC_COMPRA }}</td>
                            <td>{{ $proyectos[$compra->COD_PROYECTO]->NOM_PROYECTO ?? 'N/A' }}</td>
                            <td>{{ $estados[$compra->COD_ESTADO]->DESC_ESTADO ?? 'N/A' }}</td>
                            <td>{{ $tipos[$compra->COD_TIPO]->DESC_TIPO ?? 'N/A' }}</td>
                            <td>{{ $compra->TOTAL_CUOTAS }}</td>
                            <td>{{ $compra->PRECIO_CUOTA }}</td>
                            <td>{{ $compra->PRECIO_COMPRA }}</td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{ $compra->COD_COMPRA }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $compra->COD_COMPRA }}">
                                        <li><a class="dropdown-item" href="{{ route('solicitudes.edit', $compra->COD_COMPRA) }}">EDITAR</a></li>
                                        <li>
                                            <form class="dropdown-item" action="{{ route('solicitudes.destroy', $compra->COD_COMPRA) }}" method="POST">
                                                @csrf
                                                @method('DELETE') <!-- Este campo oculta el método DELETE -->
                                                <button type="submit">ELIMINAR</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron compras.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            </div>

             <!-- Paginación -->
            <nav id="paginationExample" class="d-flex justify-content-center mt-3">
                <button id="prevPage" class="btn btn-outline-primary me-2">Anterior</button>
                <span id="currentPage" class="align-self-center"></span>
                <button id="nextPage" class="btn btn-outline-primary ms-2">Siguiente</button>
            </nav>
        </div>
    </div>

    <!-- Modal para Generar Reporte -->
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">Generar Reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenedor para mostrar mensajes de error dentro del modal -->
                    <div id="errorContainer" class="alert alert-danger d-none"></div>

                    <form id="reporteForm" onsubmit="return enviarFormulario(event, '{{ route('solicitudes.generateReport') }}')">
                        @csrf
                        <div class="mb-3">
                            <label for="reportType" class="form-label">Tipo de Reporte</label>
                            <select id="reportType" name="reportType" class="form-select" required>
                                <option value="">Seleccione un tipo de reporte</option>
                                <option value="general">General</option>
                                <option value="proyecto">Por Proyecto</option>
                                <option value="area">Por Estado</option>
                            </select>
                        </div>
                        <div id="filtrosAdicionales">
                            <!-- Opciones adicionales dinámicas -->
                            <div id="proyectoFiltro" style="display: none;">
                                <label for="proyectoSelect" class="form-label">Selecciona el Proyecto</label>
                                <select id="proyectoSelect" name="proyecto" class="form-select">
                                    @foreach($proyectos as $proyecto)
                                        <option value="{{ $proyecto->COD_PROYECTO }}">{{ $proyecto->NOM_PROYECTO }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="areaFiltro" style="display: none;">
                                <label for="areaSelect" class="form-label">Selecciona el Área</label>
                                <select id="areaSelect" name="area" class="form-select">
                                    @foreach($areas as $area)
                                        <option value="{{ $area->COD_AREA }}">{{ $area->NOM_AREA }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Generar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="//code.jquery.com/jquery-3.7.0.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
         // Función para paginar la tabla
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
        paginateTable('mitabla', 6); // Cambiar el número de filas por página si es necesario
    });
           // Script de búsqueda mejorado
      $(document).ready(function() {
          // Guardar las filas originales para poder restaurarlas
          var originalRows = $('#mitabla tbody').html();

          // Funcionalidad de búsqueda personalizada
          $('#searchInput').on('input', function() {
              var searchTerm = $(this).val().toLowerCase().trim();
              var hasResults = false;

              // Restaurar filas originales cada vez que cambia el término de búsqueda
              $('#mitabla tbody').html(originalRows);

              if (searchTerm.length > 0) {
                  $('#mitabla tbody tr').each(function() {
                      var rowText = $(this).text().toLowerCase().replace(/\s+/g, ' ');
                      if (rowText.includes(searchTerm)) {
                          $(this).show(); // Mostrar la fila si coincide
                          hasResults = true;
                      } else {
                          $(this).hide(); // Ocultar la fila si no coincide
                      }
                  });

                  // Mostrar mensaje de "No se encontraron coincidencias" si no hay resultados visibles
                  if (!hasResults) {
                      $('#mitabla tbody').html(
                          '<tr><td colspan="7" class="text-center">No se encontraron coincidencias.</td></tr>'
                      );
                  }
              }
          });
      });
      function mostrarError(mensaje) {
    var errorContainer = document.getElementById('errorContainer');
    errorContainer.textContent = mensaje;
    errorContainer.classList.remove('d-none');
}
function enviarFormulario(event, url) {
    event.preventDefault(); // Prevenir el comportamiento por defecto del formulario

    var formData = new FormData(event.target); // Capturar los datos del formulario

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => { throw data; });
        }
        return response.blob(); // Convertir la respuesta a un Blob para manejar archivos
    })
    .then(blob => {
        var url = window.URL.createObjectURL(blob); // Crear una URL para el archivo
        var a = document.createElement('a'); // Crear un elemento <a> para la descarga
        a.href = url;
        a.target = '_blank'; // Abrir el PDF en una nueva pestaña
        a.click(); // Simular un clic en el enlace
    })
    .catch(error => {
        if (error.message) {
            mostrarError(error.message); // Mostrar el error en el contenedor de errores
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al generar el reporte. Por favor, inténtalo nuevamente.'
            });
        }
    });

    return false; // Prevenir el envío del formulario
}

function mostrarError(mensaje) {
    var errorContainer = document.getElementById('errorContainer');
    errorContainer.textContent = mensaje; // Mostrar el mensaje de error en el contenedor
    errorContainer.classList.remove('d-none'); // Asegurarse de que el contenedor esté visible
}

$(document).ready(function() {
    $('#reportModal').on('hidden.bs.modal', function () {
        $('#errorContainer').text('').addClass('d-none'); // Limpiar el mensaje de error al cerrar el modal
    });

    $('#reportType').on('change', function() {
        const selectedType = $(this).val();
        $('#proyectoFiltro').hide();
        $('#areaFiltro').hide();

        if (selectedType === 'proyecto') {
            $('#proyectoFiltro').show(); // Mostrar el filtro de proyecto si es seleccionado
        } else if (selectedType === 'area') {
            $('#areaFiltro').show(); // Mostrar el filtro de área si es seleccionado
        }
    });
});

$(document).ready(function() {
    // Restablecer el contenedor de errores cuando se cierre el modal
    $('#reportModal').on('hidden.bs.modal', function () {
        $('#errorContainer').text('').addClass('d-none');
    });

    $('#reportType').on('change', function() {
        const selectedType = $(this).val();
        $('#proyectoFiltro').hide();
        $('#areaFiltro').hide();

        if (selectedType === 'proyecto') {
            $('#proyectoFiltro').show();
        } else if (selectedType === 'area') {
            $('#areaFiltro').show();
        }
    });
});

    </script>
@stop
