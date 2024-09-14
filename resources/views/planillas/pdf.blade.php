<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Planilla</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: end;
            text-align: center;
            width: 100%;
            margin-top: 20px; /* Espacio desde el top del body */
        }
        .footer {
            text-align: center;
            width: 100%;
            position: fixed;
            bottom: 20px;
        }
        .content {
           /* margin: 10px; /* Espacio alrededor del contenido */
            margin-top: 40px; /* Ajustado para el espacio del header */
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
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
        .report-details {
            text-align: center;
            margin-top: 7px;
        }
        .report-details h2 {
            margin-top: 15px;
        }
        .report-details p {
            margin: 4px 0;
        }
        h1, h2{
            margin-top: 7px;
        }

        .contenedor-cabecera{
            display: flex;
            justify-content: space-between;
            align-items: end;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="contenedor-cabecera">
        <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
        <div class="report-details">
            <h1>Constructora Traterra S. de R.L</h1>
            <h2>Reporte de Planillas</h2>
            <h2>{{ $tipo_reporte }}</h2>
            
        </div>
    </div>
    
</div>

<div class="content">
    <table id="mitabla" class="table table-striped">
        <thead class="cabecera">
            <tr>
                <th># Planilla</th>
                <th>Fecha Generada</th>
                <th>Mes Pagado</th>
                <th>Total Pagado</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($planillas) || is_object($planillas))
                @foreach ($planillas as $planilla)
                    @php
                        $fecha_pago = \Carbon\Carbon::parse($planilla['FECHA_GENERADA']);
                        $mes = \Carbon\Carbon::parse($planilla['MES']);
                    @endphp
                    <tr>
                        <td>{{ $planilla['COD_PLANILLA'] }}</td>
                        <td>{{ $fecha_pago->format('Y/m/d') }}</td>
                        <td>{{ $mes->format('Y/m/d') }}</td>
                        <td>{{ $planilla['TOTAL_PAGADO'] }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3">No se encontraron planillas.</td>
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
                $y = 820;
                $x = 520;
                $pdf->text($x, $y, $pageText, $font, $size);
                $pdf->text(30, 820, "{{ $fechaHora }}", $font, $size);
            ');
        }
    </script>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQ+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
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

