<!doctype html>
<html lang="en">
<head>
    <title>REPORTE ESPECÍFICO DE PROYECTO</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- Bootstrap CSS v5.3.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
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
        h1, h2, h3 {
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
        .logo {
            width: 150px; /* Ajusta el tamaño del logo */
            position: absolute;
            top: 0;
            right: 0;
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
        .page-break {
            page-break-before: always;
        }
        td.descripcion-proyecto {
            word-wrap: break-word;
            max-width: 400px; /* Ajusta según sea necesario */
            white-space: pre-wrap; /* Permite saltos de línea dentro del texto */
            overflow: hidden;
        }
    </style>
    @php
        $descripcionConSaltos = wordwrap($proyecto['DESC_PROYECTO'], 100, "\n", true);
    @endphp
</head>

<body>
    <div class="header">
        <div class="logo">
            <img src="{{ $logoBase64 }}" alt="Logo" width="150px">
        </div>
    </div>
    
    <h1>Constructora Traterra S. de R.L</h1>
    <h2>Reporte de Proyecto</h2>

    <h3>Detalles del Proyecto</h3>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>NOMBRE PROYECTO</th>
                <td>{{ $proyecto['NOM_PROYECTO'] }}</td>
            </tr>
            <tr>
                <th>FECHA INICIO</th>
                <td>{{ $proyecto['FEC_INICIO'] }}</td>
            </tr>
            <tr>
                <th>FECHA FINAL</th>
                <td>{{ $proyecto['FEC_FINAL'] }}</td>
            </tr>
            <tr>
                <th>DESCRIPCIÓN PROYECTO</th>
                <td class="descripcion-proyecto">{{ $descripcionConSaltos }}</td>
            </tr>
            <tr>
                <th>PRESUPUESTO INICIO</th>
                <td>{{ $proyecto['PRESUPUESTO_INICIO'] }}</td>
            </tr>
            <tr>
                <th>ESTADO PROYECTO</th>
                <td>{{ $proyecto['ESTADO_PROYECTO'] }}</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div> <!-- Page break here -->

    <h3>Empleados Asignados al Proyecto</h3>
    <table id="empleados" class="table table-striped">
        <thead>
            <tr>
                <th>NOMBRE EMPLEADO</th>
                <th>DNI</th>
                <th>CARGO</th>
                <th>FECHA DE INGRESO</th>
                <th>SALARIO NETO</th>
                <th>ESTADO EMPLEADO</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($empleados) || is_object($empleados))
                @foreach ($empleados as $empleado)
                    <tr>
                        <td>{{ $empleado['NOM_EMPLEADO'] }}</td>
                        <td>{{ $empleado['DNI_EMPLEADO'] }}</td>
                        <td>{{ $empleado['COD_CARGO'] }}</td>
                        <td>{{ $empleado['FEC_INGRESO_EMPLEADO'] }}</td>
                        <td>{{ $empleado['SALARIO_NETO'] }}</td>
                        <td>{{ $empleado['ESTADO_EMPLEADO'] }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7">No se encontraron empleados asignados a este proyecto.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="page-break"></div> <!-- Page break here -->

    <h3>Equipos Asignados al Proyecto</h3>
    <table id="equipos" class="table table-striped">
        <thead>
            <tr>
                <th>NOMBRE DEL EQUIPO</th>
                <th>DESCRIPCIÓN DEL EQUIPO</th>
                <th>EMPLEADO ENCARGADO</th>
                <th>FECHA ASIGNACIÓN INICIO</th>
                <th>FECHA ASIGNACIÓN FIN</th>
                <th>ESTADO ASIGNACIÓN</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($equiposAsignados) || is_object($equiposAsignados))
                @foreach ($equiposAsignados as $asignacion)
                    <tr>
                        <td>{{ $asignacion->equipo->NOM_EQUIPO }}</td>
                        <td>{{ $asignacion->equipo->DESC_EQUIPO }}</td>
                        <td>{{ $asignacion->empleado->NOM_EMPLEADO }}</td>
                        <td>{{ $asignacion->FECHA_ASIGNACION_INICIO }}</td>
                        <td>{{ $asignacion->FECHA_ASIGNACION_FIN }}</td>
                        <td>{{ $asignacion->COD_ESTADO_ASIGNACION }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6">No se encontraron equipos asignados a este proyecto.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="page-break"></div> <!-- Page break here -->

    <h3>Gastos del Proyecto</h3>
    <table id="gastos" class="table table-striped">
        <thead>
            <tr>
                <th>DESCRIPCION GASTO</th>
                <th>TIPO COMPRA</th>
                <th>FECHA REGISTRO</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($gastos) || is_object($gastos))
                @foreach ($gastos as $gasto)
                    <tr>
                        <td>{{ $gasto->DESC_COMPRA }}</td>
                        <td>{{ $gasto->TIP_COMPRA }}</td>
                        <td>{{ $gasto->FEC_REGISTRO }}</td>
                        <td>{{ $gasto->PRECIO_VALOR }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4">No se encontraron gastos para este proyecto.</td>
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
