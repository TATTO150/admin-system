<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Deduccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BitacoraController;
use App\Models\Compras;
use App\Models\EstadoCompra;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Rules\Validaciones;
use Carbon\Carbon;
use App\Models\Proyectos;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Permisos;
use App\Models\TipoCompra;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Providers\PermisoService;

class ComprasControlador extends Controller
{

    protected $estadocompras, $tipocompras, 
                $permisoService, $bitacora, $proyectos, $usuarios, $deduccion, $preciofinal;

    
    public function __construct(BitacoraController $bitacora, PermisoService $permisoService)
    {
        $this->bitacora = $bitacora;
        $this->permisoService = $permisoService;  // Inyectar el PermisoService
        
    }

    public function index($id = null)
    {
        $user = Auth::user();
        
        $roleId = $user->Id_Rol;
        $fechaActual = Carbon::now();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }
        //validacion de permisos
        $this->permisoService->tienePermiso('COMPRA','Consultar',true);

        // Obtener el ID del estado "aprobada"
        $estadoAprobado = EstadoCompra::where('DESC_ESTADO', 'APROBADA')->first()->COD_ESTADO;
       
        //COMPRAS APROBADAS Y LIQUIDADAS
        $compras = Compras::Where('COD_ESTADO',$estadoAprobado)
        ->where('LIQUIDEZ_COMPRA',1)
        ->with(['estadocompras', 'usuarios', 'proyectos', 'deduccion', 'tipocompras'])
        ->get();
        //compras sin liquidar y en fecha
        $Liquidaciones = Compras::Where('LIQUIDEZ_COMPRA',0)
        ->where('COD_ESTADO',$estadoAprobado)
        ->where('FECHA_PAGO', '>', $fechaActual)
        ->with(['estadocompras', 'usuarios', 'proyectos', 'tipocompras'])
        ->get();
        //compras sin liquidar retrasadas
        $retraso = Compras::Where('LIQUIDEZ_COMPRA',0)
        ->where('COD_ESTADO',$estadoAprobado)
        ->where('FECHA_PAGO', '<', $fechaActual)
        ->with(['estadocompras', 'usuarios', 'proyectos', 'tipocompras'])
        ->get();

        // Formatear las fechas
        foreach ($compras as &$compra) {
            $compra->totalDeducciones = Deduccion::where('COD_COMPRA', $compra->COD_COMPRA)->sum('VALOR_DEDUCCION');
           
            $compra->precioFinal = $compra->PRECIO_COMPRA - $compra->totalDeducciones;
           
            if (!empty($compra['FEC_REGISTRO'])) {
                $compra['FEC_REGISTRO'] = Carbon::parse($compra['FEC_REGISTRO'])->format('Y-m-d');
            } else {
                $compra['FEC_REGISTRO'] = 'Fecha no disponible';
            }
        }
        //llamada a los modulos para obtener la informacion de las tablas
        $proyectos = Proyectos::all()->keyBy('COD_PROYECTO');
        $usuarios = User::all()->keyBy('Id_usuario');
        $estadocompras = EstadoCompra::all()->keyBy('COD_ESTADO');
        $tipocompras = TipoCompra::all()->KeyBy('COD_TIPO');
        $deduccion = Deduccion::all()->KeyBy('COD_COMPRA');
        $preciofinal = '0.00';
        //retorno de la informacion
        return view('compras.index', compact('retraso','Liquidaciones','preciofinal','usuarios','compras','proyectos','estadocompras','tipocompras', 'deduccion'));
    }

    public function agregarDeduccion(Request $request, $id)
    {
        // Validar la entrada
        $validated = $request->validate([
            'valor_deduccion' => 'numeric|min:0',
            'tipo_deduccion' => 'required|in:numerico,porcentaje',
            'descripcion_deduccion' => 'nullable|string|max:255',
        ]);
        
        
        // Buscar la compra y agregar deducción
        $compra = Compras::findOrFail($id);
       
       
        // Determinar si la deducción es un valor numérico o porcentaje
        if ($validated['tipo_deduccion'] === 'porcentaje') {
            // Si es porcentaje, convertirlo a valor numérico
            $valorNuevaDeduccion = ($validated['valor_deduccion'] / 100) * $compra->PRECIO_COMPRA;
        } else {
            // Si es numérico, tomar el valor directamente
            $valorNuevaDeduccion = $validated['valor_deduccion'];
        }
        
        $sumaDeduccionesExistentes = Deduccion::where('COD_COMPRA', $compra->COD_COMPRA)
        ->sum('VALOR_DEDUCCION');

            // Sumar la nueva deducción con la suma existente
        $totalDeducciones = $sumaDeduccionesExistentes + $valorNuevaDeduccion;

        if ($totalDeducciones > $compra->PRECIO_COMPRA) {
            // Redirigir con mensaje de error si el total de deducciones excede el precio de compra
            return redirect()->back()->withErrors(['error' => 'Las deducciones no puede exceder el precio de la compra.']);
        }
        
         // Guardar la deducción en la tabla correspondiente (Deduccion)
         Deduccion::create([
            'COD_COMPRA' => $compra->COD_COMPRA,
            'VALOR_DEDUCCION' => $valorNuevaDeduccion,
            'DESC_DEDUCCION' => $validated['descripcion_deduccion'],
        ]);

        return redirect()->back()->with('success', 'Deducción agregada correctamente. El total de deducciones es: ' . number_format($totalDeducciones, 2));
    }

    public function liquidarCompras(Request $request)
    {
        // Decodificar los IDs de las compras seleccionadas
        $comprasSeleccionadas = json_decode($request->compras_seleccionadas, true);
    
        foreach ($comprasSeleccionadas as $compraId) {
            // Encontrar la compra por COD_COMPRA
            $compra = Compras::where('COD_COMPRA', $compraId)->first();
            
            if ($compra && $compra->TOTAL_CUOTAS >= 1) {
                // Incrementar CUOTAS_PAGADAS y calcular PRECIO_NETO
                $compra->CUOTAS_PAGADAS += 1;
                $compra->PRECIO_NETO = $compra->PRECIO_COMPRA - $compra->PRECIO_CUOTA;
    
                // Verificar si se ha completado el total de cuotas
                if ($compra->CUOTAS_PAGADAS >= $compra->TOTAL_CUOTAS) {
                    $compra->LIQUIDEZ_COMPRA = 1;
                }
    
                // Guardar los cambios
                $compra->save();
            }
        }
    
        return redirect()->route('compras.liquidar')->with('success', 'Compras liquidadas correctamente.');
    }
    


}

