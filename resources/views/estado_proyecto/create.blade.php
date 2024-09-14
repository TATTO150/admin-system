@extends('adminlte::page')

@section('title', 'Crear Estado del Proyecto')

@section('content_header')
    <h1>CREAR ESTADO PROYECTO</h1>
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

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('estado_proyecto.store') }}" method="POST">
            @csrf

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">CREAR ESTADO</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="estado_proyecto">ESTADO PROYECTO</label>
                        <input type="text" name="ESTADO_PROYECTO" id="estado_proyecto" class="form-control" value="{{ old('ESTADO_PROYECTO') }}" maxlength="25" required>
                        <small id="charLimit" class="text-danger" style="display: none;">Has alcanzado el límite de 25 caracteres.</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">CREAR</button>
                    <a href="{{ route('estado_proyecto.index') }}" class="btn btn-secondary">CANCELAR</a>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const estadoProyectoInput = document.getElementById('estado_proyecto');
            const charLimitWarning = document.getElementById('charLimit');

            estadoProyectoInput.addEventListener('input', function () {
                if (this.value.length >= 25) {
                    charLimitWarning.style.display = 'inline';
                    this.value = this.value.substring(0, 25); // Limita la entrada a 25 caracteres
                } else {
                    charLimitWarning.style.display = 'none';
                }
            });
        });
    </script>
@stop
