<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Solicitudes</title>
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
            right: -20;
            top: 5;
        }

        .empresa {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            flex-grow: 1;
        }

        .titulo {
            font-size: 18px;
            margin-top: 10px;
        }

        .subtitulo {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
            text-transform: uppercase;
            word-wrap: break-word; /* Asegura que las palabras largas se ajusten */
            white-space: normal;  /* Permite que el texto fluya a la siguiente línea */
            text-align: center;   /* Centra el texto */
            max-width: 80%;       /* Establece un ancho máximo para el subtítulo */
            margin: 0 auto;       /* Centra el subtítulo horizontalmente */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 20px;
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
            text-transform: uppercase;
        }

        td {
            font-size: 10px;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            font-size: 10px;
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            border-top: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .footer .left {
            text-align: left;
        }

        .footer .right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="empresa">
            <br><br>
            Constructora Traterra S. de R.L <br>
            Reporte de Solicitudes <br>
            <div class="subtitulo">
                  {{ strtoupper($filterValue) }}
            </div>
            
        </div>
        <img src="{{ $logoBase64 }}" class="logo">
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Solicitante</th>
                <th>Descripción Solicitud</th>
                <th>Proyecto</th>
                <th>Estado</th>
                <th>Tipo</th>
                <th>Total Cuotas</th>
                <th>Precio Cuota</th>
                <th>Precio Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($solicitudes as $index => $solicitud)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $solicitud->usuario->Usuario ?? 'N/A' }}</td>
                    <td>{{ $solicitud->DESC_COMPRA }}</td>
                    <td>{{ $solicitud->proyecto->NOM_PROYECTO ?? 'N/A' }}</td>
                    <td>{{ $solicitud->estadocompras->DESC_ESTADO ?? 'N/A' }}</td>
                    <td>{{ $solicitud->tipocompras->DESC_TIPO ?? 'N/A' }}</td>
                    <td>{{ $solicitud->TOTAL_CUOTAS }}</td>
                    <td>{{ number_format($solicitud->PRECIO_CUOTA, 2) }}</td>
                    <td>{{ number_format($solicitud->PRECIO_COMPRA, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
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
