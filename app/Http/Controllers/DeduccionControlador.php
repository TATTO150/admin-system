<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proyectos;
use App\Models\Deduccion;
use App\Models\Compras;
use App\Models\User;
use App\Models\TipoCompra;
use App\Models\EstadoCompra;
use App\Http\Controllers\BitacoraController;
use Illuminate\Support\Facades\Auth;
use App\Providers\PermisoService;
use Carbon\Carbon;

class DeduccionControlador extends Controller
{
    protected $estadocompras, $tipocompras, 
    $permisoService, $bitacora, $proyectos, $usuarios, $deduccion, $preciofinal;

    public function __construct(BitacoraController $bitacora, PermisoService $permisoService)
    {
        $this->bitacora = $bitacora;
        $this->permisoService = $permisoService;  // Inyectar el PermisoService
        
    }
   
   public function index($id)
   {
       $user = Auth::user();
       $roleId = $user->Id_Rol;

        // Validación de permisos
        $this->permisoService->tienePermiso('COMPRA', 'Consultar', true);


        // Validación de existencia de compra
        $compras = Compras::with(['usuario', 'estadocompras', 'tipocompras'])->find($id);
        if (!$compras) {
            return redirect()->route('compras.index')->withErrors('Compra no encontrada.');
        }
        //relaciones de datos 
       $compras = Compras::findOrFail($id); 
       $compras = Compras::with('usuario')->findOrFail($id);
       $compras = Compras::with('estadocompras')->findOrFail($id);
       $compras = Compras::with('tipocompras')->findOrFail($id);

       // Obtener las deducciones asociadas a esa compra
       $deducciones = Deduccion::where('COD_COMPRA', $id)->get();
       $totalDeducciones = $deducciones->sum('VALOR_DEDUCCION');
       $pagoFinal = $compras->PRECIO_COMPRA - $totalDeducciones;

       return view('compras.deduccion', compact( 'deducciones','compras', 'pagoFinal'));
   }

    public function edit($COD_DEDUCCION){ 
        $USER = Auth::user();

        $this-> permisoService->tienePermiso('DEDUCCION','Actualizacion',true);

        $deducciones = Deduccion::find('COD_DEDUCCION',$COD_DEDUCCION);


        if (!isset($deducciones['COD_DEDUCCION'])) {
            dd('COD_DEDUCCION no está definido en la respuesta del modal', $deducciones);
        }

        if (!$deducciones) {
            return redirect()->back()->withErrors('Deducción no encontrada.');
        }

        return view('compras.deduccion',compact('deducciones'));

    }   


public function update(Request $request, $COD_COMPRA, $COD_DEDUCCION)
{
    $palabrasSoeces = ['conchatumadre', 'prostituta', 'pene', 'culo', 
        'estupido', 'idiota', 'invecil', 'imbécil', 'tonto', 'inutil', 'malnacido',
        'baboso', 'payaso', 'demonio', 'muerte', 'violacion', 'pudrete', 'basura',
        'asqueroso', 'bastardo', 'puta','despreciable','pendejo','bastardo','maldito']; // Reemplaza con palabras inapropiadas específicas
    
        // Convertir las palabras prohibidas en una expresión regular
        $regexPalabrasSoeces = implode('|', array_map('preg_quote', $palabrasSoeces));
        
        // Validar la entrada con reglas avanzadas
        $validated = $request->validate([
            'VALOR_DEDUCCION' => 'required|numeric|min:1',
            'tipo_deduccion' => 'required|in:numerico,porcentaje',
            'DESC_DEDUCCION' => [
                'required',
                'nullable',
                'string',
                'max:255',
                
                // Validación: no permite palabras soeces
                function($attribute, $value, $fail) use ($regexPalabrasSoeces) {
                    if (preg_match("/\b($regexPalabrasSoeces)\b/i", $value)) {
                        $fail("La descripción contiene palabras inapropiadas.");
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
            'VALOR_DEDUCCION.numeric' => 'El valor de la deducción debe ser numérico.',
            'VALOR_DEDUCCION.min' => 'El valor de la deducción no puede ser menor a 100.',
            'tipo_deduccion.required' => 'El tipo de deducción es obligatorio.',
            'tipo_deduccion.in' => 'El tipo de deducción debe ser "numérico" o "porcentaje".',
            'DESC_DEDUCCION.string' => 'La descripción debe ser un texto.',
            'DESC_DEDUCCION.max' => 'La descripción no puede tener más de 255 caracteres.',
            'DESC_DEDUCCION.required' => 'La descripción no puede ser nula.',
            'VALOR_DEDUCCION.required' => 'La deduccion es obligatoria',
            'DESC_DEDUCCION.regex' => 'La descripción contiene caracteres inválidos o múltiples espacios consecutivos.',
        ]);

    // Buscar la deducción por el ID de la deducción
    $deducciones = Deduccion::where('COD_DEDUCCION', $COD_DEDUCCION)->first();
 
     // Buscar deducción
     $deducciones = Deduccion::where('COD_DEDUCCION', $COD_DEDUCCION)->first();
     if (!$deducciones) {
         return redirect()->route('compras.deduccion', $COD_COMPRA)->withErrors('Deducción no encontrada.');
     }

    if (!$deducciones) {
        return redirect()->route('compras.deduccion', $COD_COMPRA)->with('error', 'Deducción no encontrada.');
    }

    // Buscar compra
    $compra = Compras::where('COD_COMPRA', $COD_COMPRA)->first();
    if (!$compra || !is_numeric($compra->PRECIO_COMPRA)) {
        return redirect()->route('compras.deduccion', $COD_COMPRA)->withErrors('Compra no encontrada o precio inválido.');
    }

    $compra = Compras::where('COD_COMPRA', $COD_COMPRA)->first();

    if (!$compra) {
        return redirect()->route('compras.deduccion', $COD_COMPRA)->with('error', 'Compra no encontrada.');
    }

    // Verificar si la deducción es porcentaje o numérica
    if ($validated['tipo_deduccion'] === 'porcentaje') {
        // Si es porcentaje, convertirlo a valor numérico
        $valorDeduccionActualizada = ($validated['VALOR_DEDUCCION'] / 100) * $compra->PRECIO_COMPRA;
    } else {
        // Si es numérico, usar el valor directamente
        $valorDeduccionActualizada = $validated['VALOR_DEDUCCION'];
    }

    $sumaDeduccionesExistentes = Deduccion::where('COD_COMPRA', $compra->COD_COMPRA)
    ->where('COD_DEDUCCION', '!=', $COD_DEDUCCION)
    ->sum('VALOR_DEDUCCION');

    $totalDeducciones = $sumaDeduccionesExistentes + $valorDeduccionActualizada;


    if ($totalDeducciones > $compra->PRECIO_COMPRA) {
        // Si excede, redirigir con mensaje de error
        return redirect()->route('compras.deduccion', $COD_COMPRA)
            ->withErrors(['error' => 'Las deducciones no puede exceder el precio de la compra.']);
    }
    // Actualizar los campos
    $deducciones->update([
        'DESC_DEDUCCION' => $request->input('DESC_DEDUCCION'),
        'VALOR_DEDUCCION' => $valorDeduccionActualizada,
    ]);

    // Redirigir con mensaje de éxito
    return redirect()->route('compras.deduccion', $COD_COMPRA)->with('success', 'Deducción actualizada correctamente!');
}


public function destroy($COD_COMPRA, $COD_DEDUCCION)
{
    // Encuentra la deducción basada en los parámetros
    $deducciones = Deduccion::where('COD_COMPRA', $COD_COMPRA)
                          ->where('COD_DEDUCCION', $COD_DEDUCCION)
                          ->first();

    // Verifica si la deducción existe
    if (!$deducciones) {
        return redirect()->back()->withErrors('Deducción no encontrada.');
    }

    // Elimina la deducción
    $deducciones->delete();

    // Redirige con un mensaje de éxito
    return redirect()->route('compras.deduccion', ['COD_COMPRA' => $COD_COMPRA])
        ->with('success', 'Deducción eliminada correctamente.');
}




}