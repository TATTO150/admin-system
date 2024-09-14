<!-- resources/views/usuarios/depurar.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Datos Enviados a la API</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Campo</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $campo => $valor)
                    <tr>
                        <td>{{ $campo }}</td>
                        <td>{{ is_string($valor) ? $valor : json_encode($valor) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2>Respuesta de la API</h2>
        <pre>{{ $response->body() }}</pre>
    </div>
@endsection
