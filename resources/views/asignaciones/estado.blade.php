<!DOCTYPE html>
<html>
<head>
    <title>Reporte por Estado</title>
    <style>
        @page {
            size: A4 landscape; /* Cambia la orientación a horizontal */
            margin: 20mm;
        }
        body {
         display: flex;
         flex-direction: column;
         align-items: center; /* Centra el contenido horizontalmente */
         text-align: center;
         margin: 0; /* Asegúrate de que no haya márgenes predeterminados */
         padding: 0;
         width: 100%; /* Ocupa todo el ancho de la página */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th, td {
            padding: 8px;
        text-align: left;
        border: 1px solid black;
        }
        th {
            background-color: #343a40 ;
            color: white; 
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .info {
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
    <div class="header">
        <img src="{{ $logoBase64 }}" alt="Logo" style="width: 100px;">
        <h1 class="text-center">Constructora Traterra S. de R.L</h1>
        <h1>Reporte por Estado: {{ $estado }}</h1>
        <p>{{ $fechaHora }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>NUM#</th> <!-- Columna de numeración -->
                <th>NOMBRE EQUIPO</th>
                <th>DESCRIPCIÓN</th>
                <th>ESTADO</th>
            </tr>
        </thead>
            <tbody>
                @foreach ($asignaciones as $asignacion)
                    <tr>
                        <td>{{ $loop->iteration }}</td> <!-- Numeración automática -->
                        <td>{{ $asignacion->equipo->NOM_EQUIPO }}</td>
                        <td>{{ $asignacion->DESCRIPCION }}</td>
                        <td>{{ $asignacion->estado_asignacion->ESTADO }}</td>
                     
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
                    $y = 560;  // Ajusta la posición y para estar en la parte inferior
                    $x = 720;  // Ajusta la posición x para estar en el borde derecho
                    $pdf->text($x, $y, $pageText, $font, $size);
                ');
            }
        </script>
    </div>
</body>
</html>
