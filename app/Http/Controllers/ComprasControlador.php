<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BitacoraController;
use App\Models\Compras;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Rules\Validaciones;
use Carbon\Carbon;
use App\Models\Proyectos;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Gastos;
use App\Models\Permisos;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Dompdf\Options;

class ComprasControlador extends Controller
{
    protected $proyectos;
 /*   protected $bitacora;

    public function __construct(BitacoraController $bitacora)
    {
        $this->bitacora = $bitacora;
    }*/

    public function index()
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de lectura en el objeto COMPRA
        $permisoLeer = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'COMPRA')
                    ->limit(1);
            })
            ->where('Permiso_Consultar', 'PERMITIDO')
            ->exists();

        if (!$permisoLeer) {
            return redirect()->route('dashboard')->withErrors('No tiene permiso para acceder a la ventana de compras');
        }

        $response = Http::get('http://127.0.0.1:3000/compras');
        $compras = $response->json();

        // Formatear las fechas
        foreach ($compras as &$compra) {
            if (!empty($compra['FEC_REGISTRO'])) {
                $compra['FEC_REGISTRO'] = Carbon::parse($compra['FEC_REGISTRO'])->format('Y-m-d');
            } else {
                $compra['FEC_REGISTRO'] = 'Fecha no disponible';
            }
        }

        $proyectos = \App\Models\Proyectos::all()->keyBy('COD_PROYECTO');

        return view('compras.index', compact('compras', 'proyectos'));
    }

    
    public function pdf()
    {
        $compras = Compras::all();
        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
        $proyectos = \App\Models\Proyectos::all()->keyBy('COD_PROYECTO');
        //cambio de img a formato pdf
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    
        //paginacion
        $pdf = Pdf::loadView('compras.pdf', compact('compras', 'proyectos', 'fechaHora','logoBase64'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
        ]);
    
        return $pdf->stream('reporte_compras.pdf');
    }
    
    public function __construct(){

        $this->proyectos = proyectos::all();
    }

    public function crear()
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de inserción en el objeto COMPRA
        $permisoInsercion = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'COMPRA')
                    ->limit(1);
            })
            ->where('Permiso_Insercion', 'PERMITIDO')
            ->exists();

        if (!$permisoInsercion) {
            return redirect()->route('compras.index')->withErrors('No tiene permiso para crear una compra');
        }

        // Obtener la lista de proyectos
        $proyectos = $this->proyectos;

        return view('compras.crear', compact('proyectos'));
    }

    public function insertar(Request $request)
{
    // Validar los datos del formulario
    $validator = Validator::make($request->all(), [
        'DESC_COMPRA' => [(new Validaciones)->requerirCampo()->prohibirNumerosSimbolos()->requerirTodoMayusculas()],
        'COD_PROYECTO' => [(new Validaciones)->requerirCampo()->requerirSoloNumeros()->requerirSinEspacios()->requerirEstadoValidoProyecto()],
        'FEC_REGISTRO' => [(new Validaciones)->requerirCampo()],
        'PRECIO_VALOR' => [(new Validaciones)->requerirCampo()->requerirSoloNumeros()->requerirValorMinimo(1)],
        //'CANTIDAD' => [(new Validaciones)->requerirCampo()->requerirSoloNumeros()->requerirValorMinimo(1)],   
        'SUBTOTAL' => [(new Validaciones)->requerirCampo()->requerirSoloNumeros()->requerirValorMinimo(1)],
        'TOTAL' => [(new Validaciones)->requerirCampo()->requerirSoloNumeros()->requerirValorMinimo(1)],
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    DB::transaction(function () use ($request) {
        // Calcula el subtotal
        $subtotal =$request->input('PRECIO_VALOR');
        
        // Inserta la compra
        $compra = Compras::create([
            
            'DESC_COMPRA' => $request->input('DESC_COMPRA'),
            'COD_PROYECTO' => $request->input('COD_PROYECTO'),
            'FEC_REGISTRO' => Carbon::now(),
            'TIP_COMPRA' => $request->input('TIP_COMPRA'), 
            'PRECIO_VALOR' => $request->input('PRECIO_VALOR'),
           // 'CANTIDAD' => $request->input('CANTIDAD'), 
            // Otros campos necesarios
        ]);
    
        // Inserta el gasto asociado
        Gastos::create([
            'COD_COMPRA' => $compra->COD_COMPRA,  // Usar el COD_COMPRA recién creado
            'COD_PROYECTO' => $request->input('COD_PROYECTO'),
            'FEC_REGISTRO' => Carbon::now(),
            'SUBTOTAL' => $subtotal,
            'TOTAL' => $subtotal,
            // Otros campos necesarios
        ]);
    
        // Actualiza el TOTAL acumulado en la tabla de gastos para esta compra
        $totalAcumulado = Gastos::where('COD_COMPRA', $compra->COD_COMPRA)
            ->sum('SUBTOTAL');
    
        Gastos::where('COD_COMPRA', $compra->COD_COMPRA)
            ->update(['TOTAL' => $totalAcumulado]);
    });
    
    return redirect()->route('gastos.index')->with('success', 'Compra y gasto creados exitosamente');
}


    public function destroy($COD_COMPRA)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de eliminación en el objeto COMPRA
        $permisoEliminacion = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'COMPRA')
                    ->limit(1);
            })
            ->where('Permiso_Eliminacion', 'PERMITIDO')
            ->exists();

        if (!$permisoEliminacion) {
            return redirect()->route('gastos.index')->withErrors('No tiene permiso para eliminar esta compra');
        }

        try {
            DB::transaction(function() use ($COD_COMPRA) {
                // Elimina los registros de gastos asociados
                Gastos::where('COD_COMPRA', $COD_COMPRA)->delete();
                
                // Elimina la compra
                Compras::where('COD_COMPRA', $COD_COMPRA)->delete();
            });

            return redirect()->route('gastos.index')->with('success', 'Registro eliminados correctamente');
        } catch (\Exception $e) {
            return redirect()->route('gastos.index')->with('error', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }



    public function edit($COD_COMPRA)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de actualización en el objeto COMPRA
        $permisoActualizacion = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'COMPRA')
                    ->limit(1);
            })
            ->where('Permiso_Actualizacion', 'PERMITIDO')
            ->exists();

        if (!$permisoActualizacion) {
            return redirect()->route('compras.index')->withErrors('No tiene permiso para editar esta compra');
        }

        $response = Http::get("http://localhost:3000/compras/{$COD_COMPRA}");
        $compras = $response->json();

        if (!isset($compras['COD_COMPRA'])) {
            dd('COD_COMPRA no está definido en la respuesta de la API', $compras);
        }

        // Obtener la lista de proyectos
        $proyectos = $this->proyectos;

        return view('compras.edit', [
            'compras' => $compras,
            'proyectos' => $proyectos
        ]);
    }


    public function update(Request $request, $COD_COMPRA)
{
    $validator = Validator::make($request->all(), [
        'DESC_COMPRA' => [(new Validaciones)->requerirCampo()->prohibirNumerosSimbolos()->requerirTodoMayusculas()],
        'COD_PROYECTO' => [(new Validaciones)->requerirCampo()->requerirSoloNumeros()->requerirEstadoValidoProyecto()],
        'FEC_REGISTRO' => [(new Validaciones)->requerirCampo()],
        'TIP_COMPRA' => [(new Validaciones)->requerirCampo()->prohibirNumerosSimbolos()->requerirSinEspacios()->requerirTodoMayusculas()],
        'PRECIO_VALOR' => [(new Validaciones)->requerirCampo()->requerirSoloNumeros()->requerirValorMinimo(1)],
       // 'CANTIDAD' => [(new Validaciones)->requerirCampo()->requerirSoloNumeros()->requerirValorMinimo(1)],
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    DB::transaction(function () use ($request, $COD_COMPRA) {
        // Encuentra la compra
        $compra = Compras::find($COD_COMPRA);
        if (!$compra) {
            throw new \Exception('Registro no encontrada');
        }

        // Actualiza la compra
        $compra->DESC_COMPRA = $request->DESC_COMPRA;
        $compra->COD_PROYECTO = $request->COD_PROYECTO;
        $compra->FEC_REGISTRO = Carbon::now();
        $compra->TIP_COMPRA = $request->TIP_COMPRA;
        $compra->PRECIO_VALOR = $request->PRECIO_VALOR;
       // $compra->CANTIDAD = $request->CANTIDAD;
        $compra->save();

        // Calcula el subtotal
        $subtotal =  $request->input('PRECIO_VALOR');

        // Actualiza el gasto asociado
        $gasto = Gastos::where('COD_COMPRA', $COD_COMPRA)->first();
        if ($gasto) {
            $gasto->COD_PROYECTO = $compra->COD_PROYECTO;
            $gasto->FEC_REGISTRO = Carbon::now();
            $gasto->SUBTOTAL = $subtotal;
            $gasto->TOTAL = $subtotal;
            $gasto->save();
        } else {
            Gastos::create([
                'COD_COMPRA' => $COD_COMPRA,
                'COD_PROYECTO' => $compra->COD_PROYECTO,
                'FEC_REGISTRO' => Carbon::now(),
                'SUBTOTAL' => $subtotal,
                'TOTAL' => $subtotal,
            ]);
        }
    });

    return redirect()->route('gastos.index')->with('success', 'Registro actualizado exitosamente');
}

    


    
    public function reporteDiario()
    {
        // Obtener la fecha actual
        $hoy = Carbon::today();
        
        // Obtener todas las compras realizadas el día de hoy
        $comprasHoy = Compras::whereDate('FEC_REGISTRO', $hoy)->get();
        
        // Calcular el gasto total del día
        $totalGastadoHoy = $comprasHoy->sum(function ($compra) {
            return $compra->gastos->sum('TOTAL');
        });
    
        // Obtener los proyectos
        $proyectos = Proyectos::all()->keyBy('COD_PROYECTO');
        
        // Convertir FEC_REGISTRO a instancia de Carbon en cada compra
        $comprasHoy->each(function($compra) {
            $compra->FEC_REGISTRO = Carbon::parse($compra->FEC_REGISTRO);
        });
    
        // Generar el PDF
        $pdf = Pdf::loadView('compras.reporte_diario', compact('comprasHoy', 'totalGastadoHoy', 'proyectos', 'hoy'));
        return $pdf->stream();
    }
    public function reporteMensual()
{
    // Obtener el primer y último día del mes actual
    $primerDiaMes = Carbon::now()->startOfMonth();
    $ultimoDiaMes = Carbon::now()->endOfMonth();
    
    // Obtener todas las compras realizadas en el mes actual
    $comprasMes = Compras::whereBetween('FEC_REGISTRO', [$primerDiaMes, $ultimoDiaMes])->get();
    
    // Calcular el gasto total del mes
    $totalGastadoMes = $comprasMes->sum(function ($compra) {
        return $compra->gastos->sum('TOTAL');
    });

    // Obtener los proyectos
    $proyectos = Proyectos::all()->keyBy('COD_PROYECTO');
    
    // Convertir FEC_REGISTRO a instancia de Carbon en cada compra
    $comprasMes->each(function($compra) {
        $compra->FEC_REGISTRO = Carbon::parse($compra->FEC_REGISTRO);
    });

    // Generar el PDF
    $pdf = Pdf::loadView('compras.reporte_mensual', compact('comprasMes', 'totalGastadoMes', 'proyectos', 'primerDiaMes', 'ultimoDiaMes'));
    return $pdf->stream();
}

}

