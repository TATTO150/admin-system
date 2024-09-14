@extends('adminlte::page')

@section('title', 'Editar Estado del Proyecto')

@section('content_header')
    <h1>Editar Estado del Proyecto</h1>
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

        <form action="{{ route('estado_proyecto.update', $estadoProyecto->COD_ESTADO_PROYECTO) }}" method="POST">
            @csrf
            @method('POST')

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Formulario de Edición</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="estado_proyecto">Estado del Proyecto:</label>
                        <input type="text" name="ESTADO_PROYECTO" id="estado_proyecto" class="form-control" value="{{ old('ESTADO_PROYECTO', $estadoProyecto->ESTADO_PROYECTO) }}" maxlength="25" required>
                        <small id="charCount" class="text-muted">0/25 caracteres usados</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('estado_proyecto.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </div>

    <script>
        const input = document.getElementById('estado_proyecto');
        const charCount = document.getElementById('charCount');

        input.addEventListener('input', function () {
            const length = input.value.length;
            charCount.textContent = `${length}/25 caracteres usados`;

            if (length >= 25) {
                charCount.classList.add('text-danger');
                input.value = input.value.substring(0, 25); // Trunca el texto a 25 caracteres
            } else {
                charCount.classList.remove('text-danger');
            }
        });
    </script>
@stop
