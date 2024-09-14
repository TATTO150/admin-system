@extends('adminlte::page')

@section('title', 'Gestión de Respaldos')

@section('content_header')
    <h1>Gestión de Respaldos</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <form action="{{ route('backups.create') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">CREAR RESPALDO</button>
            </form>
        </div>

        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre del Respaldo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($backups as $backup)
                        <tr>
                            <td>{{ basename($backup) }}</td>
                            <td>
                                <a href="{{ route('backups.download', basename($backup)) }}" class="btn btn-success">Descargar</a>
                                <form action="{{ route('backups.delete', basename($backup)) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Respaldos de base de datos cargados'); </script>
@stop
