<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Planillas</title>
    <style>
        /* Agrega tus estilos aquí */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid black;
        }
        th {
            background-color: #343a40;
            color: white;
        }
        .card {
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
<img src="{{ $logoBase64 }}" alt="Logo" style="width: 100px;">

    <h1>Reporte de Planilla</h1>
    <p>Fecha: {{ $fechaHora }}</p>

    <div class="card">
        <h5>Planilla #{{ $planilla->COD_PLANILLA }}</h5>
        <p><strong>Proyecto:</strong> {{ $proyecto->NOM_PROYECTO ?? 'No disponible' }}</p>
        <p><strong>Estado Actual:</strong> {{ $planilla->ESTADO_PLANILLA }}</p>
        <p><strong>Periodo de pago:</strong> {{ $planilla->PERIODO_PAGO }}</p>
        <p><strong>Fecha de Pago:</strong> {{ \Carbon\Carbon::parse($planilla->FECHA_PAGO)->format('Y/m/d') }}</p>
        <p><strong>Total a Pagar:</strong> {{ $planilla->TOTAL_PAGAR }}</p>
    </div>

    <h3>Empleados en esta Planilla:</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Nombre del Empleado</th>
                <th>Cargo</th>
                <th>Salario Base</th>
                <th>Deducciones</th>
                <th>Salario Neto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($empleados as $empleado)
                <tr>
                    <td>{{ $empleado->NOM_EMPLEADO }}</td>
                    <td>{{ $empleado->cargo->NOM_CARGO ?? 'Cargo no disponible' }}</td>
                    <td>{{ $empleado->SALARIO_BASE }}</td>
                    <td>{{ $empleado->DEDUCCIONES }}</td>
                    <td>{{ $empleado->SALARIO_NETO }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

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
                ');
            }
        </script>
    </div>
</body>
</html>
