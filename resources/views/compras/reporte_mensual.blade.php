<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Mensual de Compras</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <img src="{{ $logoBase64 }}" alt="Logo" style="width: 100px;">
    <h1 class="text-center">Constructora Traterra S. de R.L</h1>
    <h1>Reporte Mensual de Compras</h1>
    <p>Desde: {{ $primerDiaMes->format('d-m-Y') }} Hasta: {{ $ultimoDiaMes->format('d-m-Y') }}</p>
    <h2>Total Gastado en el Mes: ${{ number_format($totalGastadoMes, 2) }}</h2>
    
    <table>
        <thead>
            <tr>
                <th>NUM#</th> <!-- Columna de numeración -->
                <th>Código de Compra</th>
                <th>Descripción</th>
                <th>Proyecto</th>
                <th>Fecha de Registro</th>
                <th>Total Gastado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($comprasMes as $compra)
                <tr>
                    <td>{{ $loop->iteration }}</td> <!-- Numeración automática -->
                    <td>{{ $compra->COD_COMPRA }}</td>
                    <td>{{ $compra->DESC_COMPRA }}</td>
                    <td>{{ $proyectos[$compra->COD_PROYECTO]->NOM_PROYECTO ?? 'Proyecto no encontrado' }}</td>
                    <td>{{ $compra->FEC_REGISTRO->format('d-m-Y') }}</td>
                    <td>${{ number_format($compra->gastos->sum('TOTAL'), 2) }}</td>
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
