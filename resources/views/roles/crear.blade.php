@extends('adminlte::page')

@section('title', 'Crear Rol')

@section('content_header')
    <h1 class="text-center">CREAR ROL</h1>
@stop

@section('content')
    <div class="container d-flex justify-content-center">
        <div class="card shadow-sm p-4 mb-5 bg-white rounded" style="width: 50%;">
            <main class="mt-3">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: '{{ session('success') }}',
                        });
                    </script>
                @endif

                @if (session('error'))
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: '{{ session('error') }}',
                        });
                    </script>
                @endif

                <form action="{{ route('roles.insertar') }}" method="POST" id="roleForm">
                    @csrf
                    <div class="mb-3">
                        <label for="Rol" class="form-label">Rol</label>
                        <input type="text" class="form-control" id="Rol" name="Rol" value="{{ old('Rol') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="Descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="Descripcion" name="Descripcion" required maxlength="255" oninput="updateCharCount()">{{ old('Descripcion') }}</textarea>
                        <div id="charCount">0 / 255 caracteres</div>
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-primary" id="saveBtn">GUARDAR</button>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">CANCELAR</a>
                    </div>
                </form>
            </main>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card {
            margin-top: 20px;
        }
        .form-label {
            font-weight: bold;
        }
    </style>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
         function updateCharCount() {
        const descripcion = document.getElementById('Descripcion');
        const charCount = document.getElementById('charCount');
        const maxLength = descripcion.getAttribute('maxlength');
        const currentLength = descripcion.value.length;

        charCount.textContent = `${currentLength} / ${maxLength} caracteres`;
    }

    // Inicializa el conteo de caracteres al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        updateCharCount();
    });
        $(document).ready(function() {
            $('#saveBtn').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#roleForm').submit();
                    }
                });
            });
        });
    </script>
@stop
