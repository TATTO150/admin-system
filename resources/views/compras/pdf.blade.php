<!doctype html>
<html lang="en">
<head>
    <title>Reporte de Compras</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.3.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

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
            font-size: 8px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 3px;
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
            text-align: right;
            font-size: 12px;
            margin-bottom: 10px;
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
<div class="header">
        <div class="fecha-hora">
            Reporte generado el: {{ $fechaHora }}
        </div>
        <div class="logo">
        <img src="{{ $logoBase64 }}" alt="Logo" style="width: 100px;">
        </div>
    </div>
    
    <h1 class="text-center">Constructora Traterra S. de R.L</h1>
    <h2 class="text-center">Reporte de Compras</h2>
    <table id="mitabla" class="table table-striped">
        <thead class="cabecera">
            <tr>
                <th>NUM#</th> <!-- Columna de numeración -->
                <th>DESCRIPCION COMPRA</th>
                <th>CODIGO PROYECTO</th>
                <th>FECHA REGISTRO</th>
                <th>TIPO COMPRA</th>
                <th>PRECIO VALOR</th>
                <th>CANTIDAD</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($compras) || is_object($compras))
                @foreach ($compras as $compra)
                    <tr>
                        <td>{{ $loop->iteration }}</td> <!-- Numeración automática -->
                        <td>{{ $compra['DESC_COMPRA'] }}</td>
                        <td>{{ $proyectos[$compra['COD_PROYECTO']]->NOM_PROYECTO ?? 'N/A' }}</td>
                        <td>{{ $compra['FEC_REGISTRO'] }}</td>
                        <td>{{ $compra['TIP_COMPRA'] }}</td>
                        <td>{{ $compra['PRECIO_VALOR'] }}</td>
                        <td>{{ $compra['CANTIDAD'] }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6">No se encontraron compras.</td>
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
                    $y = 560;  // Adjust y position to be at the bottom
                    $x = 720;  // Adjust x position to be at the right
                    $pdf->text($x, $y, $pageText, $font, $size);
                ');
            }
        </script>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    
    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#mitabla').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });
    </script>
</body>
</html>
