<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use App\Models\Permisos;
use App\Models\Bitacora;
use App\Models\Planillas;
use App\Models\Empleados;
use App\Models\Compras;
use App\Models\Gastos;
use App\Models\Proyectos;
use App\Models\Cargo;
use App\Models\Area;
use App\Models\Asignacion_Equipos;
use App\Models\Solitudes;
use App\Models\Mantenimientos;
use App\Models\Equipos;
use App\Models\EstadoAsignacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
{
    // Cantidad de proyectos por estado
    $proyectosActivosCount = Proyectos::where('ESTADO_PROYECTO', 'ACTIVO')->count();
    $proyectosSuspendidosCount = Proyectos::where('ESTADO_PROYECTO', 'SUSPENDIDO')->count();
    $proyectosFinalizadosCount = Proyectos::where('ESTADO_PROYECTO', 'FINALIZADO')->count();
    $proyectosAperturaCount = Proyectos::where('ESTADO_PROYECTO', 'APERTURA')->count();

    // Cantidad de solicitudes pendientes de revisión
    $solicitudesPendientesCount = Solitudes::where('ESTADO_SOLICITUD', 'ESPERA')->count();

    // Cantidad de planillas con fecha de pago igual a hoy
    $planillasHoyCount = Planillas::whereDate('FECHA_GENERADA', Carbon::today())->count();

      // Cantidad de planillas con fecha de pago igual a hoy
      $planillasHoyCount = Planillas::whereDate('FECHA_GENERADA', Carbon::today())->count();

    // Cantidad de empleados por proyecto con nombres de proyectos
    $empleadosPorProyecto = DB::table('tbl_proyectos')
        ->join('tbl_empleado_proyectos', 'tbl_proyectos.COD_PROYECTO', '=', 'tbl_empleado_proyectos.COD_PROYECTO')
        ->select('tbl_proyectos.NOM_PROYECTO', DB::raw('count(*) as total_empleados'))
        ->groupBy('tbl_proyectos.NOM_PROYECTO')
        ->get();

    // Cantidad de mantenimientos por estado
// Contar los equipos por estado de asignación
$asignacionesActivasCount = Equipos::whereHas('estadoEquipo', function($query) {
    $query->where('COD_ESTADO_EQUIPO', 1);
})->count();

$asignacionesFinalizadasCount = Equipos::whereHas('estadoEquipo', function($query) {
    $query->where('COD_ESTADO_EQUIPO', 2);
})->count();

$mantenimientoActivoCount = Equipos::whereHas('estadoEquipo', function($query) {
    $query->where('COD_ESTADO_EQUIPO', 3);
})->count();

$mantenimientoFinalizadoCount = Equipos::whereHas('estadoEquipo', function($query) {
    $query->where('COD_ESTADO_EQUIPO', 4);
})->count();

    $equiposTotalCount = Equipos::count();
    return view('dashboard', [
        'usuariosCount' => User::count(),
        'rolesCount' => Rol::count(),
        'permisosCount' => Permisos::count(),
        'bitacoraCount' => Bitacora::count(),
        'planillasCount' => Planillas::count(),
        'empleadosCount' => Empleados::count(),
        'comprasCount' => Compras::count(),
        'gastosCount' => Gastos::count(),
        'proyectosCount' => Proyectos::count(),
        'cargosCount' => Cargo::count(),
        'areasCount' => Area::count(),
        'solicitudesCount' => Solitudes::count(),
        'mantenimientosCount' => Mantenimientos::count(),
        'equiposCount' => Equipos::count(),
        'proyectosActivosCount' => $proyectosActivosCount,
        'proyectosSuspendidosCount' => $proyectosSuspendidosCount,
        'proyectosFinalizadosCount' => $proyectosFinalizadosCount,
        'proyectosAperturaCount' => $proyectosAperturaCount,
        'solicitudesPendientesCount' => $solicitudesPendientesCount,
        'planillasHoyCount' => $planillasHoyCount,
        'empleadosPorProyecto' => $empleadosPorProyecto,
        'asignacionesActivasCount' => $asignacionesActivasCount,
        'asignacionesFinalizadasCount' => $asignacionesFinalizadasCount,
        'mantenimientoActivoCount' => $mantenimientoActivoCount,
        'mantenimientoFinalizadoCount' => $mantenimientoFinalizadoCount,
        'equiposTotalCount' => $equiposTotalCount, // Nuevo dato agregado
    ]);
}
}
