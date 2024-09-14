<table class="table table-hover table-bordered">
    <thead class="thead-dark">
        <tr>
            <th>COD EQUIPO</th>
            <th>COD EMPLEADO</th>
            <th>COD PROYECTO</th>
            <th>DESCRIPCIÓN</th>
            <th>ESTADO ASIGNACIÓN</th>
            <th>FECHA INICIO</th>
            <th>FECHA FIN</th>
            <th>ACCIÓN</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($asignaciones as $asignacion)
            <tr>
                <td>{{ $asignacion['COD_EQUIPO'] }}</td>
                <td>{{ $asignacion['COD_EMPLEADO'] }}</td>
                <td>{{ $asignacion['COD_PROYECTO'] }}</td>
                <td>{{ $asignacion['DESCRIPCION'] }}</td>
                <td>{{ $asignacion['COD_ESTADO_ASIGNACION'] }}</td>
                <td>{{ \Carbon\Carbon::parse($asignacion['FECHA_ASIGNACION_INICIO'])->format('Y-m-d') }}</td>
                <td>{{ $asignacion['FECHA_ASIGNACION_FIN'] == 'Pendiente' ? 'Pendiente' : \Carbon\Carbon::parse($asignacion['FECHA_ASIGNACION_FIN'])->format('Y-m-d') }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton{{ $asignacion['COD_ASIGNACION_EQUIPO'] }}" data-bs-toggle="dropdown" aria-expanded="false">
                            Acciones
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $asignacion['COD_ASIGNACION_EQUIPO'] }}">
                            <li><a class="dropdown-item" href="{{ route('asignaciones.edit', $asignacion['COD_ASIGNACION_EQUIPO']) }}">EDITAR</a></li>
                            <li>
                                <form action="{{ route('asignaciones.destroy', $asignacion['COD_ASIGNACION_EQUIPO']) }}" method="POST" class="d-inline" onsubmit="return confirmDelete({{ $asignacion['COD_ASIGNACION_EQUIPO'] }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item">ELIMINAR</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
