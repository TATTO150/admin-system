<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Equipos por Estado</title>
    <style>
        /* Mismo estilo que en el reporte general */
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
            
            <h3>Constructora Traterra S. de R.L</h3>
            <h3>Reporte de Equipos por Estado: {{ $estadoNombre }}</h3>
            <h3>Total Equipos: {{ $equipos->count() }}</h3>
        </div>
        <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
    </div>
    <div class="content">
        <table class="table table-striped">
            <thead class="cabecera">
                <tr>
                    <th>#</th> 
                    <th>Nombre del Equipo</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Fecha de Compra</th>
                    <th>Valor Equipo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equipos as $equipo)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $equipo->NOM_EQUIPO }}</td>
                        <td>{{ $equipo->TipoEquipo ->TIPO_EQUIPO ?? 'No disponible' }}</td>
                        <td>{{ $equipo->DESC_EQUIPO }}</td>
                        <td>{{ $equipo->estadoEquipo->DESC_ESTADO_EQUIPO ?? 'No disponible' }}</td>
                        <td>{{ \Carbon\Carbon::parse($equipo->FECHA_COMPRA)->format('d-m-Y') }}</td>
                        <td>{{ $equipo->VALOR_EQUIPO }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="footer">
        <script type="text/php">
            if ( isset($pdf) ) {
                $pdf->page_script('
                    $font = $fontMetrics->get_font("Arial", "normal");
                    $size = 10;
                    $pageText = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT;
                    $pdf->text(520, 820, $pageText, $font, $size);
                    $pdf->text(30, 820, "{{ $fechaHora }}", $font, $size);
                ');
            }
        </script>
    </div>
</body>
</html>
