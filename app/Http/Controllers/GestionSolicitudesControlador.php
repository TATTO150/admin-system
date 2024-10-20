<?php

namespace App\Http\Controllers;

use App\Models\Proyectos;
use App\Models\solitudes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Area;
use App\Models\Permisos;
use Illuminate\Support\Facades\Auth;
use App\Models\Gastos;
use App\Models\Compras;
use App\Models\Empleados;
use App\Providers\PermisoService;

class GestionSolicitudesControlador extends Controller
{
    protected $areas;
    protected $proyectos;
    protected $empleados;
    protected $permisoService;
    protected $bitacora;

    public function __construct(BitacoraController $bitacora, PermisoService $permisoService)
    {
        $this->areas = Area::all();
        $this->proyectos = Proyectos::all();
        $this->empleados = Empleados::all();
        $this->bitacora = $bitacora;
        $this->permisoService = $permisoService;
    }

    public function index()
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Nueva validación de permisos
        $this->permisoService->tienePermiso('GESTIONSOLICITUD', 'Consultar', true);

        $solicitudesEspera = Compras::where('COD_ESTADO', 1)->get();
        $otrasSolicitudes = Compras::where('COD_ESTADO', '!=', 1)->get();
        $proyectos = $this->proyectos->keyBy('COD_PROYECTO');
        $usuarios = \App\Models\User::all()->keyBy('Id_usuario');
        $estados = \App\Models\EstadoCompra::all()->keyBy('COD_ESTADO');
        $tipos = \App\Models\TipoCompra::all()->keyBy('COD_TIPO');

        return view('gestionSolicitudes.index', compact('solicitudesEspera', 'otrasSolicitudes', 'usuarios', 'estados', 'tipos', 'proyectos'));
    }

    public function gestionar($COD_COMPRA)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Nueva validación de permisos
        $this->permisoService->tienePermiso('GESTIONSOLICITUD', 'Consultar', true);

        // Obtener los datos de la solicitud desde la base de datos
        $solicitud = Compras::where('COD_COMPRA', $COD_COMPRA)->first();

        if (!$solicitud) {
            abort(404, 'Solicitud no encontrada');
        }

        // Obtener áreas, proyectos y empleados
        $areas = $this->areas->keyBy('COD_AREA');
        $proyectos = $this->proyectos->keyBy('COD_PROYECTO');
        $empleados = $this->empleados->keyBy('COD_EMPLEADO');
        $usuarios = \App\Models\User::all()->keyBy('Id_usuario');
        $estados = \App\Models\EstadoCompra::all()->keyBy('COD_ESTADO');
        $tipos = \App\Models\TipoCompra::all()->keyBy('COD_TIPO');

        return view('gestionSolicitudes.gestionar', compact('solicitud', 'usuarios', 'estados','proyectos', 'empleados'));
    }



    public function aprobar($COD_COMPRA)
    {
        // Encuentra la solicitud por su código
        $solicitud = Compras::findOrFail($COD_COMPRA);
        
        // Cambia el estado de la solicitud a 'APROBADA'
        $solicitud->COD_ESTADO = 2; 
        $solicitud->save();
    
        // Redirige al índice de gestión de solicitudes con un mensaje de éxito
        return redirect()->route('gestionSolicitudes.index')->with('success', 'Solicitud aprobada exitosamente.');
    }



    public function rechazar($COD_COMPRA)
    {
        $solicitud = Compras::findOrFail($COD_COMPRA);

        $solicitud->COD_ESTADO = 3; // O el estado que correspondan para el rechazo
        $solicitud->save();

        return redirect()->route('gestionSolicitudes.index')->with('success', 'Solicitud rechazada exitosamente.');
    }

}
