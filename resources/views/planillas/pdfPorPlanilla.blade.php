<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Planilla</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.3.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            position: relative;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 10px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 3px;
            text-align: center;
        }

        .cabecera {
            background-color: #343a40;
            color: white;
        }

        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }

        .report-details {
            text-align: center;
            flex-grow: 1;
            order: 1;
        }

        .report-details h2 {
            margin-top: 15px;
        }
        .report-details p {
            margin: 10px 0;
        }

        .content {
           /* margin: 10px; /* Espacio alrededor del contenido */
            margin-top: 40px; /* Ajustado para el espacio del header */
        }

        .logo {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 120px;
            height: 120px;
        }

        .logo img {
            width: 150px;
            height: 150px;
        }

        .footer {
            text-align: left;
            width: 100%;
            position: fixed;
            bottom: 20px;
        }

        @page {
            size: A4;
            margin: 10mm;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="report-details">
            <h2>Constructora Traterra S. de R.L</h2>
            <h2 style="margin: 0;">Reporte de Planilla #{{ $planilla->COD_PLANILLA }}</h2>
            <p><strong>Planilla del Mes:</strong> {{ \Carbon\Carbon::parse($planilla->FECHA_PAGO)->format('Y-m-d') }}</p>
            <p><strong>Total Pagado:</strong> {{ number_format($totalPagar, 2) }}</p>
        </div>
        <div class="logo">
            <img src="{{ $logoBase64 }}" alt="Logo">
        </div>
    </div>

    <div class="content">
        <div class="table-container">
            <table class="table">
                <thead class="cabecera">
                    <tr>
                        <th>NUM#</th>
                        <th>Nombre</th>
                        <th>Área</th>
                        <th>Cargo</th>
                        <th>Salario Base</th>
                        <th>Deducciones</th>
                        <th>Salario Neto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($empleados as $index => $empleado)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $empleado->NOM_EMPLEADO }}</td>
                            <td>{{ $empleado->area->NOM_AREA }}</td>
                            <td>{{ $empleado->cargo->NOM_CARGO }}</td>
                            <td>{{ number_format($empleado->SALARIO_BASE, 2) }}</td>
                            <td>{{ number_format($empleado->DEDUCCIONES, 2) }}</td>
                            <td>{{ number_format($empleado->SALARIO_NETO, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        <script type="text/php">
            if (isset($pdf)) {
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
