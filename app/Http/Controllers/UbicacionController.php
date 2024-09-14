<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ubicaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Rules\Validaciones;
use Barryvdh\DomPDF\Facade\Pdf;

class UbicacionController extends Controller
{
    public function index()
    {
        $response = Http::get('http://127.0.0.1:3000/Ubicaciones');
        $ubicaciones = $response->json();

        return view('ubicaciones.index', compact('ubicaciones'));
    }
    public function pdf(){
        $ubicaciones = Ubicaciones::all();
        //fecha
        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
        //cambio de img a formato pdf
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
        //paginacion
        $pdf = Pdf::loadView('ubicaciones.index', compact('ubicaciones','fechaHora','logoBase64'))
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
        return view('ubicaciones.crear');
    }

    public function insertar(Request $request)
    {
        

        Http::post('http://127.0.0.1:3000/INS_UBICACION');

        $validator = Validator::make($request->all(), [
            'NOM_UBICACION' => [(new Validaciones)->requerirCampo()->prohibirNumerosSimbolos()->requerirTodoMayusculas()],
            'DESCRIPCION' => [(new Validaciones)->requerirCampo()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $response = Http::post('http://127.0.0.1:3000/INS_UBICACION', [
            'NOM_UBICACION' => $request->NOM_UBICACION,
            'DESCRIPCION' => $request->DESCRIPCION,
        ]);

        return redirect()->route('ubicaciones.index')->with('success', 'Ubicación creada correctamente');
    }


    public function edit($COD_UBICACION)
    {
        $response = Http::get("http://127.0.0.1:3000/Ubicaciones/{$COD_UBICACION}");
        $ubicacion = $response->json()[0];

        if (!isset($ubicacion['COD_UBICACION'])) {
            dd('COD_UBICACION no está definido en la respuesta de la API', $ubicacion);
        }

        return view('ubicaciones.edit', compact('ubicacion'));
    }


    public function update(Request $request, $COD_UBICACION)
    {
      

        $validator = Validator::make($request->all(), [
            'NOM_UBICACION' => [(new Validaciones)->requerirCampo()->prohibirNumerosSimbolos()->requerirTodoMayusculas()],
            'DESCRIPCION' => [(new Validaciones)->requerirCampo()->prohibirNumerosSimbolos()->requerirTodoMayusculas()],

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $response = Http::put("http://127.0.0.1:3000/Ubicaciones/{$COD_UBICACION}", [
            'NOM_UBICACION' => $request->NOM_UBICACION,
            'DESCRIPCION' => $request->DESCRIPCION,

        ]);

        return redirect()->route('ubicaciones.index')->with('success', 'Ubicación actualizada correctamente');
    }



    public function destroy($COD_UBICACION)
    {
        try {
            DB::statement('CALL ELI_UBICACION(?)', [$COD_UBICACION]);


            return redirect()->route('ubicaciones.index')->with('success', 'Ubicacion eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('ubicaciones.index')->with('error', 'Error al eliminar Ubicacion');
        }
    }
    
}
