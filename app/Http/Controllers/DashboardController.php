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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
{
    $user = Auth::user();
    $fechaVencimiento = $user->Fecha_Vencimiento ? Carbon::parse($user->Fecha_Vencimiento) : null; // Aseguramos que sea un objeto Carbon si existe
    $mostrarAlerta = false;
    $ahora = Carbon::now(); // Fecha actual

   // Validar si el usuario tiene pendiente la autenticación de dos factores
   if (!is_null($user->two_factor_secret) && ($user->two_factor_status === 0 || is_null($user->two_factor_status))) {
        return redirect()->route('two-factor.login');
    }

     // Comprobar si el usuario ya tiene una sesión activa
     $existingSession = DB::table('usuarios_logueados')->where('user_id', $user->Id_usuario)->first();

     if ($existingSession) {
         return redirect()->route('unica.sesion'); // Ruta a la vista de advertencia
     }

     // Insertar el nuevo registro de sesión en la tabla usuarios_logueados
     DB::table('usuarios_logueados')->insert([
         'user_id' => $user->Id_usuario,
         'session_id' => Session::getId(),
     ]);

 // Verificar si el usuario ya cambió la contraseña
 if ($user->password_updated_at && $user->password_updated_at->gt($fechaVencimiento)) {
    // Si el usuario ya cambió la contraseña, no mostrar alerta
    $mostrarAlerta = false;
} else {
    // Asegurarse de que la fecha de vencimiento es válida
    if ($fechaVencimiento) {
        // Calcular la diferencia en días entre la fecha actual y la fecha de vencimiento
        $diasRestantes = (int) $ahora->diffInDays($fechaVencimiento, false); // Obtener la diferencia como entero

        if ($diasRestantes === 0) {
            // Calcular la diferencia de tiempo entre ahora y la fecha de vencimiento en minutos
            $totalMinutosRestantes = $ahora->diffInMinutes($fechaVencimiento, false);
        
            // Si los minutos restantes son negativos, significa que ya ha pasado la fecha de vencimiento
            if ($totalMinutosRestantes < 0) {
                $mostrarAlerta = false;
            } else {
                // Calcular horas y minutos restantes
                $horasRestantes = floor($totalMinutosRestantes / 60);
                $minutosRestantes = $totalMinutosRestantes % 60;
        
                $mostrarAlerta = true;
                session()->flash('alert', 'Su contraseña expirará en ' . $horasRestantes . ' horas y ' . $minutosRestantes . ' minutos. Debe cambiar su contraseña o su cuenta será bloqueada.');
            }
        }
   
        // Mostrar alerta si faltan entre 1 y 15 días
        elseif ($diasRestantes > 0 && $diasRestantes <= 15) {
            $mostrarAlerta = true;
            session()->flash('alert', 'Su contraseña expirará en ' . $diasRestantes . ' días. Debe cambiar su contraseña o su cuenta será bloqueada.');
        }
         // Si la fecha de vencimiento ya pasó y el usuario no ha cambiado la contraseña
         elseif ($diasRestantes < 0 && !$user->password_updated_at) {
            // Bloquear al usuario (Estado_Usuario = 3 para indicar bloqueado)
            DB::table('tbl_ms_usuario')
    ->where('Id_usuario', $user->Id_usuario)
    ->update([
        'Estado_Usuario' => 3,       // Estado bloqueado numérico
        'Estado_Usuario' => 'BLOQUEADO'      // Estado textual (si tienes una columna que almacena "BLOQUEADO")
    ]);

      // Cerrar la sesión del usuario
      Auth::logout();

            // Redirigir al usuario a una página bloqueada o mostrar un mensaje
            return redirect()->route('bloqueado-por-no-cambiar')->with('alert', 'Tu cuenta ha sido bloqueada por no cambiar tu contraseña a tiempo.');
        }
    }
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
        'equiposTotalCount' => $equiposTotalCount,
        'mostrarAlerta' => $mostrarAlerta, // Nuevo dato agregado
    ]);
}
}
}