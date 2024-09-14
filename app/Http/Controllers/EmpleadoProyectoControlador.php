<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyectos;
use App\Models\EmpleadoProyectos;
use App\Models\empleados;

class EmpleadoProyectoControlador extends Controller
{
    public function index($codProyecto)
    {
        // Obtener el proyecto por su código
        $proyecto = Proyectos::findOrFail($codProyecto);
        
        // Obtener los empleados asignados a este proyecto
        $empleadosAsignados = EmpleadoProyectos::where('COD_PROYECTO', $codProyecto)
            ->with('empleado') // Cargar los empleados relacionados
            ->get()
            ->pluck('empleado'); // Obtener solo la colección de empleados
        
        // Pasar los datos a la vista
        return view('proyectos.empleados_por_proyecto', [
            'proyecto' => $proyecto,
            'empleados' => $empleadosAsignados,
        ]);
    }

    public function proyectosPorEmpleado($dniEmpleado)
    {
        // Obtener el empleado por su DNI
        $empleado = empleados::where('DNI_EMPLEADO', $dniEmpleado)->firstOrFail();
        
        // Obtener los proyectos asignados a este empleado
        $proyectosAsignados = EmpleadoProyectos::where('DNI_EMPLEADO', $dniEmpleado)
            ->with('proyectos') // Cargar los proyectos relacionados
            ->get()
            ->pluck('proyectos'); // Obtener solo la colección de proyectos
        
        // Pasar los datos a la vista
        return view('empleados.proyectos_por_empleado', [
            'empleado' => $empleado,
            'proyectos' => $proyectosAsignados,
        ]);
    }
}
