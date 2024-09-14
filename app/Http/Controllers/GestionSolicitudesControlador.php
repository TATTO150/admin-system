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

class GestionSolicitudesControlador extends Controller
{
    protected $areas;
    protected $proyectos;
    protected $empleados;

    public function __construct()
    {
        $this->areas = Area::all();
        $this->proyectos = Proyectos::all();
        $this->empleados = Empleados::all();
    }

    public function index()
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de consulta en el objeto SOLICITUD
        $permisoConsultar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'SOLICITUD')
                    ->limit(1);
            })
            ->where('Permiso_Consultar', 'PERMITIDO')
            ->exists();

        if (!$permisoConsultar) {
            return redirect()->route('dashboard')->withErrors('No tiene permiso para ingresar a la ventana de gestion de solicitudes');
        }
        $solicitudesEspera = solitudes::where('ESTADO_SOLICITUD', 'Espera')->with(['empleado', 'area', 'proyecto'])->get();
        $otrasSolicitudes = solitudes::where('ESTADO_SOLICITUD', '!=', 'Espera')->with(['empleado', 'area', 'proyecto'])->get();

        $empleados = $this->empleados->keyBy('COD_EMPLEADO');
        $proyectos = $this->proyectos->keyBy('COD_PROYECTO');

        return view('gestionSolicitudes.index', compact('solicitudesEspera', 'otrasSolicitudes', 'empleados', 'proyectos'));
    }

    public function gestionar($COD_SOLICITUD)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de actualización en el objeto SOLICITUD
        $permisoActualizacion = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'SOLICITUD')
                    ->limit(1);
            })
            ->where('Permiso_Actualizacion', 'PERMITIDO')
            ->exists();

        if (!$permisoActualizacion) {
            return redirect()->route('gestionSolicitudes.index')->withErrors('No tiene permiso para gestionar solicitudes');
        }
        // Obtener los datos de la solicitud desde la base de datos
        $solicitud = solitudes::where('COD_SOLICITUD', $COD_SOLICITUD)
                            ->with(['empleado', 'area', 'proyecto'])
                            ->first();

        if (!$solicitud) {
            abort(404, 'Solicitud no encontrada');
        }

        // Obtener áreas, proyectos y empleados
        $areas = $this->areas->keyBy('COD_AREA');
        $proyectos = $this->proyectos->keyBy('COD_PROYECTO');
        $empleados = $this->empleados->keyBy('COD_EMPLEADO');

        return view('gestionSolicitudes.gestionar', compact('solicitud', 'areas', 'proyectos', 'empleados'));
    }



    public function aprobar($COD_SOLICITUD)
    {
        // Encuentra la solicitud por su código
        $solicitud = solitudes::findOrFail($COD_SOLICITUD);
        
        // Cambia el estado de la solicitud a 'APROBADA'
        $solicitud->ESTADO_SOLICITUD = 'APROBADA'; 
        $solicitud->save();

        // Crea una nueva instancia de Compras
        $compra = new Compras();
        
        // Asigna los valores de la solicitud a la compra
        $compra->DESC_COMPRA = $solicitud->DESC_SOLICITUD;
        $compra->COD_PROYECTO = $solicitud->COD_PROYECTO;
        $compra->FEC_REGISTRO = now(); // Asigna la fecha actual como la fecha de registro
        $compra->TIP_COMPRA = 'Solicitud Aprobada'; // O el tipo de compra que corresponda
        $compra->PRECIO_VALOR = $solicitud->PRESUPUESTO_SOLICITUD;

        // Guarda la compra en la base de datos
        $compra->save();

        // Crea una nueva instancia de Gastos
        $gasto = new Gastos();
        
        // Asigna los valores de la solicitud al gasto
        $gasto->COD_COMPRA = $compra->COD_COMPRA; // Asigna el código de compra generado automáticamente
        $gasto->COD_PROYECTO = $solicitud->COD_PROYECTO;
        $gasto->FEC_REGISTRO = now(); // Asigna la fecha actual como la fecha de registro
        $gasto->SUBTOTAL = $solicitud->PRESUPUESTO_SOLICITUD;
        $gasto->TOTAL = $solicitud->PRESUPUESTO_SOLICITUD; // O ajusta según sea necesario

        // Guarda el gasto en la base de datos
        $gasto->save();

        // Redirige al índice de gestión de solicitudes con un mensaje de éxito
        return redirect()->route('gestionSolicitudes.index')->with('success', 'Solicitud aprobada exitosamente.');
    }



public function rechazar($COD_SOLICITUD)
    {
        $solicitud = solitudes::findOrFail($COD_SOLICITUD);

             // Verifica si la solicitud fue previamente aprobada
        if ($solicitud->ESTADO_SOLICITUD === 'APROBADA') {
            // Encuentra la compra asociada
            $compra = Compras::where('DESC_COMPRA', $solicitud->DESC_SOLICITUD)
                         ->where('COD_PROYECTO', $solicitud->COD_PROYECTO)
                         ->first();

            if ($compra) {
              // Encuentra el gasto asociado
             $gasto = Gastos::where('COD_COMPRA', $compra->COD_COMPRA)->first();
            
             // Elimina el gasto asociado si existe
                if ($gasto) {
                $gasto->delete();
            }

                // Elimina la compra asociada
                $compra->delete();
            }
        }

        $solicitud->ESTADO_SOLICITUD = 'Rechazada'; // O el estado que correspondan para el rechazo
        $solicitud->save();

        return redirect()->route('gestionSolicitudes.index')->with('success', 'Solicitud rechazada exitosamente.');
    }

}
