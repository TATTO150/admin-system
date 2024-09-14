<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EmpleadoPlanilla;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Rules\Validaciones;
use App\Models\empleados;
use App\Models\Planillas;
use Barryvdh\DomPDF\Facade\Pdf;

class EmpleadosPlanillascontrolador extends Controller
{
    protected $empleados;
    protected $planillas;
    public function __construct()
    {
       
        $this->empleados = empleados::all();
        $this->planillas = Planillas::all();
       

    }
    public function index()
    {
        $response = Http::get('http://127.0.0.1:3000/empleados_planillas');
        $empleado_planillas = EmpleadoPlanilla::with(['empleado', 'planilla'])->get();
        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s'); 
        return view('empleado_planillas.index', compact('empleado_planillas','fechaHora'));
    }
    public function pdf(){
        $empleado_planillas=EmpleadoPlanilla::all();
        $empleados = empleados::all();
        $planillas = Planillas::all();
        //fecha y hora
        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
        //cambio de img a formato pdf
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
        //paginacion
        $pdf = Pdf::loadView('empleado_planillas.pdf', compact('empleado_planillas', 'proyectos', 'fechaHora','logoBase64'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
        ]);


        $pdf = Pdf::loadView('empleado_planillas.pdf', compact('empleado_planillas','empleados', 'planillas'));
        return $pdf->stream();
    }

    public function crear()
    {
          return view('empleado_planillas.crear', [
            'empleados' => $this->empleados,
            'planillas' => $this->planillas,
        ]);
    }

    public function insertar(Request $request)
{
    // Validación de los datos del formulario
    $validator = Validator::make($request->all(), [
        'COD_EMPLEADO' => [(new Validaciones)->requerirCampo()],
        'COD_PLANILLA' => [(new Validaciones)->requerirCampo()],
        'SALARIO_BASE' => [(new Validaciones)->requerirCampo()->requerirValorMinimo(1)],
        'DEDUCCIONES' => [(new Validaciones)->requerirCampo()->requerirSoloNumeros()], // Ahora esperamos un porcentaje entero
    ]);

    // Si la validación falla, redirige de vuelta con los errores
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Calcular el salario neto
    $salarioBase = $request->SALARIO_BASE;
    $deduccionesPorcentaje = $request->DEDUCCIONES;

    // Calcular el monto de las deducciones basado en el porcentaje
    $montoDeducciones = ($deduccionesPorcentaje / 100) * $salarioBase;
    $salarioNeto = $salarioBase - $montoDeducciones;

    // Insertar el empleado en la planilla
    $response = Http::post('http://127.0.0.1:3000/INS_EMPLEADO_PLANILLA', [
        'COD_EMPLEADO' => $request->COD_EMPLEADO,
        'COD_PLANILLA' => $request->COD_PLANILLA,
        'SALARIO_BASE' => $salarioBase,
        'DEDUCCIONES' => $montoDeducciones, // Guardar porcentaje
        'SALARIO_NETO' => $salarioNeto
    ]);

    // Verificar si la inserción fue exitosa
    if ($response->successful()) {
        // Actualizar el total a pagar de la planilla
        $planilla = Planillas::find($request->COD_PLANILLA);

        if ($planilla) {
            // Sumar el salario neto al total a pagar
            $planilla->TOTAL_PAGAR += $salarioNeto;
            $planilla->save();
        }
    }

    // Redirigir a la vista de gestión de planillas con el COD_PLANILLA
    return redirect()->route('pagoPlanillas.gestionar', ['COD_PLANILLA' => $request->COD_PLANILLA]);
}

    
public function destroy($COD_EMPLEADO_PLANILLA)
{
    try {
        // Obtener el empleado de planilla antes de eliminar
        $empleadoPlanilla = EmpleadoPlanilla::findOrFail($COD_EMPLEADO_PLANILLA);

        // Restar el salario neto del total a pagar en la planilla
        $planilla = Planillas::find($empleadoPlanilla->COD_PLANILLA);
        if ($planilla) {
            $planilla->TOTAL_PAGAR -= $empleadoPlanilla->SALARIO_NETO;
            $planilla->save();
        }

        // Eliminar el empleado de la planilla
        DB::statement('CALL ELI_EMPLEADO_PLANILLA(?)', [$COD_EMPLEADO_PLANILLA]);

        // Redirigir a la vista de gestión de planillas con el COD_PLANILLA
        return redirect()->route('pagoPlanillas.gestionar', ['COD_PLANILLA' => $empleadoPlanilla->COD_PLANILLA])
                         ->with('success', 'Empleado eliminado correctamente');
    } catch (\Exception $e) {
        // Asegurarse de que el COD_PLANILLA está definido en caso de error
        if (isset($empleadoPlanilla->COD_PLANILLA)) {
            return redirect()->route('pagoPlanillas.gestionar', ['COD_PLANILLA' => $empleadoPlanilla->COD_PLANILLA])
                             ->with('error', 'Error al eliminar empleado');
        } else {
            return redirect()->route('pagoPlanillas.gestionar', ['COD_PLANILLA' => $empleadoPlanilla->COD_PLANILLA])
                             ->with('error', 'Error al eliminar empleado, planilla no encontrada');
        }
    }
}




    public function edit($COD_EMPLEADO_PLANILLA)
    {
        $response = Http::get("http://localhost:3000/empleados_planillas/{$COD_EMPLEADO_PLANILLA}");
        $empleado_planillas = $response->json();
    
        if (!isset($empleado_planillas['COD_EMPLEADO_PLANILLA'])) {
            dd('COD_EMPLEADO_PLANILLA no está definido en la respuesta de la API', $empleado_planillas);
        }
        $empleados = $this->empleados;
        $planillas = $this->planillas;
        return view('empleado_planillas.edit', compact('empleado_planillas','empleados','planillas'));
    }

    public function update(Request $request, $COD_EMPLEADO_PLANILLA)
{
    // Validación de los datos del formulario
    $validator = Validator::make($request->all(), [
        'COD_EMPLEADO' => [(new Validaciones)->requerirCampo()],
        'COD_PLANILLA' => [(new Validaciones)->requerirCampo()],
        'SALARIO_BASE' => [(new Validaciones)->requerirCampo()->requerirValorMinimo(1)],
        'DEDUCCIONES' => [(new Validaciones)->requerirCampo()->requerirSoloNumeros()],
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Obtener los datos del empleado en la planilla antes de la actualización
    $response = Http::get("http://127.0.0.1:3000/empleados_planillas/{$COD_EMPLEADO_PLANILLA}");
    $empleadoPlanillaPrev = $response->json();

    if (!isset($empleadoPlanillaPrev['SALARIO_NETO'])) {
        return redirect()->back()->with('error', 'No se pudo obtener el salario neto previo');
    }

    // Calcular el nuevo salario neto
    $salarioBase = $request->SALARIO_BASE;
    $deduccionesPorcentaje = $request->DEDUCCIONES;
    $montoDeducciones = ($deduccionesPorcentaje / 100) * $salarioBase;
    $salarioNeto = $salarioBase - $montoDeducciones;

    // Actualizar el empleado en la planilla
    $response = Http::put("http://127.0.0.1:3000/empleados_planillas/{$COD_EMPLEADO_PLANILLA}", [
        'COD_EMPLEADO' => $request->COD_EMPLEADO,
        'COD_PLANILLA' => $request->COD_PLANILLA,
        'SALARIO_BASE' => $salarioBase,
        'DEDUCCIONES' => $montoDeducciones,
        'SALARIO_NETO' => $salarioNeto
    ]);

    if ($response->successful()) {
        // Restar el salario neto previo del total a pagar
        $planilla = Planillas::find($request->COD_PLANILLA);
        if ($planilla) {
            $planilla->TOTAL_PAGAR -= $empleadoPlanillaPrev['SALARIO_NETO'];
            // Sumar el nuevo salario neto al total a pagar
            $planilla->TOTAL_PAGAR += $salarioNeto;
            $planilla->save();
        }
    }

    return redirect()->route('pagoPlanillas.gestionar', ['COD_PLANILLA' => $request->COD_PLANILLA]);
}

}
