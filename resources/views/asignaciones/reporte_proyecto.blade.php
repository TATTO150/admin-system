<!DOCTYPE html>
<html>
<head>
    <title>Reporte del Proyecto {{ $proyectoNombre }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            position: relative;
        }
        .header {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 50px;
        }
        .logo {
            width: 150px;
            position: absolute;
            right: 0;
            top: 5;
        }
        .empresa {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            flex-grow: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            word-wrap: break-word;
        }
        th {
            background-color: #000;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="empresa">
            <br><br> <!-- Ajuste de salto de línea -->
            Constructora Traterra S. de R.L <br>
            Reporte del Proyecto: {{ $proyectoNombre }}<br>
            <div class="total">Total de Asignaciones: {{ count($asignaciones) }}</div>
        </div>
        <img src="{{ $logoBase64 }}" class="logo">
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Proyecto</th> <!-- Columna para el nombre del proyecto -->
                <th>Equipo</th>
                <th>Empleado</th>
                <th>Descripción</th>
                <th>Tipo de Asignación</th>
                <th>Estado Asignación</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($asignaciones as $index => $asignacion)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $proyectoNombre }}</td> <!-- Mostrar el nombre del proyecto en cada fila -->
                    <td>{{ $asignacion->NOM_EQUIPO }}</td>
                    <td>{{ $asignacion->NOM_EMPLEADO }}</td>
                    <td>{{ $asignacion->DESCRIPCION }}</td>
                    <td>{{ $asignacion->TIPO_ASIGNACION_NOMBRE }}</td>
                    <td>{{ $asignacion->ESTADO_ASIGNACION }}</td>
                    <td>{{ $asignacion->FECHA_ASIGNACION_INICIO }}</td>
                    <td>{{ $asignacion->FECHA_ASIGNACION_FIN }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
