<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte por Estado</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid black;
        }
        th {
            background-color: #343a40;
            color: white;
        }
        .header, .info {
            margin-bottom: 20px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #555;
        }
        img {
            width: 100px;
            margin: 20px 0;
        }
        .pie-pagina {
             position: fixed;
             bottom: 10px;
             width: 100%;
             text-align: center;
             font-size: 10px;
             border-top: 1px solid black;
             padding-top: 5px;
         }
         .page-number {
             position: fixed;
             bottom: 20px;
             right: 10px;
             font-size: 10px;
         }
         @page {
             size: A4;
             margin: 10mm;
         }        
    </style>
</head>
<body>

<img src="{{ $logoBase64 }}" alt="Logo" style="width: 100px;">
<h1 class="text-center">Constructora Traterra S. de R.L</h1>
    <div class="header">
        <h1>Reporte de Solicitudes por Estado</h1>
    </div>
    <div class="info">
        <p>Generado el: {{ $fechaHora }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>NUM#</th> <!-- Columna de numeración -->
                <th>Empleado</th>
                <th>Estado Solicitud</th>
                <th>Descripción Solicitud</th>
                <th>Presupuesto Solicitud</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($solicitudes as $solicitud)
                <tr>
                <td>{{ $loop->iteration }}</td> <!-- Numeración automática -->
                    <td>{{ $solicitud->empleado->NOM_EMPLEADO ?? 'N/A' }}</td>
                    <td>{{ $solicitud->ESTADO_SOLICITUD }}</td>
                    <td>{{ $solicitud->DESC_SOLICITUD }}</td>
                    <td>${{ number_format($solicitud->PRESUPUESTO_SOLICITUD, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No se encontraron solicitudes para el estado seleccionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">
        <script type="text/php">
            if ( isset($pdf) ) {
                $pdf->page_script('
                    $font = $fontMetrics->get_font("Arial", "normal");
                    $size = 10;
                    $pageText = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT;
                    $y = 560;
                    $x = 720;
                    $pdf->text($x, $y, $pageText, $font, $size);
                ');
            }
        </script>
    </div>
</body>
</html>