<!doctype html>
<html lang="es">
<head>
    <title>REPORTE DEL TOTAL</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <style>
        @page {
            size: A4 landscape;
            margin: 20mm;
        }
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: Arial, sans-serif;
        }
        h1, h2 {
            font-size: 22px;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 8px;
            text-align: center;
            border: 1px solid black;
        }
        th {
            background-color: #343a40;
             color: white;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 20px;
        }
        .logo img {
            width: 150px;
        }
        .fecha-hora {
            font-size: 14px;
            text-align: left;
            margin-left: 20px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            right: 0;
            width: 100%;
            text-align: right;
            font-size: 12px;
            color: #555;
            padding-right: 10mm;
            padding-bottom: 10mm;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="fecha-hora">
            Reporte generado el: {{ $fechaHora }}
        </div>
        <div class="logo">
        <img src="{{ $logoBase64 }}" alt="Logo" style="width: 100px;">
        </div>
    </div>
    
    <h1>Constructora Traterra S. de R.L</h1>
    <h2>Reporte del Proyecto</h2>
    <table>
        <thead>
            <tr>
                <th>NUM#</th> <!-- Columna de numeración -->
                <th>NOMBRE PROYECTO</th>
                <th>FECHA INICIO</th>
                <th>FECHA FINAL</th>
                <th>DESCRIPCIÓN PROYECTO</th>
                <th>PRESUPUESTO INICIO</th>
                <th>ESTADO PROYECTO</th>
            </tr>
        </thead>
        <tbody>
            <tr>
               <td>{{ $loop->iteration }}</td> <!-- Numeración automática -->  
                <td>{{ $proyecto->NOM_PROYECTO }}</td>
                <td>{{ $proyecto->FEC_INICIO }}</td>
                <td>{{ $proyecto->FEC_FINAL }}</td>
                <td>{{ $proyecto->DESC_PROYECTO }}</td>
                <td>{{ $proyecto->PRESUPUESTO_INICIO }}</td>
                <td>{{ $proyecto->ESTADO_PROYECTO }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <script type="text/php">
            if ( isset($pdf) ) {
                $pdf->page_script(function ($pageNumber, $pageCount, $pdf, $fontMetrics) {
                    $font = $fontMetrics->get_font("Arial", "normal");
                    $size = 10;
                    $pageText = "Página " . $pageNumber . " de " . $pageCount;
                    $x = 560;
                    $y = 720;
                    $pdf->text($x, $pdf->get_height() - $y, $pageText, $font, $size);
                });
            }
        </script>
    </div>
</body>
</html>
