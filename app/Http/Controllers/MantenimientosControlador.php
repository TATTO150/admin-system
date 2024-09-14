<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BitacoraController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Rules\Validaciones;

class MantenimientosControlador extends Controller
{
   /* protected $bitacora;

    public function __construct(BitacoraController $bitacora)
    {
        $this->bitacora = $bitacora;
    }*/

    public function index()
    {
        $response = Http::get('http://127.0.0.1:3000/mantenimientos');
        $mantenimientos = $response->json();
        return view('mantenimientos.index', compact('mantenimientos'));
    }

    public function crear()
    {
        return view('mantenimientos.crear');
    }

    public function insertar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'COD_EMPLEADO' => [(new Validaciones)->requerirSoloNumeros()],
            'FECHA_PAGO' => [(new Validaciones)->requerirCampo()],
            'SALARIO_BASE' => [(new Validaciones)->requerirSoloNumeros()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $response = Http::post('http://127.0.0.1:3000/INS_MANTENIMIENTO', [
            'COD_EMPLEADO' => $request->COD_EMPLEADO,
            'FECHA_PAGO' => $request->FECHA_PAGO,
            'SALARIO_BASE' => $request->SALARIO_BASE,
        ]);

        if ($response->successful()) {
          /*  $this->bitacora->registrarEnBitacora(Auth::id(), 11, 'Mantenimiento insertado', 'Insert'); // ID_objetos 11: 'mantenimientos'*/
        }

        return redirect()->route('mantenimientos.index');
    }

    public function destroy($COD_MANTENIMIENTO)
    {
        try {
            DB::statement('CALL ELI_MANTENIMIENTO(?)', [$COD_MANTENIMIENTO]);
           /*$this->bitacora->registrarEnBitacora(Auth::id(), 11, 'Mantenimiento eliminado', 'Delete'); // ID_objetos 11: 'mantenimientos'*/
            return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('mantenimientos.index')->with('error', 'Error al eliminar mantenimiento');
        }
    }

    public function edit($COD_MANTENIMIENTO)
    {
        $response = Http::get("http://127.0.0.1:3000/mantenimientos/{$COD_MANTENIMIENTO}");
        $mantenimientos = $response->json();

        if (!isset($mantenimientos['COD_MANTENIMIENTO'])) {
            dd('COD_MANTENIMIENTO no está definido en la respuesta de la API', $mantenimientos);
        }

        return view('mantenimientos.edit', compact('mantenimientos'));
    }

    public function update(Request $request, $COD_MANTENIMIENTO)
    {
        $validator = Validator::make($request->all(), [
            'COD_EMPLEADO' => [(new Validaciones)->requerirSoloNumeros()],
            'FECHA_PAGO' => [(new Validaciones)->requerirCampo()],
            'DEDUCCIONES' => [(new Validaciones)->requerirSoloNumeros()],
            'SALARIO_NETO' => [(new Validaciones)->requerirSoloNumeros()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $response = Http::put("http://127.0.0.1:3000/mantenimientos/{$COD_MANTENIMIENTO}", [
            'COD_EMPLEADO' => $request->COD_EMPLEADO,
            'FECHA_PAGO' => $request->FECHA_PAGO,
            'DEDUCCIONES' => $request->DEDUCCIONES,
            'SALARIO_NETO' => $request->SALARIO_NETO,
        ]);

        if ($response->successful()) {
           /* $this->bitacora->registrarEnBitacora(Auth::id(), 11, 'Mantenimiento actualizado', 'Update'); // ID_objetos 11: 'mantenimientos'*/
        }

        return redirect()->route('mantenimientos.index');
    }
}
