@extends('adminlte::page')

@section('title', 'Ubicacion')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1>MODULO UBICACIONES</h1>
@stop

@section('content')
    <div class="container">
        <main class="mt-3">
            <a href="{{ route('ubicaciones.crear') }}" class="btn btn-primary mb-3">CREAR</a>
            <a href="{{route('ubicaciones.pdf')}}" class="btn btn-primary mb-3" target="_blank">PDF</a>
            <table id="mitabla" class="table table-striped">
                <thead>
                    <tr>
                        <th>NOMBRE UBICACION</th>
                        <th>DESCRIPCION</th>
                        <th>ACCION</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @if(is_array($ubicaciones) || is_object($ubicaciones))
                        @foreach ($ubicaciones as $ubicacion)
                            <tr>
                                <td>{{ $ubicacion['NOM_UBICACION'] }}</td>
                                <td>{{ $ubicacion['DESCRIPCION'] }}</td>
                                <td>
                              
                                    <a href="{{ route('ubicaciones.edit', $ubicacion['COD_UBICACION']) }}" class="btn btn-info btn-sm">EDITAR</a>
                                    <form action="{{ route('ubicaciones.destroy', $ubicacion['COD_UBICACION']) }}" method="POST" style="display: inline-block;" onsubmit="return confirmDeletion()">
                                        @csrf
                                        @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">ELIMINAR</button>
                                    </form>
                            
                            <script>
                              function confirmDeletion() {
                              return confirm('¿Estás seguro de que deseas eliminar esta ubicacion? Esta acción no se puede deshacer.');
                            }
                             </script>

                                </td>
                            </tr>
                            
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10">No se encontraron Ubicaciones.</td>
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
    </script>
@stop
