@extends('adminlte::page')

@section('title', 'empleado planilla')
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1>MODULO EMPLEADO PLANILLA</h1>
@stop

@section('content')
    <div class="container">
        <main class="mt-3">
            <a href="{{ route('empleado_planillas.crear') }}" class="btn btn-primary mb-3">CREAR EMPLEADO PLANILLA</a>
            <a href="{{route('empleado_planillas.pdf')}}" class="btn btn-primary mb-3" target="_blank">PDF</a>
            <table id="mitabla" class="table table-striped">
                <thead>
                    <tr>
                        <th>EMPLEADO</th>
                        <th>PLANILLA</th>
                        <th>SALARIO BASE</th>
                        <th>DEDUCCIONES</th>
                        <th>SALARIO NETO</th>
                        <th>ACCION</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @if(is_array($empleado_planillas) || is_object($empleado_planillas))
                        @foreach ($empleado_planillas as $empleado_planillas)
                            <tr>
                                <td>{{ $empleado_planillas->empleado?->NOM_EMPLEADO ?? 'No asignado' }}</td> 
                                <td>{{ $empleado_planillas->planilla?->TIP_PLANILLA ?? 'No asignado' }}</td> 
                                <td>{{ $empleado_planillas['SALARIO_BASE'] }}</td>
                                <td>{{ $empleado_planillas['DEDUCCIONES'] }}</td>
                                <td>{{ $empleado_planillas['SALARIO_NETO'] }}</td>
                                <td>
                
                               
                                    <a href="{{ route('empleado_planillas.edit', $empleado_planillas['COD_EMPLEADO_PLANILLA']) }}" class="btn btn-info btn-sm">EDITAR</a>
                                    <form action="{{ route('empleado_planillas.destroy', $empleado_planillas['COD_EMPLEADO_PLANILLA']) }}" method="POST" style="display: inline-block;" onsubmit="return confirmDeletion()">
                                        @csrf
                                        @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">ELIMINAR</button>
                                    </form>
                              
                            <script>
                              function confirmDeletion() {
                              return confirm('¿Estás seguro de que deseas eliminar este empleado planilla? Esta acción no se puede deshacer.');
                            }
                             </script>

                                </td>
                            </tr>
                            
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10">No se encontraron planillas de empleado.</td>
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
        Swal.fire({
            title: "Exitoso!",
            text: "",
            icon: "success"
        });

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
