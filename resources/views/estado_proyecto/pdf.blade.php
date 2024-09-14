<!doctype html>
<html lang="en">
<head>
    <title>Reporte de Estados de Proyecto</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.3.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <style>
    @page {
        size: A4; /* Tamaño de página A4 */
        margin: 20mm; /* Márgenes de la página */
    }
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 100%;
        padding: 10px;
        box-sizing: border-box;
    }
    .header {
        display: flex;
        justify-content: flex-end;
        align-items: flex-start; /* Alinea el logo al inicio (arriba) */
        margin-bottom: 20px;
        position: relative;
    }
    .logo {
        position: absolute;
        top: -10mm; /* Ajusta la distancia desde el borde superior */
        right: -15mm; /* Ajusta la distancia desde el borde derecho */
    }
    .logo img {
        width: 145px; /* Ajusta el tamaño del logo según tus necesidades */
    }
    h1 {
        font-size: 24px;
        text-align: center;
        margin: 10px 0;
    }
    h2 {
        font-size: 18px;
        text-align: center;
        margin: 10px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 40px; /* Ajusta este valor para mover la tabla más abajo */
        margin-bottom: 15px;
    }
    th, td {
        padding: 8px;
        text-align: left;
        border: 1px solid black;
        font-size: 12px;
    }
    th {
        background-color: #343a40;
        color: white;
        text-align: center; /* Alinea el texto en el centro */
    }
    .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        text-align: right;
        font-size: 10px;
        color: #555;
        padding-right: 10mm;
        padding-bottom: 10mm;
    }
    .no-data {
        text-align: center;
        font-weight: bold;
        font-size: 14px;
        margin-top: 15px;
    }
</style>

</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ $logoBase64 }}" alt="Logo">
            </div>
        </div>
        <h1>Constructora Traterra S. de R.L</h1>
        <h2>Reporte de estados de proyecto</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Estado Proyecto</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($estados as $index => $estado)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $estado->ESTADO_PROYECTO }}</td>
                    </tr>
                @endforeach
                @if ($estados->isEmpty())
                    <tr>
                        <td colspan="2" class="no-data">No se encontraron estados de proyecto.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer">
        <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script(function ($pageNumber, $pageCount, $pdf, $fontMetrics) {
                // Configura la fuente
                $font = $fontMetrics->get_font("Arial", "normal");
                $size = 8;

                // Calcula la posición de la página
                $pageText = "Página " . $pageNumber . " de " . $pageCount;
                $x = 540; // Ajusta la posición horizontal si es necesario
                $y = 15;  // Ajusta la posición vertical si es necesario
                $pdf->text($x, $pdf->get_height() - $y, $pageText, $font, $size);

                // Configura la fecha y hora
                $fechaHora = "Reporte generado el: " . date('d/m/Y H:i:s');
                $pdf->text(10, $pdf->get_height() - 25, $fechaHora, $font, $size);
            });
        }
        </script>
    </div>
</body>
</html>
