</html>
<!doctype html>
<html lang="en">
<head>
    <title>Reporte de Compras de Deducciones</title>
    <!-- Required meta tags -->
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
            position: relative;
        }
        h1, h2 {
            margin-bottom: 10px;
            text-align: center; /* Centrar los títulos */
        }
        h1{
            font-size: 30px;
        }
        h2{
            font-size: 22px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: end;
            text-align: center;
            width: 100%;
            margin-top: 20px; /* Espacio desde el top del body */
        }
        .content {
           /* margin: 10px; /* Espacio alrededor del contenido */
            margin-top: 40px; /* Ajustado para el espacio del header */
        }
        .logo {
            position: absolute;
            top: 0;
            right: 10px;
            width: 120px;
            height: 120px;
        }

        .logo img {
            width: 150px;
            height: 150px;
        }
        .fecha-hora {
            text-align: center;
            font-size: 12px;
            margin-bottom: 10px;
        }
        table {
            width: 90%;
            margin-left: auto;
            margin-right: auto;
            border-collapse: collapse;
            font-size: 10px;
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
        .pie-pagina {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid black;
            padding-top: 5px;
        }
        .fecha-hora-pie {
            position: fixed;
            bottom: 10px;
            left: 10px;
            font-size: 10px;
        }
        .page-number {
            position: fixed;
            bottom: 10px;
            right: 10px;
            font-size: 10px;
        }
        .footer {
            width: 100%;
            position: fixed;
            bottom: 10px; /* Ajuste para la paginación en la parte inferior */
            font-size: 10px;
            display: flex;
            justify-content: space-between;
            padding: 0 30px;
        }
        @page {
            size: A4;
            margin: 10mm;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">
            <img src="{{ $logoBase64 }}" alt="Logo">
        </div>
    </div>

    <h1>Constructora Traterra S. de R.L</h1>
    <h2>Reporte de Deducciones</h2>
    

 <div class="content">
    <table id="table" class="table table-bordered">
        <thead class="cabecera">
            <tr>
                <th>NUM#</th> 
                <th>Descripción</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($deducciones) || is_object($deducciones))
            @foreach ($deducciones as $index => $deduccion)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $deduccion->DESC_DEDUCCION }}</td>
                <td>{{ number_format($deduccion->VALOR_DEDUCCION, 2) }}</td>
               
            </tr>
            @endforeach
            @else
                <tr>
                    <td colspan="4">No se encontraron empleados.</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: right;"><strong>Total Deducciones:</strong></td>
                <td colspan="2"><strong>{{ number_format($totalDeducciones, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
    
    <div style="page-break-after: always;"></div>
    <div class="header">
        <div class="logo">
        <img src="{{ $logoBase64 }}" alt="Logo" style="width: 100px;">
        </div>
    </div>
    <h1 class="text-center">Constructora Traterra S. de R.L</h1>
    <h2 class="text-center">Reporte de Deducciones</h2>

    <h1>Detalles de la Compra</h1>
    <table>
        <tr>
            <th>Usuario que realizo la solicitud:</th>
            <td>{{ $usuario[$compra['Id_usuario']]->Nombre_Usuario ?? 'N/A' }}</td>
                        
        </tr>
        <tr>
            <th>Descripcion de Deduccion:</th>
            <td>{{ $deduccion->DESC_DEDUCCION }}</td>
        </tr>
        <tr>
            <th>Descripción de la compra:</th>
            <td>{{ $compra->DESC_COMPRA }}</td>
        </tr>
        
        <tr>
            <th>Estado dela solicitud:</th>
            <td>{{ $estadocompras[$compra['COD_ESTADO']]->DESC_ESTADO ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Tipo de pago realizado:</th>
            <td>{{ $tipocompras[$compra['COD_TIPO']]->DESC_TIPO ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Fecha de Registro:</th>
            <td>{{ $compra->FEC_REGISTRO }}</td>
        </tr>
        <tr>
            <th>Fecha de Pago:</th>
            <td>{{ $compra->FECHA_PAGO }}</td>
        </tr>
        <tr>
            <th>Precio Inicial:</th>
            <td>{{ number_format($compra->PRECIO_COMPRA, 2) }}</td>
        </tr>
        <tr>
            <th>Precio Final:</th>
            <td>{{ number_format($pagoFinal, 2) }}</td>
        </tr>
    </table>
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
            $('#table').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });
    </script>
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
