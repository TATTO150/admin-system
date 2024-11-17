<?php

namespace App\Http\Controllers;

use App\Models\Proyectos;
use App\Models\solitudes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Area;
use App\Models\User;
use App\Models\Permisos;
use Illuminate\Support\Facades\Auth;
use App\Models\Gastos;
use App\Models\Compras;
use App\Models\Empleados;
use App\Providers\PermisoService;
use App\Mail\NotificacionResultadoSolicitud;
use Illuminate\Support\Facades\Mail;

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
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
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
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Nueva validación de permisos
        $this->permisoService->tienePermiso('GESTIONSOLICITUD', 'Consultar', true);

        // Obtener los datos de la solicitud desde la base de datos
        $solicitud = Compras::where('COD_COMPRA', $COD_COMPRA)->first();

        if (!$solicitud) {
            abort(404, 'Solicitud no encontrada');
        }

        if($solicitud->LIQUIDEZ_COMPRA === 1){
            return redirect()->back()
            ->withErrors("La solicitud ya ha sido liquidada, no puede editarla")
            ->send();
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
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
        // Encuentra la solicitud por su código
        $solicitud = Compras::findOrFail($COD_COMPRA);
        
        // Cambia el estado de la solicitud a 'APROBADA'
        $solicitud->COD_ESTADO = 2; 
        $solicitud->save();

        $motivoRechazo = '';

        $this->enviarEmail($solicitud, $motivoRechazo);
    
        // Redirige al índice de gestión de solicitudes con un mensaje de éxito
        return redirect()->route('gestionSolicitudes.index')->with('success', 'Solicitud aprobada exitosamente.');
    }



    public function rechazar(Request $request, $COD_COMPRA)
    {
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
        // Buscar la solicitud por código
        $solicitud = Compras::findOrFail($COD_COMPRA);

        // Actualizar el estado a rechazado
        $solicitud->COD_ESTADO = 3; // O el estado que corresponda para el rechazo

        // Almacenar el motivo de rechazo en una variable
        $motivoRechazo = $request->input('motivo');

        // Guardar los cambios en la solicitud
        $solicitud->save();

        // Llamar al método de envío de email
        $this->enviarEmail($solicitud, $motivoRechazo);

        // Redirigir con mensaje de éxito
        return redirect()->route('gestionSolicitudes.index')->with('success', 'Solicitud rechazada exitosamente.');
    }

    public function enviarEmail($solicitud, $motivoRechazo){
        // Obtener el nombre de usuario y el nombre del proyecto
        $usuario = User::find($solicitud->Id_usuario);
        $proyecto = Proyectos::where('COD_PROYECTO', $solicitud->COD_PROYECTO)->first();
        
        if($solicitud->COD_ESTADO === 2){ 
        $message = 'Su solicitud ha sido aprobada';
        }
        if($solicitud->COD_ESTADO === 3){
            $message = 'Su solicitud ha sido rechazada';
        }

        // Enviar el correo con los detalles
        $detalles = [
            'usuario' => $usuario ? $usuario->Nombre_Usuario : 'Desconocido',
            'proyecto' => $proyecto ? $proyecto->NOM_PROYECTO : 'Desconocido',
            'resultado' => $message,
            'motivo' => $motivoRechazo,
            'url' => route('login'),
        ];

        Mail::to($usuario->Correo_Electronico)->send(new NotificacionResultadoSolicitud($detalles));
    }

}
