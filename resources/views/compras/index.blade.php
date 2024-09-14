@extends('adminlte::page')

@section('title', 'compras')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1>MODULO COMPRAS</h1>
@stop

@section('content')
    <div class="container">
        <!-- Mostrar errores -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <main class="mt-3">
            <a href="{{ route('compras.crear') }}" class="btn btn-primary mb-3">Registrar Comprar</a>
            <a href="{{route('compras.pdf')}}" class="btn btn-primary mb-3" target="_blank">PDF</a>
            <a href="{{ route('compras.reporteDiario') }}" class="btn btn-info mb-3" target="_blank">Reporte Diario</a>
            <a href="{{ route('compras.reporteMensual') }}" class="btn btn-warning mb-3" target="_blank">Reporte Mensual</a>
            <a href="{{ route('gastos.index') }}" class="btn btn-warning mb-3" target="_blank">GESTION GASTO</a>
            <table id="mitabla" class="table table-striped">
                <thead>
                    <tr>
                        <th>DESCRIPCION COMPRA</th>
                        <th>CODIGO PROYECTO</th>
                        <th>FECHA REGISTRO</th>
                        <th>TIPO COMPRA</th>
                        <th>PRECIO VALOR</th>
                        <th>ACCION</th>
                       
                    </tr>
                </thead>
                <tbody>
                    
                    @if(is_array($compras) || is_object($compras))
                        @foreach ($compras as $compra)
                            <tr>
                                <td>{{ $compra['DESC_COMPRA'] }}</td>
                                <td>{{ $proyectos[$compra['COD_PROYECTO']]->NOM_PROYECTO ?? 'N/A' }}</td>
                                <td>{{ $compra['FEC_REGISTRO'] }}</td>
                                <td>{{ $compra['TIP_COMPRA'] }}</td>
                                <td>{{ $compra['PRECIO_VALOR'] }}</td>
                                <td>
                
                               
                                    <a href="{{ route('compras.edit', $compra['COD_COMPRA']) }}" class="btn btn-info btn-sm">Editar</a>
                                    <form action="{{ route('compras.destroy', $compra['COD_COMPRA']) }}" method="POST" style="display: inline-block;" onsubmit="return confirmDeletion()">
                                        @csrf
                                        @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                              
                            <script>
                              function confirmDeletion() {
                              return confirm('¿Estás seguro de que deseas eliminar esta compra? Esta acción no se puede deshacer.');
                            }
                             </script>

                                </td>
                            </tr>
                            
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10">No se encontraron proyectos.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </main>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="//code.jquery.com/jquery-3.7.0.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        ;

        new DataTable('#mitabla', {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json',
            },
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copy', text: '<i class="bi bi-clipboard-check-fill"></i>', titleAttr: 'Copiar', className: 'btn btn-secondary' },
                { extend: 'excel', text: '<i class="bi bi-file-earmark-spreadsheet"></i>', titleAttr: 'Exportar a Excel', className: 'btn btn-success' },
                { extend: 'csv', text: '<i class="bi bi-filetype-csv"></i>', titleAttr: 'Exportar a csv', className: 'btn btn-success' },
                { extend: 'pdf', text: '<i class="bi bi-file-earmark-pdf"></i>', titleAttr: 'Exportar a PDF', className: 'btn btn-danger' },
                { extend: 'print', text: '<i class="bi bi-printer"></i>', titleAttr: 'Imprimir', className: 'btn btn-info' },
            ],
        });
        $('#generarGastos').on('click', function() {
                $.ajax({
                    url: "{{ route('compras.generarGastos') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Éxito', response.message, 'success');
                    },
                    error: function(response) {
                        Swal.fire('Error', 'Hubo un error al generar los gastos', 'error');
                    }
                });
            });
        

        function confirmDeletion() {
            return confirm('¿Estás seguro de que deseas eliminar esta compra? Esta acción no se puede deshacer.');
        } 
    </script>
@stop
