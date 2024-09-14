<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte de Estados de Empleados</title>
    
    <!-- Bootstrap CSS v5.3.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 5px;
            text-align: center;
        }
        .cabecera {
            background-color: #343a40;
            color: white;
        }
        .fecha-hora {
            text-align: left;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .logo {
            position: absolute;
            top: 0;
            right: 10px;
            width: 120px;
            height: 120px;
        }

        .logo img {
            width: 150px;
            height: 150px;
        }
        .content {
           /* margin: 10px; /* Espacio alrededor del contenido */
            margin-top: 50px; /* Ajustado para el espacio del header */
        }
        @page {
            size: A4;
            margin: 10mm;
        }
        .footer {
            position: fixed;
            bottom: 10px;
            right: 10px;
            width: 100%;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="{{ $logoBase64 }}" alt="Logo" width="150" height="150">
    </div>
    <div class="header">
       
    </div>
    
    <h1 class="text-center">Constructora Traterra S. de R.L</h1>
    <h2 class="text-center">Reporte de Estados de Empleados</h2>
    <h2 class="text-center">Reporte General</h2>

    <div class="content">
    <table id="mitabla" class="table table-striped">
        <thead class="cabecera">
            <tr>
                <th>Num#</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($estados) || is_object($estados))
                @foreach ($estados as $index => $estado)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $estado['ESTADO_EMPLEADO'] }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="2">No se encontraron estados.</td>
                </tr>
            @endif
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
                    $y = 820;  // Ajusta la posición y para estar en la parte inferior
                    $x = 520;  // Ajusta la posición x para estar en el borde derecho
                    $pdf->text($x, $y, $pageText, $font, $size);
                    $pdf->text(30, 820, "{{ $fechaHora }}", $font, $size);
                ');
            }
        </script>
    </div>
</body>
</html>
