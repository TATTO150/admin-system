<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Compras - Búsqueda</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .header { width: 100%; margin-top: 20px; text-align: center; position: relative; }
        .logo { position: absolute; top: 10px; right: 10px; width: 120px; height: 120px; }
        .report-details { margin-top: 10px; }
        .report-details h3 { margin-bottom: 0; font-size: 16px; }
        .report-details p { margin: 4px 0; font-size: 14px; }
        .content { margin: 10px; margin-top: 60px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        .table th, .table td { padding: 5px; border: 1px solid black; text-align: center; }
        .footer { width: 100%; position: fixed; bottom: 20px; font-size: 10px; display: flex; justify-content: space-between; padding: 0 30px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="report-details">
            <h3>Constructora Traterra S. de R.L</h3>
            <h3>Reporte de Compras - Búsqueda: "{{ $terminoBusqueda }}"</h3>
            <h3>Total de Compras Encontradas: {{ $compras->count() }}</h3>
        </div>
        <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
    </div>

    <div class="content">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Descripción de Compra</th>
                    <th>Proyecto</th>
                    <th>Fecha de Registro</th>
                    <th>Estado</th>
                    <th>Tipo de Compra</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($compras as $compra)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $compra->DESC_COMPRA }}</td>
                        <td>{{ $compra->proyecto->NOM_PROYECTO ?? 'No disponible' }}</td>
                        <td>{{ \Carbon\Carbon::parse($compra->FEC_REGISTRO)->format('d-m-Y') }}</td>
                        <td>{{ $compra->estadoCompra->DESC_ESTADO ?? 'No disponible' }}</td>
                        <td>{{ $compra->tipoCompra->DESC_TIPO ?? 'No disponible' }}</td>
                        <td>{{ number_format($compra->VALOR_COMPRA, 2) }}</td>
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
                    $pdf->text(520, 820, $pageText, $font, $size);
                    $pdf->text(30, 820, "{{ $fechaHora }}", $font, $size);
                ');
            }
        </script>
    </div>
</body>
</html>
