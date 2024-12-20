<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BitacoraController;
use App\Providers\PermisoService;
use App\Models\EmpleadoPlanilla;
use App\Models\Planillas;
use App\Models\Permisos;
use App\Models\Empleados;
use App\Models\Proyectos;
use App\Models\TipoPlanilla;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Rules\Validaciones;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PlanillaControlador extends Controller
{
    protected $planillas;
    protected $proyectos;
    protected $bitacora;
    protected $permisoService;

    public function __construct(BitacoraController $bitacora, PermisoService $permisoService)
    {
        $this->bitacora = $bitacora;
        $this->proyectos = Proyectos::all();
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
        
        //Nueva validacvion de permisos
        $this->permisoService->tienePermiso('PLANILLA', 'Consultar', true);
       
        $planillas = Planillas::all();
        // Obtener los proyectos
    $proyectos = Proyectos::all();

    $tipos_planilla = TipoPlanilla::all()->keyBy('COD_TIPO_PLANILLA');

    $this->bitacora->registrarEnBitacora(21, 'Ingreso a la ventana de planillas', 'Ingreso');
        
    
        return view('planillas.index', compact('planillas', 'proyectos', 'tipos_planilla'));
    }

    public function generarReporte(Request $request)
{
    // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
    $mesInicio = $request->input('fecha_inicio_reporte');
    $mesFin = $request->input('fecha_fin_reporte');
    $tipoPlanilla = $request->input('tipo_planilla');

    // Convierte los valores del mes a fechas (inicio y fin del mes)
    $fechaInicio = \Carbon\Carbon::parse($mesInicio)->startOfMonth()->format('Y-m-d');
    $fechaFin = \Carbon\Carbon::parse($mesFin)->endOfMonth()->format('Y-m-d');

    // Filtra los datos de las planillas por rango de fechas y tipo de planilla
    $planillasQuery = Planillas::select('COD_PLANILLA', 'FECHA_GENERADA', 'TOTAL_PAGADO', 'COD_TIPO_PLANILLA', 'MES')
        ->whereBetween('MES', [$fechaInicio, $fechaFin]);

    // Si se selecciona un tipo de planilla, se agrega al filtro
    if ($tipoPlanilla) {
        $planillasQuery->where('COD_TIPO_PLANILLA', $tipoPlanilla);
        $tipo_planilla = "Por tipo";
    }

    $planillas = $planillasQuery->get();
    $tipo_reporte = 'En Rango de: ' . $fechaInicio . ' - ' . $fechaFin;


    // Validar si no se encontraron empleados
    if ($planillas->isEmpty()) {
        // Retornar un mensaje de error en formato JSON
        return response()->json(['error' => 'No se encontraron empleados para el reporte solicitado'], 404);
    }

    // Resto del código para preparar el PDF
    $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
    $proyectos = Proyectos::all();
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    $pdf = Pdf::loadView('planillas.pdf', compact('planillas', 'proyectos', 'fechaHora', 'logoBase64', 'tipo_reporte'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    return $pdf->stream();
}



public function generarReporteGeneral()
{
    // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
    // Obtiene todas las planillas
    $planillas = Planillas::select('COD_PLANILLA', 'FECHA_GENERADA', 'TOTAL_PAGADO')->get();
    $tipos_planilla = TipoPlanilla::all()->keyBy('COD_TIPO_PLANILLA');

     // Validar si no se encontraron empleados
     if ($planillas->isEmpty()) {
        // Retornar un mensaje de error en formato JSON
        return response()->json(['error' => 'No se encontraron empleados para el reporte solicitado'], 404);
    }

    // Resto del código para preparar el PDF
    $tipo_reporte = 'Reporte General de Planillas';
    $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
    $proyectos = Proyectos::all();
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    $pdf = Pdf::loadView('planillas.pdf', compact('planillas', 'proyectos', 'fechaHora', 'logoBase64', 'tipos_planilla', 'tipo_reporte'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    return $pdf->stream();
}

    public function pdfIndividual($id)
    {
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
        $planilla = Planillas::findOrFail($id);

        // Obtener empleados con relaciones de área y cargo
        $empleados = EmpleadoPlanilla::where('COD_PLANILLA', $id)
            ->with(['empleado.area', 'empleado.cargo']) // Asume que las relaciones con 'area' y 'cargo' están definidas en el modelo Empleado
            ->get()
            ->pluck('empleado');


        $totalPagar = $empleados->sum('SALARIO_NETO');

        // Formato fecha y logo
        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

        $pdf = Pdf::loadView('planillas.pdfPorPlanilla', compact('planilla', 'empleados', 'totalPagar', 'fechaHora', 'logoBase64'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
            ]);

        return $pdf->stream();
    }


    public function crear()
    {
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
        $user = Auth::user();

    //Nueva validacvion de permisos
    $this->permisoService->tienePermiso('Planilla', 'Insercion', true);
       
        $proyectos = Proyectos::all(); 

        return view('planillas.crear', compact('proyectos'));
    }


    public function insertar(Request $request)
    {
        // Validar los datos recibidos, sin incluir TOTAL_PAGAR
        $validator = Validator::make($request->all(), [
            'COD_PROYECTO' => [(new Validaciones)->requerirCampo()->requerirSoloNumeros()->requerirEstadoValidoProyecto()],
            'TIP_PLANILLA' => [(new Validaciones)->requerirCampo()],
            'ESTADO_PLANILLA' => [(new Validaciones)->requerirCampo()],
            'FECHA_PAGO' => [(new Validaciones)->requerirCampo()],
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Asignar 0 a TOTAL_PAGAR
        $totalPagar = 0;
    
        // Enviar los datos para la inserción
        $response = Http::post('http://127.0.0.1:3000/INS_PLANILLA', [
            'COD_PROYECTO' => $request->COD_PROYECTO,
            'TIP_PLANILLA' => $request->TIP_PLANILLA,
            'ESTADO_PLANILLA' => $request->ESTADO_PLANILLA,
            'FECHA_PAGO' => $request->FECHA_PAGO,
            'TOTAL_PAGAR' => $totalPagar, // Usar el valor 0
        ]);
    
        return redirect()->route('planillas.index');
    }

    public function generarPlanilla(Request $request)
{
    $user = Auth::user();
    //Nueva validacvion de permisos
    $this->permisoService->tienePermiso('PLANILLA', 'Insercion', true);

    // Obtener mes y tipo de planilla del request
    $fechaSeleccionada = $request->input('fecha_inicio');
    $codTipoPlanilla = $request->input('tipo_planilla');

    // Inicializar la fecha de planilla con la fecha seleccionada
    $fechaPlanilla = Carbon::parse($fechaSeleccionada);

    // Nueva validación: impedir la generación de planillas para fechas futuras
    if ($fechaPlanilla->isFuture()) {
        return redirect()->route('planillas.index')->withErrors('No se puede generar planillas para fechas futuras.');
    }

    // Validar si ya existe una planilla del mismo tipo en el mes
    $existingPlanillas = Planillas::whereYear('MES', $fechaPlanilla->year)
                                   ->whereMonth('MES', $fechaPlanilla->month)
                                   ->get();

    $monthlyPlanillaExists = $existingPlanillas->where('COD_TIPO_PLANILLA', 1)->isNotEmpty();
    $quincenalCount = $existingPlanillas->where('COD_TIPO_PLANILLA', 2)->count();
    $semanalCount = $existingPlanillas->where('COD_TIPO_PLANILLA', 3)->count();

    // Validación para impedir la creación de planillas si ya existen
if ($codTipoPlanilla == 1) { // Mensual
    if ($monthlyPlanillaExists) {
        return redirect()->route('planillas.index')->withErrors('Ya existe una planilla mensual este mes.');
    }
} elseif ($codTipoPlanilla == 2) { // Quincenal
    if ($monthlyPlanillaExists) {
        return redirect()->route('planillas.index')->withErrors('No se puede generar una planilla quincenal si ya existe una mensual.');
    } elseif ($quincenalCount >= 1) {
        return redirect()->route('planillas.index')->withErrors('Ya existe una planilla quincenal en este mes.');
    } elseif ($semanalCount >= 2) {
        return redirect()->route('planillas.index')->withErrors('No se puede generar otra planilla quincenal con más de dos planillas semanales existentes.');
    }
} elseif ($codTipoPlanilla == 3) { // Semanal
    if ($monthlyPlanillaExists) {
        return redirect()->route('planillas.index')->withErrors('No se puede generar una planilla semanal si ya existe una mensual.');
    } elseif ($quincenalCount >= 1 && $semanalCount >= 2) {
        return redirect()->route('planillas.index')->withErrors('No se puede generar una planilla semanal si ya existen dos planillas semanales.');
    } elseif ($semanalCount >= 4) {
        return redirect()->route('planillas.index')->withErrors('No se puede generar otra planilla semanal en este mes.');
    }
}


    // Establecer la fecha de la planilla según el tipo
if ($codTipoPlanilla == 1) { // Mensual
    $fechaPlanilla = $fechaPlanilla->endOfMonth();
} elseif ($codTipoPlanilla == 2) { // Quincenal
    if ($semanalCount >= 2) {
        $fechaPlanilla = $fechaPlanilla->endOfMonth(); // Si hay dos semanales, la quincenal será al final del mes
    } elseif ($semanalCount === 1) {
        $fechaPlanilla = $fechaPlanilla->day(21); // Si hay una semanal, la quincenal será el 21
    } else {
        $fechaPlanilla = $fechaPlanilla->day(15); // Si no hay semanales, la quincenal será el 15
    }
} elseif ($codTipoPlanilla == 3) { // Semanal
    if ($quincenalCount >= 1) {
        if ($semanalCount === 1) {
            $fechaPlanilla = $fechaPlanilla->endOfMonth(); // Si hay una quincenal y una semanal, la semanal será al final del mes
        } else {
            $fechaPlanilla = $fechaPlanilla->day(21); // Si hay una quincenal y no hay más semanales, la semanal será el 21
        }
    } else {
        if ($semanalCount === 0) {
            $fechaPlanilla = $fechaPlanilla->day(7); // Si no hay semanales, la primera será el 7
        } elseif ($semanalCount === 1) {
            $fechaPlanilla = $fechaPlanilla->day(14); // Si ya hay una semanal, la siguiente será el 14
        } elseif ($semanalCount === 2) {
            $fechaPlanilla = $fechaPlanilla->day(21); // Si ya hay dos semanales, la siguiente será el 21
        } elseif ($semanalCount >= 3) {
            $fechaPlanilla = $fechaPlanilla->endOfMonth(); // Si ya hay tres o más, la última será al final del mes
        }
    }
}

    $empleados = empleados::where('ESTADO_EMPLEADO', 'ACTIVO')
    ->where('FEC_INGRESO_EMPLEADO', '<=', $fechaPlanilla)
    ->where(function ($query) use ($fechaPlanilla) {
        // Incluir empleados que no tienen fecha de salida o cuya fecha de salida es después de la fecha de la planilla
        $query->whereNull('FECHA_SALIDA')
            ->orWhere('FECHA_SALIDA', '>=', $fechaPlanilla);
    })
    ->get();

    // Nueva validación: si no hay empleados para la fecha seleccionada
    if ($empleados->isEmpty()) {
        return redirect()->route('planillas.index')->withErrors('No hay empleados para el mes seleccionado.');
    }

    // Calcular total a pagar
    $totalPagar = $empleados->sum('SALARIO_NETO');

    // Crear nueva planilla
    $planilla = new Planillas();
    $planilla->FECHA_GENERADA = Carbon::now();
    $planilla->TOTAL_PAGADO = $totalPagar;
    $planilla->MES = $fechaPlanilla;
    $planilla->COD_TIPO_PLANILLA = $codTipoPlanilla;
    $planilla->save();

    $this->bitacora->registrarEnBitacora(21, 'Nueva planilla generada', 'Insert');

    // Llamar a la función show para redirigir a la vista de detalle de la planilla
    return view('planillas.confirm', compact('planilla', 'empleados', 'totalPagar'));
}

public function confirmarPlanilla($COD_PLANILLA){
    // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
    // Buscar la planilla específica usando el COD_PLANILLA
    $planilla = Planillas::where('COD_PLANILLA', $COD_PLANILLA)->first();

    // Verificar si se encontró la planilla
    if (!$planilla) {
        // Manejar el caso donde no se encuentra la planilla (puedes ajustar este mensaje según sea necesario)
        return response()->json(['error' => 'Planilla no encontrada'], 404);
    }

    // Obtener el mes de la planilla
    $mesPlanilla = $planilla->MES;

    // Filtrar empleados activos durante el mes de la planilla
    $empleados = empleados::where('ESTADO_EMPLEADO', 'ACTIVO')
        ->where(function ($query) use ($mesPlanilla) {
            $query->whereMonth('FEC_INGRESO_EMPLEADO', '<=', $mesPlanilla)
                ->where(function ($query) use ($mesPlanilla) {
                    $query->whereNull('FECHA_SALIDA')
                        ->orWhereMonth('FECHA_SALIDA', '>=', $mesPlanilla);
                });
        })
        ->get();

        // Asignar empleados a la planilla usando el modelo EmpleadoPlanilla
    foreach ($empleados as $empleado) {
        EmpleadoPlanilla::create([
            'COD_EMPLEADO' => $empleado->COD_EMPLEADO,
            'COD_PLANILLA' => $planilla->COD_PLANILLA,
        ]);
    }

    return redirect()->route('planillas.index')->with('success', 'Planilla actualizada correctamente');
}

public function cancelarPlanilla($COD_PLANILLA){
    // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
    // Buscar la planilla por su COD_PLANILLA
    $planilla = Planillas::find($COD_PLANILLA);

    // Verificar si se encontró la planilla
    if (!$planilla) {
        // Manejar el caso donde no se encuentra la planilla (puedes ajustar este mensaje según sea necesario)
        return response()->json(['error' => 'Planilla no encontrada'], 404);
    }

    // Eliminar la planilla
    $planilla->delete();

    return redirect()->route('planillas.index')->with('success', 'Planilla cancelada correctamente');

}

public function show($COD_PLANILLA)
{
    // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
    $user = Auth::user();

    //Nueva validacvion de permisos
    $this->permisoService->tienePermiso('PLANILLA', 'Actualizacion', true);

    $planilla = Planillas::findOrFail($COD_PLANILLA);

    // Obtener empleados asignados a la planilla específica
    $empleados = EmpleadoPlanilla::where('COD_PLANILLA', $COD_PLANILLA)
        ->with('empleado') // Carga la relación con el modelo empleado
        ->get()
        ->pluck('empleado'); // Extrae la colección de empleados

    $totalPagar = $planilla->TOTAL_PAGADO;

    return view('planillas.show', compact('planilla', 'empleados', 'totalPagar'));
}


    
    public function destroy($COD_PLANILLA)
    {
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
        //Nueva validacvion de permisos
        $this->permisoService->tienePermiso('PLANILLA', 'Eliminacion', true);

        try {
            DB::statement('CALL ELI_PLANILLA(?)', [$COD_PLANILLA]);
           $this->bitacora->registrarEnBitacora(21, 'Planilla eliminada', 'Delete'); // ID_objetos 12: 'planillas'*/
            return redirect()->route('planillas.index')->with('success', 'Planilla eliminada correctamente');
        } catch (\Exception $e) {
            return redirect()->route('planillas.index')->with('error', 'Error al eliminar planilla');
        }
    }

    public function edit($COD_PLANILLA)
    {
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
        $user = Auth::user();

    //Nueva validacvion de permisos
    $this->permisoService->tienePermiso('PLANILLA', 'Actualizacion', true);
       
    
    $planillas = Planillas::findOrFail($COD_PLANILLA);
     // Obtener empleados asignados a la planilla específica
     $empleados = EmpleadoPlanilla::where('COD_PLANILLA', $COD_PLANILLA)
     ->with('empleado') // Carga la relación con el modelo empleado
     ->get()
     ->pluck('empleado'); // Extrae la colección de empleados

    $totalPagar = $planillas->TOTAL_PAGADO;

        $proyectos = $this->proyectos;
    
        return view('planillas.edit', compact('planillas', 'empleados', 'totalPagar'));
    }

    public function eliminarEmpleados(Request $request)
    {
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
        // Validar que al menos un empleado haya sido seleccionado
        if (!$request->has('empleados')) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un empleado.');
        }

        // Obtener los empleados seleccionados
        $empleadosSeleccionados = $request->input('empleados');

        // Eliminar los registros de la tabla 'tbl_empleado_planilla'
        EmpleadoPlanilla::whereIn('COD_EMPLEADO', $empleadosSeleccionados)->delete();

        // Redirigir con un mensaje de éxito
        return redirect()->back()->with('success', 'Los empleados seleccionados han sido eliminados de la planilla.');
    }
    
    public function update(Request $request, $COD_PLANILLA)
    {
        $validator = Validator::make($request->all(), [
            'COD_PROYECTO' => [(new Validaciones)->requerirCampo()->requerirEstadoValidoProyecto()->prohibirCeroYNegativos()->prohibirSimbolosSalvoDecimal()],
            'FECHA_PAGO' => [(new Validaciones)->requerirCampo()->requerirFechaIngresoValida()],
            'TOTAL_PAGAR' => [(new Validaciones)->requerirCampo()->prohibirCeroYNegativos()->prohibirSimbolosSalvoDecimal()->requerirSinEspacios()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Obtén la planilla actual para obtener el COD_PROYECTO antiguo
        $planilla = Planillas::findOrFail($COD_PLANILLA);

        // Calcula la fecha de pago
        $fechaPago = $this->calcularFechaPago($request->FECHA_PAGO);

        // Actualiza los datos de la planilla
        $planilla->update([
            'FECHA_PAGO' => $fechaPago,
            'TOTAL_PAGADO' => $request->TOTAL_PAGADO,
        ]);

        $this->bitacora->registrarEnBitacora(21, 'Plnanilla actualizada con exito', 'Update');

        return redirect()->route('planillas.index');
    }

    
    private function calcularFechaPago($frecuencia)
    {
        $fechaPago = null;
    
        switch ($frecuencia) {
            case 'MENSUAL':
                $fechaPago = Carbon::now()->endOfMonth()->toDateString();
                break;
            case 'QUINCENAL':
                $diaHoy = Carbon::now()->day;
                if ($diaHoy <= 15) {
                    $fechaPago = Carbon::now()->startOfMonth()->addDays(14)->toDateString(); // 15 del mes
                } else {
                    $fechaPago = Carbon::now()->endOfMonth()->toDateString(); // último día del mes
                }
                break;
            case 'SEMANAL':
                $fechaPago = Carbon::now()->endOfWeek()->toDateString();
                break;
            default:
                $fechaPago = Carbon::now()->toDateString();
                break;
        }
    
        return $fechaPago;
    }
}
