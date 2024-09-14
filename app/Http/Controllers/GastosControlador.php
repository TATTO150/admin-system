<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Gastos;
use App\Models\Compras;
use App\Models\Permisos;
use App\Models\Proyectos;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Rules\Validaciones;
use Illuminate\Support\Facades\Log;

class GastosControlador extends Controller
{
    protected $apiBaseUrl = 'http://localhost:3000';
    protected $bitacora;
    protected $proyectos;
    protected $compras;


    public function __construct(BitacoraController $bitacora)
    {
        $this->bitacora = $bitacora;
        $this->proyectos = Proyectos::all();
        $this->compras = Compras::all();
    }

    ////////////////////////////// INDEX //////////////////////////////////
    public function index(Request $request)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de consulta en el objeto GASTO
        $permisoConsulta = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'GASTO')
                    ->limit(1);
            })
            ->where('Permiso_Consultar', 'PERMITIDO')
            ->exists();

        if (!$permisoConsulta) {
            $this->bitacora->registrarEnBitacora(14, 'Intento de ingreso a la ventana de libro Diario sin permisos', 'Ingreso');
            return redirect()->route('dashboard')->withErrors('No tiene permiso para consultar los reportes del libro Diario');
        }

        // Obtener los gastos con la relación a la compra
        $gastos = Gastos::with('compra')->get();
        // Obtener todas las compras y proyectos para la vista
        $compras = Compras::all();
        $proyectos = Proyectos::all();
        // Calcular el total de los gastos
        $totalGastos = Gastos::sum('SUBTOTAL');
        
        $this->bitacora->registrarEnBitacora(14, 'Ingreso a la ventana de libro Diario', 'Ingreso');

        // Pasar los datos a la vista
        return view('gastos.index', compact('compras', 'gastos', 'totalGastos', 'proyectos'));
    }

    ///////////////////////////////////// REPORTE GENERAL ////////////////////////////////////
    public function pdf(Request $request)
    {   

        
        //Validar que existen reportes
        $validator = Validator::make($request->all(), [
            'validacion' => [(new Validaciones)->validarExistenciaDeReportes()],
        ]);

        if ($validator->fails()) {
            $this->bitacora->registrarEnBitacora(14, 'Error al generar el reporte por  en Libro Diario', 'Reporte');
            return redirect()
                ->route('gastos.index')
                ->withErrors($validator)
                ->withInput()
                ->with('modal', $request->input('reporteTipo')); // Indica qué modal debe abrirse
        }

        // Obtener la fecha y hora actual
        $fechaHora = Carbon::now()->format('d-m-Y H:i:s');

        // Obtener todos los gastos
        $gastos = Gastos::all();
        
        // Cambio de img a formato pdf
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    
        // Calcular el total general de los gastos
        $totalGastos = $gastos->sum('SUBTOTAL');

        // Generar el PDF
        $pdf = Pdf::loadView('gastos.pdf', compact('gastos', 'fechaHora', 'logoBase64', 'totalGastos'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);
        $this->bitacora->registrarEnBitacora(14, 'Generación de reporte general de Libro Diario', 'Reporte ');
        return $pdf->stream();
    }

   /* public function destroy($COD_COMPRA)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de eliminación en el objeto GASTO
        $permisoEliminar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'GASTO')
                    ->limit(1);
            })
            ->where('Permiso_Eliminar', 'PERMITIDO')
            ->exists();

        if (!$permisoEliminar) {
            return redirect()->route('gastos.index')->withErrors('No tiene permiso para eliminar gastos');
        }

        try {
            // Elimina el registro de gastos asociado a la compra
            Gastos::where('COD_COMPRA', $COD_COMPRA)->delete();

            return redirect()->route('gastos.index')->with('success', 'Gasto eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('gastos.index')->with('error', 'Error al eliminar gasto: ' . $e->getMessage());
        }
    }*/


///////////////////////////// REPORTE PROYECTO //////////////////////////////////////
public function reportePorProyecto(Request $request)
{   
    
    $proyecto = $request->input('proyecto');
    
    // Validar si se seleccionó un proyecto
    $validator = Validator::make($request->all(), [
        'proyecto' => [(new Validaciones)->requerirCampo(), 'exists:tbl_proyectos,COD_PROYECTO'],
    ]);

    if ($validator->fails() ) {
        $this->bitacora->registrarEnBitacora(14, 'Error al generar reporte por proyecto en Libro Diario', 'Reporte');
        return redirect()
            ->route('gastos.index')
            ->withErrors($validator)
            ->withInput()
            ->with('modal', $request->input('reporteTipo')) // Indica qué modal debe abrirse
            ->with('forceSameTab', true);
    }

    $gastos = Gastos::where('COD_PROYECTO', $proyecto)->get();

    if ($gastos->isEmpty()) {
        // Registrar el intento fallido en la bitácora
        $this->bitacora->registrarEnBitacora(14, 'Error al generar reporte por proyecto en Libro Diario', 'Reporte');
        // Redirigir al modal de proyecto con un mensaje de error
        return redirect()
            ->route('gastos.index')
            ->withErrors('No existen registros proyecto proporcionados.')
            ->withInput()
            ->with('modal', $request->input('reporteTipo'))  // Indicador para reabrir el modal de proyecto
            ->with('forceSameTab', true);
    }

    

    return redirect()->route('gastos.descargar.reporte.proyecto', compact('proyecto'));
}

public function descargarReportePorProyecto(Request $request)
{
    $proyecto = $request->query('proyecto');

    $gastos = Gastos::where('COD_PROYECTO', $proyecto)->get();

    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
    $totalGastos = $gastos->sum('SUBTOTAL');

    // Cambio de img a formato pdf
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    // Generar el PDF
    $pdf = Pdf::loadView('gastos.reporte_proyecto', compact('gastos', 'fechaHora', 'logoBase64', 'totalGastos'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);
    $this->bitacora->registrarEnBitacora(14, 'Generación de reporte por proyecto en Libro Diario', 'Reporte');  
    return $pdf->stream('reporte_gastos_por_proyecto.pdf');
}


/////////////////////////////////// REPORTE FECHA ///////////////////////////////////////
public function reportePorFecha(Request $request)
{

    $fecha = $request->input('fecha');
    // validaciones //
    $validator = Validator::make($request->all(), [
        'fecha' => [
            (new Validaciones)
                ->requerirCampo()
                ->validarFechaConRegistros()
        ],
    ]);

    if ($validator->fails()) {
        $this->bitacora->registrarEnBitacora(14, 'Error al generar reporte por fecha en Libro Diario', 'Reporte');
        return redirect()
            ->route('gastos.index')
            ->withErrors($validator)
            ->withInput()
            ->with('modal', $request->input('reporteTipo')) // Indica qué modal debe abrirse
            ->with('forceSameTab', true);
    }

    $fecha = $request->input('fecha');

    // Verificar si hay registros de gastos para la fecha seleccionada
    $gastos = Gastos::whereDate('FEC_REGISTRO', $fecha)->get();

    if ($gastos->isEmpty()) {
        // No hay registros para esta fecha
        session()->flash('forceSameTab', true);
        session()->flash('modal', 'fecha');
        $this->bitacora->registrarEnBitacora(14, 'No existen registros generados para la fecha proporcionada en Libro Diario', 'Reporte');
        
        return redirect()
            ->route('gastos.index')
            ->withErrors(['fecha' => 'No existen registros generados para la fecha proporcionada.'])
            ->withInput()
            ->with('modal', 'fecha')
            ->with('forceSameTab', true);
    }

    return redirect()->route('gastos.descargar.reporte.fecha', compact('fecha'));
}

public function descargarReportePorFecha(Request $request)
{
    $fecha = $request->query('fecha');

    $gastos = Gastos::whereDate('FEC_REGISTRO', $fecha)->get();
    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
    $totalGastos = $gastos->sum('SUBTOTAL');

    // Cambio de img a formato pdf
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    // Generar el PDF
    $pdf = Pdf::loadView('gastos.reporte_fecha', compact('gastos', 'fechaHora', 'logoBase64', 'totalGastos', 'fecha'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);
    $this->bitacora->registrarEnBitacora(14, 'Generación de reporte por fecha en Libro Diario', 'Reporte');  
    return $pdf->stream('reporte_gastos_por_fecha.pdf');
}
////////////////////////////// REPORTE DIA ACTUAL //////////////////////////////////////////
public function reportePorHoy(Request $request)
{

    $hoy = $request->input('hoy');
    $validator = Validator::make($request->all(), [
        'validacion' => [(new Validaciones)->validarRegistrosHoy($hoy)],
    ]);

    if ($validator->fails()) {
        return redirect()
            ->route('gastos.index')
            ->withErrors($validator)
            ->withInput()
            ->with('modal', 'hoy'); // Reabre el modal correspondiente
    }

    $hoy = Carbon::today(); // Obtener la fecha de hoy

    // Verificar si existen registros de gastos para la fecha de hoy
    $existenGastos = Gastos::whereDate('FEC_REGISTRO', $hoy)->exists();

    // Si no existen gastos, lanza un error
    if (!$existenGastos) {
        return redirect()
            ->route('gastos.index')
            ->withErrors('No existen reportes generados para el día de hoy.')
            ->withInput()
            ->with('modal', 'hoy'); // Reabre el modal correspondiente
    }

    // Si pasa la validación, redirige para descargar el reporte
    return redirect()->route('gastos.descargar.reporte.hoy');
}


public function descargarReportePorHoy()
{
    $hoy = Carbon::today();

    $gastos = Gastos::whereDate('FEC_REGISTRO', $hoy)->get();
    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
    $totalGastos = $gastos->sum('SUBTOTAL');
    // Cambio de img a formato pdf
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    // Generar el PDF
    $pdf = Pdf::loadView('gastos.reporte_hoy', compact('gastos', 'fechaHora', 'logoBase64', 'totalGastos'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);
    $this->bitacora->registrarEnBitacora(14, 'Generación del reporte del día en Libro Diario', 'Reporte');  
    return $pdf->stream('reporte_gastos_por_hoy.pdf');
}

/*public function reportePorAno()
{
    return redirect()->route('gastos.descargar.reporte.ano');
}*/


/////////////////////////////////// REPORTE AÑO ///////////////////////////////////////
public function reportePorAno(Request $request)
{
    

    $anio = $request->input('anio');
    $validator = Validator::make($request->all(), [
        'validacion' => [(new Validaciones)->requerirCampo()],
    ]);

    if ($validator->fails()) {
        $this->bitacora->registrarEnBitacora(14, 'Error al generar el reporte por año en Libro Diario', 'Reporte');
        return redirect()
            ->route('gastos.index')
            ->withErrors($validator)
            ->withInput()
            ->with('modal', $request->input('reporteTipo')); 
    }

    $anio = $request->input('anio');
 
    return redirect()->route('gastos.descargar.reporte.ano', ['anio' => $anio]);
}

public function descargarReportePorAno(Request $request)
{

    $anio = $request->query('anio');

    $gastos = Gastos::whereYear('FEC_REGISTRO', $anio)->get();


    foreach ($gastos as $gasto) {
        $gasto->FEC_REGISTRO = Carbon::parse($gasto->FEC_REGISTRO);
    }

    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
    $totalGastos = $gastos->sum('SUBTOTAL');

    // Cambio de img a formato pdf
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    // Generar el PDF
    $pdf = Pdf::loadView('gastos.reporte_ano', compact('gastos', 'fechaHora', 'logoBase64', 'totalGastos', 'anio'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);
    $this->bitacora->registrarEnBitacora(14, 'Generación del reporte por año en Libro Diario', 'Reporte');  
    return $pdf->stream('gastos.reporte.ano.pdf');
}

////////////////////////////////////// REPORTE MES ///////////////////////////////////////
public function descargarReportePorMes(Request $request)
{
    $mes = $request->input('mes');
    $anio = $request->input('anio');

    // Validar los campos 'mes' y 'anio' utilizando la clase de validaciones personalizada
    $validator = Validator::make($request->all(), [
        'mes' => [
            (new Validaciones)
                ->requerirCampo()
                ->requerirSoloNumeros()
                ->requerirSinEspacios()
                ->validarMesConRegistros($mes,$anio)
        ],
        'anio' => [
            (new Validaciones)
                ->requerirCampo()
                ->requerirSoloNumeros()
                ->requerirSinEspacios()
                ->validarMesConRegistros($mes,$anio)
        ],
    ]);

    if ($validator->fails()) {
        $this->bitacora->registrarEnBitacora(14, 'Error al generar el reporte por mes en Libro Diario', 'Reporte');
        return redirect()
            ->route('gastos.index')
            ->withErrors($validator)
            ->withInput()
            ->with('modal', $request->input('reporteTipo')); // Indica qué modal debe abrirse
    }

    $mes = $request->query('mes');
    $anio = $request->query('anio');

    $gastos = Gastos::whereYear('FEC_REGISTRO', $anio)
                    ->whereMonth('FEC_REGISTRO', $mes)
                    ->get()
                    ->each(function($gasto) {
                        $gasto->FEC_REGISTRO = Carbon::parse($gasto->FEC_REGISTRO);
                    });


    if ($gastos->isEmpty()) {
        // Registrar el intento fallido en la bitácora
        $this->bitacora->registrarEnBitacora(14, 'Error al generar el reporte por año en Libro Diario', 'Reporte');
       
        // Redirigir al modal de proyecto con un mensaje de error
        return redirect()
            ->route('gastos.index')
            ->withErrors('No existen registros para el mes y año proporcionados.')
            ->withInput()
            ->with('modal', $request->input('reporteTipo'));  // Indicador para reabrir el modal de proyecto
    }
    
    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
    $totalGastos = $gastos->sum('SUBTOTAL');

    // Cambio de img a formato pdf
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    // Generar el PDF
    $pdf = Pdf::loadView('gastos.reporte_mes', compact('gastos', 'fechaHora', 'logoBase64', 'totalGastos', 'mes', 'anio'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);
    $this->bitacora->registrarEnBitacora(14, 'Generación del reporte por mes en Libro Diario', 'Reporte');  
    return $pdf->stream('gastos.reporte.mes.pdf');
}
    
}
