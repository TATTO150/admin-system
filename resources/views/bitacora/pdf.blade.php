<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de bitacora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
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
            top: 50%;
            right: 0;
            transform: translateY(-322%);
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
            margin-top: 90px; /* Incrementar el margen superior para bajar la tabla */
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            font-size: 10px;
        }
        .cabecera {
            background-color: #343a40;
            color: white;
        }
        .table, .table th, .table td {
            border: 1px solid black;
        }
        .table th, .table td {
            padding: 4px;
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
            <h3>Reporte de Bitácora</h3>
            <p>Constructora Traterra S. de R.L</p>
        </div>
        <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
    </div>
    
    <div class="content">
        <table id="mitabla" class="table table-striped">
            <thead class="cabecera">
                <tr>
                    <th>ID USUARIO</th>
                    <th>ID OBJETOS</th>
                    <th>DESCRIPCIÓN</th>
                    <th>FECHA</th>
                </tr>
            </thead>
            <tbody>
                @if(is_array($bitacoras) || is_object($bitacoras))
                    @foreach ($bitacoras as $bitacora)
                        <tr>
                            <td>{{ $bitacora->user->Nombre_Usuario ?? 'N/A' }}</td>
                            <td>{{ $bitacora->Objeto->Objeto ?? 'Sin Objeto' }}</td>
                            <td>{{ $bitacora['Descripcion'] }}</td>
                            <td>{{ $bitacora['Fecha'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">No se encontraron bitácoras.</td>
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
                    $size = 8; /* Reducir el tamaño de la fuente */
                    $pageText = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT;
                    $pdf->text(520, 820, $pageText, $font, $size);
                    $pdf->text(30, 820, "{{ $fechaHora }}", $font, $size); /* Colocar la fecha en el lado izquierdo */
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
</body>
</html>
