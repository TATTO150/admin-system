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
$palabrasSoeces = ['conchatumadre', 'prostituta', 'pene', 'culo', 
'estupido', 'idiota', 'invecil', 'imbécil', 'tonto', 'inutil', 'malnacido',
'baboso', 'payaso', 'demonio', 'muerte', 'violacion', 'pudrete', 'basura',
'asqueroso', 'bastardo', 'puta','despreciable','pendejo','bastardo','maldito']; // Reemplaza con palabras inapropiadas específicas

// Convertir las palabras prohibidas en una expresión regular
$regexPalabrasSoeces = implode('|', array_map('preg_quote', $palabrasSoeces));

// Validar la entrada con reglas avanzadas
$validated = $request->validate([
'valor_deduccion' => 'required|numeric|min:0',
'tipo_deduccion' => 'required|in:numerico,porcentaje',
'descripcion_deduccion' => [
    'required',
    'nullable',
    'string',
    'max:255',
    
    // Validación: no permite palabras soeces
    function($attribute, $value, $fail) use ($regexPalabrasSoeces) {
        if (preg_match("/\b($regexPalabrasSoeces)\b/i", $value)) {
            $fail("La $attribute contiene palabras inapropiadas.");
        }
    },
    
    // Validación: no permite múltiples espacios consecutivos
    'regex:/^\S+(?: \S+)*$/',
    
    // Validación: solo permite caracteres alfanuméricos y puntuación básica
    'regex:/^[\w\s.,-]*$/',
    
    // Validación: no permite la misma letra repetida consecutivamente más de 2 veces
    'regex:/^(?!.*([a-zA-Z])\1\1).*$/',
    
    // Validación: no permite la misma palabra consecutiva
    function($attribute, $value, $fail) {
        if (preg_match('/\b(\w+)\b(?:\s+\1\b)+/i', $value)) {
            $fail("La descripcion contiene palabras repetidas consecutivamente.");
        }
    },

],
], [
'valor_deduccion.numeric' => 'El valor de la deducción debe ser numérico.',
'valor_deduccion.min' => 'El valor de la deducción no puede ser menor a 100.',
'tipo_deduccion.required' => 'El tipo de deducción es obligatorio.',
'tipo_deduccion.in' => 'El tipo de deducción debe ser "numérico" o "porcentaje".',
'descripcion_deduccion.string' => 'La descripción debe ser un texto.',
'descripcion_deduccion.max' => 'La descripción no puede tener más de 255 caracteres.',
'descripcion_deduccion.required' => 'La descripción no puede ser nula.',
'valor_deduccion.required' => 'La deduccion es obligatoria',
'descripcion_deduccion.regex' => 'La descripción contiene caracteres inválidos o múltiples espacios consecutivos.',
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

public function BTNLiquidar(Request $request)
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

return response()->json(['success' => 'Compras liquidadas correctamente.']);
}
public function generarReporteBuscar(Request $request)
{
    $terminoBusqueda = $request->input('buscar');

    // Validación del término de búsqueda
    if (!$terminoBusqueda) {
        return response()->json(['error' => 'Debe ingresar un término de búsqueda.'], 400);
    }

    // Filtrar las compras por descripción de compra o estado
    $compras = Compras::where('DESC_COMPRA', 'LIKE', '%' . $terminoBusqueda . '%')
                ->orWhereHas('estadoCompra', function($query) use ($terminoBusqueda) {
                    $query->where('DESC_ESTADO', 'LIKE', '%' . $terminoBusqueda . '%');
                })
                ->get();

    // Verificar si hay resultados para el término de búsqueda especificado
    if ($compras->isEmpty()) {
        return response()->json(['error' => 'No se encontraron compras para el término de búsqueda ingresado.'], 400);
    }

    // Obtener la fecha y hora actuales para el reporte
    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
    $path = public_path('images/CTraterra.jpeg'); // Ruta del logo
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    // Generar el PDF con la vista 'compras.pdf_reporte_buscar'
    $pdf = Pdf::loadView('compras.pdf_reporte_buscar', compact('compras', 'fechaHora', 'logoBase64', 'terminoBusqueda'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    // Retornar el PDF
    return $pdf->stream('Reporte_Compras_Busqueda.pdf');
}

}

