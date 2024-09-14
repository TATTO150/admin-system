<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Solicitudes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            margin-top: 20px;
            position: relative;
            text-align: center; /* Centrar los títulos */
        }
        .logo {
            position: absolute;
            top: 50%; /* Mueve el logo al 50% del contenedor */
            right: 0;
            transform: translateY(-322%); /* Ajuste personalizado para alinearlo con el contenido de .report-details */
            width: 150px;
            height: 150px;
        }
        .report-details {
            margin-top: 10px;
        }
        .report-details h3 {
            margin-bottom: 0;
        }
        .report-details p {
            margin: 4px 0;
        }
        .content {
            margin: 10px;
            margin-top: 60px; /* Ajuste para dejar espacio al logo y al total */
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px; /* Espacio de tres renglones entre el total y la tabla */
        }
        .cabecera {
            background-color: #343a40;
            color: white;
        }
        .table, .table th, .table td {
            border: 1px solid black;
        }
        .table th, .table td {
            padding: 8px;
            text-align: center;
        }
        .footer {
            width: 100%;
            position: fixed;
            bottom: 20px;
            font-size: 10px;
            display: flex;
            justify-content: space-between;
            padding: 0 30px;
        }
    </style>
</head>
<body>
<div class="header">
        <div class="report-details">
            <h3>Reporte Solicitudes por Area</h3>
            <p>Constructora Traterra S. de R.L</p>
        </div>
        <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
    </div>
    
    <div class="content">
        <table class="table table-striped">
            <thead class="cabecera">
                <tr>
                <th>NUM#</th> <!-- Columna de numeración -->
                <th>CÓDIGO EMPLEADO</th>
                <th>DESCRIPCIÓN SOLICITUD</th>
                <th>CÓDIGO ÁREA</th>
                <th>CÓDIGO PROYECTO</th>
                <th>ESTADO SOLICITUD</th>
                <th>PRESUPUESTO SOLICITUD</th>
                <th>ACCIÓN</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($solicitudes) || is_object($solicitudes))
                @foreach ($solicitudes as $solicitud)
                    <tr>
                    <td>{{ $loop->iteration }}</td> <!-- Numeración automática -->
                        <td>{{ $empleados[$solicitud->COD_EMPLEADO]->NOM_EMPLEADO ?? 'N/A' }}</td>
                        <td>{{ $solicitud->DESC_SOLICITUD }}</td>
                        <td>{{ $solicitud->area->NOM_AREA ?? 'No asignado' }}</td>
                        <td>{{ $proyectos[$solicitud->COD_PROYECTO]->NOM_PROYECTO ?? 'N/A' }}</td>
                        <td>{{ $solicitud->ESTADO_SOLICITUD }}</td>
                        <td>{{ $solicitud->PRESUPUESTO_SOLICITUD }}</td>
                        <td></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7">No se encontraron solicitudes.</td>
                </tr>
            @endif
        </tbody>
    </table>

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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
