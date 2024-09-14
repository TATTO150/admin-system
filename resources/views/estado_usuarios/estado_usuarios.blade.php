<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Estado de Usuarios</title>
    <style>
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; }
        .header { width: 100%; margin-top: 20px; position: relative; text-align: center; }
        .logo { position: absolute; top: 10px; right: 10px; width: 120px; height: 120px; }
        .report-details { margin-top: 10px; }
        .report-details h3 { margin-bottom: 0; font-size: 16px; }
        .report-details p { margin: 4px 0; font-size: 14px; }
        .content { margin: 10px; margin-top: 60px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        .cabecera { background-color: #343a40; color: white; }
        .table, .table th, .table td { border: 1px solid black; }
        .table th, .table td { padding: 4px; text-align: center; }
        .footer { width: 100%; position: fixed; bottom: 20px; font-size: 10px; display: flex; justify-content: space-between; padding: 0 30px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="report-details">
            <h3>Reporte de Estado de Usuarios</h3>
            <p>Constructora Traterra S. de R.L</p>
            <h3>Total de Estados: {{ $estado_usuarios->count() }}</h3>
        </div>
        <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
    </div>
    <div class="content">
        <table class="table table-striped">
            <thead class="cabecera">
                <tr>
                    <th>#</th>
                    <th>Estado</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($estado_usuarios as $estadoUsuario)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $estadoUsuario->ESTADO }}</td>
                        <td>{{ $estadoUsuario->DESCRIPCION }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="footer">
        <script type="text/php">
            if (isset($pdf)) {
                $pdf->page_text(500, 820, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0,0,0)); // Paginación más a la izquierda
                $pdf->page_text(5, 820, "{{ $fechaHora }}", null, 10, array(0,0,0));
            }
        </script>
    </div>
    
</body>
</html>
