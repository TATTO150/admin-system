<!doctype html>
<html lang="en">
<head>
    <title>Reporte de Empleados</title>
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
            size: A4 landscape;
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
    <h2>Reporte de Empleados</h2>
    <h2>{{ $tipo_reporte }}</h2> <!-- Título centrado -->

 <div class="content">
    <table id="table" class="table table-bordered">
        <thead class="cabecera">
            <tr>
                <th>NUM#</th> 
                <th>USUARIO</th>
                <th>DESCRIPCION</th>
                <th>PROYECTO ASIGNADO</th>
                <th>FECHA REGISTRO</th>
                <th>TIPO</th>
                <th>FECHA DE PAGO</th>
                <th>PRECIO</th>
                <th>DEDUCCION</th>
                <th>PRECIO CUOTA</th>
                <th>PRECIO NETO</th>
                <th>ACUERDO DE CUOTAS</th>
                <th>CUOTAS PAGADAS</th>
                <th>LIQUIDEZ</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($compras) || is_object($compras))
                @foreach ($compras as $compra)
                    <tr>
                        <td>{{ $loop->iteration }}</td> <!-- Numeración automática -->
                        <td>{{ $usuarios[$compra['Id_usuario']]->Nombre_Usuario ?? 'N/A' }}</td>
                        <td>{{ $compra['DESC_COMPRA'] }}</td>
                        <td>{{ $proyectos[$compra['COD_PROYECTO']]->NOM_PROYECTO ?? 'N/A' }}</td>
                        <td>{{ $compra['FEC_REGISTRO'] }}</td>
                        <td>{{ $tipocompras[$compra['COD_TIPO']]->DESC_TIPO ?? 'N/A' }}</td>
                        <td>{{ $compra['FECHA_PAGO']}}</td>
                        <td>{{ number_format($compra['PRECIO_COMPRA'],2)}}</td>
                        <td>{{ number_format($compra->totalDeducciones, 2) }}</td>
                        <td>{{ $compra['PRECIO_CUOTA'] }}</td>
                        <td>{{ $compra['PRECIO_NETO'] }}</td>
                        <td>{{ $compra['TOTAL_CUOTAS'] }}</td>
                        <td>{{ $compra['CUOTAS_PAGADAS'] }}</td>
                        <td>{{ $compra['LIQUIDEZ_COMPRA'] == 1 ? 'Sí' : 'No' }}</td>
                        
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="13">No se encontraron empleados.</td>
                </tr>
            @endif
        </tbody>
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
