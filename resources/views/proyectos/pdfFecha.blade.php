<!DOCTYPE html>
<html lang="es">
<head>
    <title>Reporte de Proyectos por Fecha</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20mm;
        }
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: sans-serif;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .logo {
            position: absolute;
            top: -10mm;
            right: 0;
            margin-right: 10mm;
        }
        h1, h2 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            table-layout: fixed;
            word-wrap: break-word;
            margin: 0 auto; /* Centra la tabla */
        }
        th, td {
            padding: 8px;
            text-align: center; /* Centra el contenido de las celdas */
            border: 1px solid black;
        }
        th {
            background-color: #343a40;
            color: white;
        }
        .info {
            margin-bottom: 20px;
        }
        .footer {
            position: fixed;
            bottom: 10mm;
            right: 10mm;
            font-size: 12px;
            color: #555;
            text-align: right;
        }
        .fecha-hora {
            position: fixed;
            bottom: 10mm;
            left: 10mm;
            font-size: 12px;
            color: #555;
            text-align: left;
        }

        /* Ajusta el ancho de la columna NUM# */
        .num-col {
            width: 5%;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="info">
            <h1>Constructora Traterra S. de R.L</h1>
        </div>
        <img src="{{ $logoBase64 }}" alt="Logo" width="150px" height="150px" class="logo">
    </div>

    <h2>Reporte de Proyectos por Fecha</h2>
    
    <table class="table">
        <thead>
            <tr>
                <th class="num-col">NUM#</th>
                <th>Nombre del Proyecto</th>
                <th>Fecha Inicio</th>
                <th>Fecha Final</th>
                <th>Descripción del Proyecto</th>
                <th>Presupuesto Inicial</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($proyectos) || is_object($proyectos))
                @foreach ($proyectos as $proyecto)
                    <tr>
                        <td class="num-col">{{ $loop->iteration }}</td>
                        <td>{{ $proyecto['NOM_PROYECTO'] }}</td>
                        <td>{{ $proyecto['FEC_INICIO'] }}</td>
                        <td>{{ $proyecto['FEC_FINAL'] }}</td>
                        <td>{{ $proyecto['DESC_PROYECTO'] }}</td>
                        <td>{{ $proyecto['PRESUPUESTO_INICIO'] }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6">No se encontraron proyectos.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <script type="text/php">
            if ( isset($pdf) ) {
                $pdf->page_script(function ($pageNumber, $pageCount, $pdf, $fontMetrics) {
                    $font = $fontMetrics->get_font(null, "normal");
                    $size = 8;
                    $pageText = "Página " . $pageNumber . " de " . $pageCount;
                    $x = 700;
                    $y = 22;
                    $pdf->text($x, $pdf->get_height() - $y, $pageText, $font, $size);

                    $fechaHora = "Reporte generado el: " . date('d/m/Y H:i:s');
                    $pdf->text(10, $pdf->get_height() - 25, $fechaHora, $font, $size);
                });
            }
        </script>
    </div>

</body>
</html>
