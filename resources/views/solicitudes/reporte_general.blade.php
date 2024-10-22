<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Solicitud</title>
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
            <h3>Reporte General Solicitud</h3>
            <p>Constructora Traterra S. de R.L</p>
           
        </div>
        <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
    </div>
    
    <div class="content">
        <table class="table table-striped">
            <thead class="cabecera">
                <tr>
               <th>#</th> <!-- Columna de numeración -->
                <th>Solicitante</th>
                <th>Descripción Solicitud</th>
                <th>Proyecto</th>
                <th>Tipo</th>
                <th>Total Cuotas</th>
                <th>Precio Cuota</th>
                <th>Precio Total</th>
            </tr>
        </thead>
        <tbody>
                @if(is_array($solicitudes) || is_object($solicitudes))
                    @php $contador = 1; @endphp
                    @foreach ($solicitudes as $solicitud)
                <tr>
                    <td>{{ $contador++ }}</td>
                    <td>{{ $usuarios[$solicitud->Id_usuario]->Usuario ?? 'N/A' }}</td>
                    <td>{{ $solicitud->DESC_COMPRA }}</td>
                    <td>{{ $solicitud->proyecto->NOM_PROYECTO ?? 'N/A' }}</td>
                    <td>{{ $tipos[$solicitud->COD_TIPO]->DESC_TIPO ?? 'N/A' }}</td>
                    <td>{{ $estados[$solicitud->COD_ESTADO]->DESC_ESTADO ?? 'N/A' }}</td>
                    <td>{{ $solicitud->TOTAL_CUOTAS }}</td>
                    <td>{{ $solicitud->PRECIO_CUOTA }}</td>
                    <td>{{ $solicitud->PRECIO_COMPRA }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">No se encontraron registros de solicitudes.</td>
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
                    $pdf->text(1050, 820, $pageText, $font, $size); /* Mover la paginación más a la derecha */
                    $pdf->text(30, 820, "{{ $fechaHora }}", $font, $size); /* La fecha se mantiene en el lado izquierdo */
                ');
            }
        </script>
    </div>
    
</body>
</html>
