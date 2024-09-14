<!-- resources/views/empleado_proyecto/index.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Empleados y Proyectos</title>
</head>
<body>
    <h1>Lista de Empleados y Proyectos</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Proyecto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($empleadosProyectos as $empleadoProyecto)
                <tr>
                    <td>{{ $empleadoProyecto->id }}</td>
                    <td>{{ $empleadoProyecto->nombre }}</td>
                    <td>{{ $empleadoProyecto->proyecto }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
