<!doctype html>
<html lang="es">
<head>
    <title>Reporte de Proyectos por Estado</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }
        h1, h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            table-layout: fixed;
            margin-top: 90px; /* Ajusta el margen superior de la tabla según lo necesario */
        }
        th, td {
            padding: 8px;
            text-align: center; /* Centra el texto en las celdas */
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
            position: relative;
        }
        .logo {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .logo img {
            width: 150px;
        }
        .footer {
            position: fixed;
            bottom: 10px;
            left: 10px;
            width: 100%;
            text-align: left;
            font-size: 10px;
        }
        td {
            word-wrap: break-word;
        }
        .nombre-proyecto, .descripcion-proyecto {
            width: 25%;
        }
        .numero {
            width: 5%; /* Ajusta este valor según el ancho deseado */
        }
        @page {
            size: A4 landscape; /* Cambia a formato horizontal */
            margin: 10mm;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">
            <img src="{{ $logoBase64 }}" alt="Logo">
        </div>
    </div>
    
    <h1>Constructora Traterra S. de R.L</h1>
    <h2>Reporte de Proyectos por Estado</h2>
    
    <table id="mitabla" class="table table-striped">
        <thead>
            <tr>
                <th class="numero">NUM#</th> <!-- Columna de numeración con ancho ajustado -->
                <th class="nombre-proyecto">NOMBRE PROYECTO</th>
                <th class="descripcion-proyecto">DESCRIPCIÓN PROYECTO</th>
                <th>PRESUPUESTO INICIO</th>
                <th>ESTADO PROYECTO</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($proyectos) || is_object($proyectos))
                @foreach ($proyectos as $proyecto)
                    <tr>
                        <td>{{ $loop->iteration }}</td> <!-- Numeración automática -->
                        <td class="nombre-proyecto">{{ $proyecto['NOM_PROYECTO'] }}</td>
                        <td class="descripcion-proyecto">{{ $proyecto['DESC_PROYECTO'] }}</td>
                        <td>{{ $proyecto['PRESUPUESTO_INICIO'] }}</td>
                        <td>{{ $proyecto['ESTADO_PROYECTO'] }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5">No se encontraron proyectos.</td>
                </tr>
            @endif
        </tbody>
    </table>

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
