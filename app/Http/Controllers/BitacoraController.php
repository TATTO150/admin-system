<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\Permisos;

class BitacoraController extends Controller
{
   
    public function index()
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Verificar si el rol del usuario tiene el permiso de consulta en el objeto SOLICITUD
        $permisoConsultar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'BITACORA')
                    ->limit(1);
            })
            ->where('Permiso_Consultar', 'PERMITIDO')
            ->exists();
    
        if (!$permisoConsultar) {
          
            return redirect()->route('dashboard')->withErrors('No tiene permiso para consultar bitacora');
        }
   // Obtener todas las bitácoras con sus relaciones
        $bitacoras = Bitacora::with('user', 'Objeto')->get();


        return view('bitacora.index', compact('bitacoras'));

  }
    
  public function pdf()
{
    // Aumentar el tiempo de ejecución máximo a 300 segundos (5 minutos)
    set_time_limit(300);

    // Obtener la fecha y hora actual
    $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');

    // Ruta del logo de la empresa
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    
    // Asegurarse de que el archivo de imagen existe
    if (file_exists($path)) {
        $logoData = file_get_contents($path);
        $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
    }

    // Recoger todos los registros de bitácora en un array
    $bitacoras = [];
    
    Bitacora::chunk(100, function ($bitacorasChunk) use (&$bitacoras) {
        // Añadir cada chunk al array completo de bitácoras
        foreach ($bitacorasChunk as $bitacora) {
            $bitacoras[] = $bitacora;
        }
    });

    // Generar el PDF para todos los registros en una sola vista
    $pdf = Pdf::loadView('bitacora.pdf', compact('bitacoras', 'fechaHora', 'logoBase64'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    // Retornar el PDF generado al navegador
    return $pdf->stream('reporte_bitacora.pdf');
}
    /**
     * Registra un evento en la bitácora.
     *
     * @param  int  $userId
     * @param  int  $ID_objetos
     * @param  string  $descripcion
     * @param  string  $accion
     * @return void
     */
    public function registrarEnBitacora($ID_objetos, $descripcion, $accion)
    {
        $user = Auth::user();

        Bitacora::create([
            'Id_usuario' => $user->Id_usuario,
            'Id_Objetos' => $ID_objetos,
            'Descripcion' => $descripcion,
            'Fecha' => Carbon::now(),
            'Accion' => $accion
        ]);
    }
}
