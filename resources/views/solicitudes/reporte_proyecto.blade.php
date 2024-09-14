<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte por Proyecto</title>
    <style>
        /* Estilos */
    </style>
</head>
<body>
    <img src="{{ $logoBase64 ?? '' }}" alt="Logo">
    <h1 class="text-center">Constructora Traterra S. de R.L</h1>
    <div class="header">
        <h1>Reporte de Solicitudes por Proyecto</h1>
    </div>
    <div class="info">
        <p>Generado el: {{ $fechaHora ?? 'Desconocida' }}</p>
    </div>
    @foreach ($proyectos as $proyecto)
        <h2>Proyecto: {{ $proyecto->NOM_PROYECTO ?? 'Desconocido' }}</h2>
        <table>
            <thead>
                <tr>
                    <th>NUM#</th>
                    <th>Empleado</th>
                    <th>Descripción Solicitud</th>
                    <th>Presupuesto Solicitud</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($proyecto->solicitudes as $solicitud)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $solicitud->empleado->NOM_EMPLEADO ?? 'N/A' }}</td>
                        <td>{{ $solicitud->DESC_SOLICITUD ?? 'Sin descripción' }}</td>
                        <td>${{ number_format($solicitud->PRESUPUESTO_SOLICITUD ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No se encontraron solicitudes para este proyecto.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
    <div class="footer">
        <script type="text/php">
            if ( isset($pdf) ) {
                $pdf->page_script('
                    $font = $fontMetrics->get_font("Arial", "normal");
                    $size = 10;
                    $pageText = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT;
                    $y = 560;
                    $x = 720;
                    $pdf->text($x, $y, $pageText, $font, $size);
                ');
            }
        </script>
    </div>
</body>
</html>
