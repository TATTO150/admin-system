<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Mantenimiento</title>
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
            font-size: 12px;
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
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>
        <div class="fecha-hora">
            Reporte generado el: {{ $fechaHora }}
        </div>
        <div class="logo">
        <img src="{{ $logoBase64 }}" alt="Logo" style="width: 100px;">
        </div>
    </div>

    <h1 class="text-center">Constructora Traterra S. de R.L</h1>
    <h2 class="text-center">Reporte de Mantenimiento</h2>
    <table id="mitabla" class="table table-striped">
        <thead class="cabecera">
            <tr>
                <th>NUM#</th> <!-- Columna de numeración -->
                <th>EMPLEADO SOLICITANTE</th>
                <th>ESTADO MANTENIMIENTO</th>
                <th>EQUIPO</th>
                <th>DESCRIPCION MANTENIMIENTO</th>
                <th>FECHA INGRESO</th>
                <th>FECHA FINAL PLANIFICADA</th>
                <th>FECHA FINAL REAL</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($mantenimientos) || is_object($mantenimientos))
                @foreach ($mantenimientos as $mantenimiento) 
                <tr>
                   <td>{{ $loop->iteration }}</td> <!-- Numeración automática -->
                    <td>{{ $mantenimiento['empleado']->NOM_EMPLEADO ?? 'No asignado' }}</td>
                    <td>{{ $mantenimiento['estado']->ESTADO ?? 'Estado no asignado' }}</td>
                    <td>{{ $mantenimiento['equipo']->NOM_EQUIPO ?? 'No asignado' }}</td>
                    <td>{{ $mantenimiento['DESC_MANTENIMIENTO'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($mantenimiento['FEC_INGRESO'])->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($mantenimiento['FEC_FINAL_PLANIFICADA'])->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($mantenimiento['FEC_FINAL_REAL'])->format('d-m-Y') }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7">No se encontraron mantenimientos.</td>
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
