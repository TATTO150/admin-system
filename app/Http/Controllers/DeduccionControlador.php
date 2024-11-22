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
use Barryvdh\DomPDF\Facade\Pdf;
use App\Rules\Validaciones;

use Illuminate\Support\Facades\Validator;

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
    // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
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

       if ($deducciones->isEmpty()) {
        // Retornar un mensaje de error en formato JSON
        return back()->withErrors( 'No existen deducciones en esta compra');
        }

       return view('compras.deduccion', compact( 'deducciones','compras', 'pagoFinal'));
   }

   public function generarPDF($id)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('sesion.suspendida');
        }

        // Obtener la compra
        $compra = Compras::with(['usuario', 'estadocompras', 'tipocompras','proyectos'])->findOrFail($id);

      

        // Obtener las deducciones asociadas a esa compra
        $deducciones = Deduccion::where('COD_COMPRA', $id)->get();

        // Validar si no se encontraron deducciones
        $usuario = User::all()->keyBy('Id_usuario');
        $estadocompras = EstadoCompra::all()->keyBy('COD_ESTADO');
        $tipocompras = TipoCompra::all()->KeyBy('COD_TIPO');
        

        // Suma total de deducciones
        $totalDeducciones = $deducciones->sum('VALOR_DEDUCCION');

        // Calcular el precio final
        $pagoFinal = $compra->PRECIO_COMPRA - $totalDeducciones;

        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');

        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
        
        $pdfFileName = "Reporte_Compra_{$id}_{$compra->usuario->name}_{$fechaHora}.pdf";


        
        // Generar PDF usando la vista `reporte_compras.blade.php`
        $pdf = Pdf::loadView('compras.reporte_compras', [
            'compra' => $compra,
            'deducciones' => $deducciones,
            'totalDeducciones' => $totalDeducciones,
            'pagoFinal' => $pagoFinal,
            'fechaHora' => $fechaHora,
            'logoBase64' => $logoBase64,
            'tipocompras' => $tipocompras,
            'estadocompras' => $estadocompras,
            'usuario' => $usuario,
        ])->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);
        // Retornar el PDF para descargar
        return $pdf->stream($pdfFileName);
    }

   
    public function edit($COD_DEDUCCION){ 

        // Verificar si el usuario no está autenticado
        if (!Auth::check()) {
            // Redirigir a la vista `sesion_suspendida`
            return redirect()->route('sesion.suspendida');
        }
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
    $validated = $request->validate([
        'VALOR_DEDUCCION' => 'numeric|min:0',
        'tipo_deduccion' => 'required|in:numerico,porcentaje',
        'DESC_DEDUCCION' => 'nullable|string|max:255',
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
    // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
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