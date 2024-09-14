<?php

namespace App\Http\Controllers;

use App\Models\Planillas;
use App\Models\Proyectos;
use App\Models\EmpleadoPlanilla;
use App\Models\Empleados;
use App\Models\Permisos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BitacoraController;
use App\Models\Cargo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PagoPlanillasControlador extends Controller
{
    protected $proyectos;
    protected $bitacora;

    public function __construct(BitacoraController $bitacora)
    {
        $this->proyectos = Proyectos::all();
        $this->bitacora = $bitacora;
    }

    public function index()
    {
        $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de consulta en el objeto PROYECTO
    $permisoConsultar = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'PLANILLA')
                ->limit(1);
        })
        ->where('Permiso_Consultar', 'PERMITIDO')
        ->exists();

    if (!$permisoConsultar) {
        $this->bitacora->registrarEnBitacora(21, 'Intento de ingreso a la ventana de planillas sin permisos', 'Ingreso');
        
        return redirect()->route('dashboard')->withErrors('No tiene permiso para ingresar a la ventana de planillas');
    }

        // Obtener todas las planillas
        $todasPlanillas = Planillas::all();

        // Obtener los proyectos, si es necesario
        $proyectos = Proyectos::all(); // Asegúrate de tener el modelo y la consulta correcta

        // Obtener la fecha actual
        $fechaActual = Carbon::now()->startOfDay();

        // Separar planillas atrasadas de las no atrasadas
        $planillasHoy = $todasPlanillas->filter(function ($planilla) use ($fechaActual) {
            $fechaPago = Carbon::parse($planilla->FECHA_PAGO);
            return $fechaPago->isSameDay($fechaActual);
        });

        $planillasGenerales = $todasPlanillas->filter(function ($planilla) use ($fechaActual) {
            $fechaPago = Carbon::parse($planilla->FECHA_PAGO);
            return !$fechaPago->isSameDay($fechaActual);
        });

        $this->bitacora->registrarEnBitacora(21, 'Ingreso a la ventana de planillas', 'Ingreso');
        
        // Pasar las variables a la vista
        return view('pagoPlanillas.index', [
            'planillasHoy' => $planillasHoy,
            'planillasGenerales' => $planillasGenerales,
            'proyectos' => $proyectos
        ]);
    }


    public function gestionar($COD_PROYECTO)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de actualización en el objeto PROYECTO
        $permisoActualizar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'PLANILLA')
                    ->limit(1);
            })
            ->where('Permiso_Actualizacion', 'PERMITIDO')
            ->exists();

        if (!$permisoActualizar) {
            $this->bitacora->registrarEnBitacora(21, 'Intento de gestionar planillas sin permisos', 'Ingreso');
        
            return redirect()->route('pagoPlanillas.index')->withErrors('No tiene permiso para gestionar planillas');
        }
        // Obtener la planilla correspondiente al proyecto
        $planilla = Planillas::where('COD_PROYECTO', $COD_PROYECTO)->first();

        if (!$planilla) {
            // Manejar el caso donde no se encuentra la planilla
            return redirect()->back()->with('error', 'No se encontró la planilla para el proyecto especificado.');
        }

       // Obtener el proyecto asociado a la planilla 
        $proyecto = Proyectos::find($COD_PROYECTO);

        // Obtener los empleados asociados al proyecto
        $empleados = Empleados::where('COD_PROYECTO', $COD_PROYECTO)->get();

        return view('pagoPlanillas.gestionar', compact('planilla', 'proyecto', 'empleados'));
    }


    public function pagar($COD_PLANILLA)
    {
        // Encuentra la planilla por su código
        $planilla = Planillas::find($COD_PLANILLA);

        // Verifica si la planilla existe
        if (!$planilla) {
            return view('pagoPlanillas.index')->with('error', 'Planilla no encontrada.');
        }

        // Verifica si la planilla ya ha sido pagada este mes
        $fechaPagoActual = Carbon::now();
        $fechaPagoPlanilla = Carbon::parse($planilla->FECHA_PAGO);

        if ($fechaPagoPlanilla->isSameMonth($fechaPagoActual) && $planilla->ESTADO_PLANILLA == 'PAGADA') {
            $this->bitacora->registrarEnBitacora(21, 'Intento de pago de planilla ya pagada este mes', 'Error');
            return redirect()->route('pagoPlanillas.gestionar', ['COD_PROYECTO' => $planilla->COD_PROYECTO])
                ->with('error', 'Esta planilla ya ha sido pagada en este mes. Próxima fecha de pago: ' . $fechaPagoPlanilla->format('Y/m/d'));
        }

        // Actualiza el estado de la planilla a PAGADA
        $planilla->ESTADO_PLANILLA = 'PAGADA';

        // Calcula la fecha de pago según el periodo de pago
        $fechaPago = Carbon::now();
        switch ($planilla->PERIODO_PAGO) {
            case 'MENSUAL':
                $fechaPago = $fechaPago->endOfMonth()->addMonth();
                break;
            case 'QUINCENAL':
                if ($fechaPago->day > 15) {
                    $fechaPago = $fechaPago->endOfMonth();
                } else {
                    $fechaPago = $fechaPago->day(15)->addMonth();
                }
                break;
            case 'SEMANAL':
                $fechaPago = $fechaPago->endOfWeek()->addWeek();
                break;
            default:
                // Maneja cualquier otro caso o lanza una excepción si es necesario
                break;
        }

        // Guarda la próxima fecha de pago
        $planilla->FECHA_PAGO = $fechaPago->toDateString();
        $planilla->save();

        $this->bitacora->registrarEnBitacora(21, 'Pago de planillas exitoso', 'Ingreso');

        // Redirige de vuelta con un mensaje de éxito
        return redirect()->route('pagoPlanillas.gestionar', ['COD_PROYECTO' => $planilla->COD_PROYECTO])
            ->with('success', 'Planilla pagada exitosamente. Próxima fecha de pago: ' . $fechaPago->format('Y/m/d'));
    }


    public function pdf()
    {
        // Obtener las planillas sin el campo TIP_PLANILLA
        $planillas = Planillas::select('COD_PLANILLA', 'COD_PROYECTO', 'ESTADO_PLANILLA', 'FECHA_PAGO', 'TOTAL_PAGAR', 'PERIODO_PAGO')
        ->get();

        $proyectos = Proyectos::all();

        // Obtener la fecha y hora actual
        $fechaHora = Carbon::now()->format('d-m-Y H:i:s');

        // Formatear las fechas
        foreach ($planillas as &$planilla) {
            $planilla->FECHA_PAGO = !empty($planilla->FECHA_PAGO) 
                ? Carbon::parse($planilla->FECHA_PAGO)->format('Y-m-d') 
                : 'Fecha no disponible';
        }

        // Cargar logo como base64
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

        // Generar el PDF
        $pdf = Pdf::loadView('planillas.pdf', compact('planillas', 'fechaHora', 'logoBase64', 'proyectos'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
            ]);

        $this->bitacora->registrarEnBitacora(21, 'Generacion de repoprte de planilla', 'Ingreso');

        // Retornar el PDF generado
        return $pdf->stream('pagoPlanillas.pdf');
    }

    public function planillaIndividualpdf($COD_PROYECTO)
    {
        $planilla = Planillas::where('COD_PROYECTO', $COD_PROYECTO)->first();

        if (!$planilla) {
            return redirect()->back()->with('error', 'No se encontró la planilla para el proyecto especificado.');
        }

        $proyecto = Proyectos::find($COD_PROYECTO);
        $empleados = Empleados::where('COD_PROYECTO', $COD_PROYECTO)->get();

        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
       // Cargar logo como base64
       $path = public_path('images/CTraterra.jpeg');
       $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

        $pdf = Pdf::loadView('pagoPlanillas.planillaIndividualpdf', compact('planilla', 'proyecto', 'empleados', 'fechaHora', 'logoBase64'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
            ]);
        
        $this->bitacora->registrarEnBitacora(21, 'Generacion de reporte de planilla', 'Ingreso');

        return $pdf->stream();
    }

    public function destroy($COD_PLANILLA)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de eliminación en el objeto PROYECTO
        $permisoEliminar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'PROYECTO')
                    ->limit(1);
            })
            ->where('Permiso_Eliminacion', 'PLANILLA')
            ->exists();

        if (!$permisoEliminar) {
            $this->bitacora->registrarEnBitacora(21, 'Intento de eliminar planilla sin permisos', 'Error');

            return redirect()->route('pagoPlanillas.index')->withErrors('No tiene permiso para eliminar planillas');
        }

        try {
            DB::statement('CALL ELI_PLANILLA(?)', [$COD_PLANILLA]);
            $this->bitacora->registrarEnBitacora(21, 'Planilla eliminada exitosamente', 'Delete');

            return redirect()->route('pagoPlanillas.index')->with('success', 'Planilla eliminada correctamente');
        } catch (\Exception $e) {
            $this->bitacora->registrarEnBitacora(21, 'Error al eliminar planilla', 'Error');

            return redirect()->route('pagoPlanillas.index')->with('error', 'Error al eliminar planilla');
        }
    }


}
