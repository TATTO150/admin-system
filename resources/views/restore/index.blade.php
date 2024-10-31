@extends('adminlte::page')

@section('title', 'Restaurar Base de Datos')

@section('content_header')
    <h1 class="text-center">Restaurar Base de Datos</h1>
@stop

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title"><i class="fas fa-database"></i> Opciones de Restauración de Base de Datos</h3>
        </div>
        <div class="card-body">
            <form action="{{ url('/restore-from-backup') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="backup_file" class="font-weight-bold">Subir archivo de respaldo (.sql):</label>
                    
                    <!-- Alerta para instruir al usuario -->
                    <div class="alert alert-info alert-dismissible fade show mt-2" role="alert">
                        <i class="fas fa-info-circle"></i> Solo se permiten archivos con la extensión <strong>.sql</strong>. Asegúrese de que el archivo de respaldo esté actualizado.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <!-- Campo de carga de archivo con estilo adicional -->
                    <div class="custom-file mt-3">
                        <input type="file" name="backup_file" id="backup_file" class="custom-file-input" required>
                        <label class="custom-file-label" for="backup_file">Seleccionar archivo...</label>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="fas fa-play-circle"></i> Iniciar Restauración de Base de Datos
                    </button>
                </div>
            </form>

            <!-- Mensajes de respuesta -->
            @if(session('status'))
                <div class="alert alert-info alert-dismissible fade show mt-4" role="alert">
                    <i class="fas fa-info-circle"></i> {{ session('status') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('js')
    <!-- Script para mostrar el nombre del archivo seleccionado -->
    <script>
        document.getElementById('backup_file').addEventListener('change', function(event) {
            var fileName = event.target.files[0].name;
            var nextSibling = event.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    </script>

    <!-- SweetAlert para mostrar mensajes de éxito, error e información -->
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @elseif(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @elseif(session('status'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Información',
                text: '{{ session('status') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
@stop
