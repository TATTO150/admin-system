<!doctype html>
<html lang="en">
<head>
    <title>REPORTE GENERAL DE PROYECTO</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.3.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

    <style>
        @page {
            size: A4 landscape; /* Cambia la orientación a horizontal */
            margin: 10mm; /* Reducir los márgenes para maximizar el uso del espacio */
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 10px; /* Ajusta el padding para un diseño más compacto */
            box-sizing: border-box;
            page-break-inside: avoid; /* Evita saltos de página dentro de elementos */
        }
        h1, h2 {
            font-size: 18px; /* Reduce el tamaño de los encabezados */
            margin: 5px 0; /* Ajusta el margen para un diseño más compacto */
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px; /* Ajusta el margen inferior para un diseño más compacto */
        }
        th, td {
            padding: 6px; /* Reduce el padding de las celdas */
            text-align: center;
            border: 1px solid black;
            word-wrap: break-word;
            font-size: 12px; /* Reduce el tamaño de fuente para un diseño más compacto */
        }
        th {
            background-color: #343a40;
            color: white;
        }
        .header {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 15px; /* Ajusta el margen inferior para un diseño más compacto */
            position: relative;
            height: 80px; /* Ajusta la altura del encabezado */
        }
        .logo {
            width: 150px; /* Ajusta el tamaño del logo */
            position: absolute;
            top: 0;
            right: 0;
        }
        .fecha-hora {
            font-size: 12px; /* Reduce el tamaño de la fuente */
            text-align: right;
            margin-right: 100px; /* Ajusta el margen derecho según el tamaño del logo */
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: right;
            font-size: 10px; /* Reduce el tamaño de la fuente */
            color: #555;
            padding-right: 5mm;
            padding-bottom: 5mm;
        }
        .nombre-proyecto, .descripcion-proyecto {
            max-width: 150px; /* Reduce el ancho máximo para las columnas */
            word-break: break-word;
        }
        .no-data {
            text-align: center;
            font-weight: bold;
            font-size: 14px; /* Reduce el tamaño de la fuente */
            margin-top: 15px; /* Ajusta el margen superior para un diseño más compacto */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ $logoBase64 }}" alt="Logo" style="width: 100%;">
            </div>
        </div>
        
        <h1>Constructora Traterra S. de R.L</h1>
        <h2>Reporte General de Proyecto</h2>
        <table id="mitabla" class="table table-striped">
            <thead>
                <tr>
                    <th>NUM#</th>
                    <th class="nombre-proyecto">NOMBRE PROYECTO</th>
                    <th>FECHA INICIO</th>
                    <th>FECHA FINAL</th>
                    <th class="descripcion-proyecto">DESCRIPCIÓN PROYECTO</th>
                    <th>PRESUPUESTO INICIO</th>
                    <th>ESTADO PROYECTO</th>
                </tr>
            </thead>
            <tbody>
                @if(is_array($proyectos) || is_object($proyectos))
                    @foreach ($proyectos as $proyecto)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="nombre-proyecto">{{ $proyecto['NOM_PROYECTO'] }}</td>
                            <td>{{ $proyecto['FEC_INICIO'] }}</td>
                            <td>{{ $proyecto['FEC_FINAL'] }}</td>
                            <td class="descripcion-proyecto">{{ $proyecto['DESC_PROYECTO'] }}</td>
                            <td>{{ $proyecto['PRESUPUESTO_INICIO'] }}</td>
                            <td>{{ $proyecto['ESTADO_PROYECTO'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="no-data">No se encontraron proyectos.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer">
        <script type="text/php">
            if ( isset($pdf) ) {
                $pdf->page_script(function ($pageNumber, $pageCount, $pdf, $fontMetrics) {
                    $font = $fontMetrics->get_font("Arial", "normal");
                    $size = 8; // Reduce el tamaño de la fuente
                    $pageText = "Página " . $pageNumber . " de " . $pageCount;
                    $x = 700;
                    $y = 22;
                    $pdf->text($x, $pdf->get_height() - $y, $pageText, $font, $size);

                    // Agrega la fecha y hora en el pie de página
                    $fechaHora = "Reporte generado el: " . date('d/m/Y H:i:s');
                    $pdf->text(10, $pdf->get_height() - 25, $fechaHora, $font, $size); // Ajusta la posición si es necesario
                });
            }
        </script>
    </div>
</body>
</html>
