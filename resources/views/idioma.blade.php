@extends('adminlte::page')

@section('title', 'Cambiar Idioma')

@section('content_header')
    <h1>Cambiar Idioma</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('cambiar-idioma') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="lang">Selecciona tu idioma:</label>
                    <select name="lang" id="lang" class="form-control">
                        <option value="es" {{ App::getLocale() == 'es' ? 'selected' : '' }}>Español</option>
                        <option value="en" {{ App::getLocale() == 'en' ? 'selected' : '' }}>English</option>
                        <!-- Agrega más opciones de idioma aquí si es necesario -->
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Guardar</button>
            </form>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card {
            border-radius: 10px;
        }
    </style>
@stop
