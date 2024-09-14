<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BitacoraController;
use App\Models\Equipos;
use App\Models\empleados;
Use App\Models\Permisos;
use App\Models\Estado_Mantenimiento;
use App\Models\Mantenimientos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Rules\Validaciones;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class MantenimientoControlador extends Controller
{
    protected $guard;
    protected $createNewUser;
    protected $empleados;
    protected $equipo;
    protected $estado_mantenimientos;
  
    /*protected $bitacora;

    public function __construct(BitacoraController $bitacora)
    {
        $this->bitacora = $bitacora;
    }*/


    public function __construct(){
    
        $this->empleados = empleados::all();
        $this->equipo = Equipos::all();
        $this->estado_mantenimientos = Estado_Mantenimiento::all();
    }

    public function index()
{
    $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de lectura en el objeto COMPRA
        $permisoLeer = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'MANTENIMIENTO')
                    ->limit(1);
            })
            ->where('Permiso_Consultar', 'PERMITIDO')
            ->exists();

        if (!$permisoLeer) {
            return redirect()->route('dashboard')->withErrors('No tiene permiso para acceder a la ventana de mantenimientos');
        }

    $response = Http::get('http://127.0.0.1:3000/mantenimientos');
    $mantenimientos = $response->json();

    // Formatear las fechas
    foreach ($mantenimientos as &$mantenimiento) {
        if (!empty($mantenimiento['FEC_INGRESO'])) {
            $mantenimiento['FEC_INGRESO'] = Carbon::parse($mantenimiento['FEC_INGRESO'])->format('Y-m-d');
        } else {
            $mantenimiento['FEC_INGRESO'] = 'Fecha no disponible';
        }

        if (!empty($mantenimiento['FEC_FINAL_PLANIFICADA'])) {
            $mantenimiento['FEC_FINAL_PLANIFICADA'] = Carbon::parse($mantenimiento['FEC_FINAL_PLANIFICADA'])->format('Y-m-d');
        } else {
            $mantenimiento['FEC_FINAL_PLANIFICADA'] = 'Fecha no disponible';
        }

        if (!empty($mantenimiento['FEC_FINAL_REAL'])) {
            $mantenimiento['FEC_FINAL_REAL'] = Carbon::parse($mantenimiento['FEC_FINAL_REAL'])->format('Y-m-d');
        } else {
            $mantenimiento['FEC_FINAL_REAL'] = 'Fecha no disponible';
        }
    }

    $empleados = \App\Models\Empleados::all()->keyBy('COD_EMPLEADO');
    $equipo = \App\Models\Equipos::all()->keyBy('COD_EQUIPO');
    $estado_mantenimientos = \App\Models\Estado_Mantenimiento::all()->keyBy('COD_ESTADO_MANTENIMIENTO');

    // Asignar empleados, equipos y estados a los mantenimientos
    foreach ($mantenimientos as &$mantenimiento) {
        $mantenimiento['empleado'] = $empleados[$mantenimiento['COD_EMPLEADO']] ?? null;
        $mantenimiento['equipo'] = $equipo[$mantenimiento['COD_EQUIPO']] ?? null;
        $mantenimiento['estado'] = $estado_mantenimientos[$mantenimiento['COD_ESTADO_MANTENIMIENTO']] ?? null;
    }

    return view('mantenimientos.index', compact('mantenimientos', 'empleados', 'equipo', 'estado_mantenimientos'));
}



/*pdf*/
public function pdf(){
    $response = Http::get('http://127.0.0.1:3000/mantenimientos');
    $mantenimientos = $response->json();
    //fecha
    $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
    // Formatear las fechas
    foreach ($mantenimientos as &$mantenimiento) {
        if (!empty($mantenimiento['FEC_INGRESO'])) {
            $mantenimiento['FEC_INGRESO'] = Carbon::parse($mantenimiento['FEC_INGRESO'])->format('Y-m-d');
        } else {
            $mantenimiento['FEC_INGRESO'] = 'Fecha no disponible';
        }

        if (!empty($mantenimiento['FEC_FINAL_PLANIFICADA'])) {
            $mantenimiento['FEC_FINAL_PLANIFICADA'] = Carbon::parse($mantenimiento['FEC_FINAL_PLANIFICADA'])->format('Y-m-d');
        } else {
            $mantenimiento['FEC_FINAL_PLANIFICADA'] = 'Fecha no disponible';
        }

        if (!empty($mantenimiento['FEC_FINAL_REAL'])) {
            $mantenimiento['FEC_FINAL_REAL'] = Carbon::parse($mantenimiento['FEC_FINAL_REAL'])->format('Y-m-d');
        } else {
            $mantenimiento['FEC_FINAL_REAL'] = 'Fecha no disponible';
        }
    }

    //cambio de img a formato pdf
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    $empleados = \App\Models\Empleados::all()->keyBy('COD_EMPLEADO');
    $equipo = \App\Models\Equipos::all()->keyBy('COD_EQUIPO');
    $estado_mantenimientos = \App\Models\Estado_Mantenimiento::all()->keyBy('COD_ESTADO_MANTENIMIENTO');

    // Asignar empleados, equipos y estados a los mantenimientos
    foreach ($mantenimientos as &$mantenimiento) {
        $mantenimiento['empleado'] = $empleados[$mantenimiento['COD_EMPLEADO']] ?? null;
        $mantenimiento['equipo'] = $equipo[$mantenimiento['COD_EQUIPO']] ?? null;
        $mantenimiento['estado'] = $estado_mantenimientos[$mantenimiento['COD_ESTADO_MANTENIMIENTO']] ?? null;
    }
    $pdf = Pdf::loadView('mantenimientos.pdf',  compact('mantenimientos', 'empleados', 'equipo', 'estado_mantenimientos', 'fechaHora','logoBase64'))
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
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de inserción en el objeto COMPRA
        $permisoInsercion = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'MANTENIMIENTO')
                    ->limit(1);
            })
            ->where('Permiso_Insercion', 'PERMITIDO')
            ->exists();

        if (!$permisoInsercion) {
            return redirect()->route('mantenimientos.index')->withErrors('No tiene permiso para crear mantenimientos');
        }

        $estados = \App\Models\Estado_Mantenimiento::all();
        return view('mantenimientos.crear', [
    'empleados' => $this->empleados,
    'equipo' => $this->equipo,
    'estados' =>$this->estado_mantenimientos
        ]);
    }

    public function insertar(Request $request)
    {
        $validaciones = new Validaciones();

        // Configurar la validación de rango de fechas
        $validaciones->validarRangoFechas($request->input('FEC_INGRESO'), $request->input('FEC_FINAL_REAL'));

        // Crear el validador con las reglas de validación
        $validator = Validator::make($request->all(), [
            'COD_EMPLEADO' => [(new Validaciones)->requerirSoloNumeros()],
            'COD_ESTADO_MANTENIMIENTO' => [(new Validaciones)->requerirCampo()],
            'COD_EQUIPO' => [(new Validaciones)->requerirSoloNumeros()],
            'DESC_MANTENIMIENTO' => [(new Validaciones)->requerirCampo()],
            'FEC_INGRESO' => [
                (new Validaciones)->requerirCampo(),
                function ($attribute, $value, $fail) use ($request, $validaciones) {
                    $validaciones->validarRangoFechas($request->input('FEC_INGRESO'), $request->input('FEC_FINAL_REAL'));
                    if (!$validaciones->passes('FEC_FINAL_REAL', $request->input('FEC_FINAL_REAL'))) {
                        $fail($validaciones->message());
                    }
                }
            ],
            'FEC_FINAL_PLANIFICADA' => [(new Validaciones)->requerirCampo()],
            'FEC_FINAL_REAL' => [
                (new Validaciones)->requerirCampo(),
                function ($attribute, $value, $fail) use ($request, $validaciones) {
                    if (!$validaciones->passes('FEC_FINAL_REAL', $value)) {
                        $fail($validaciones->message());
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $response = Http::post('http://127.0.0.1:3000/INS_MANTENIMIENTO', [
            'COD_EMPLEADO' => $request->COD_EMPLEADO,
            'COD_ESTADO_MANTENIMIENTO' => $request->COD_ESTADO_MANTENIMIENTO,
            'COD_EQUIPO' => $request->COD_EQUIPO,
            'DESC_MANTENIMIENTO' => $request->DESC_MANTENIMIENTO,
            'FEC_INGRESO' => Carbon::now(),
            'FEC_FINAL_PLANIFICADA' => $request->FEC_FINAL_PLANIFICADA,
            'FEC_FINAL_REAL' => $request->FEC_FINAL_REAL,
        ]);

        /* $this->bitacora->registrarEnBitacora(Auth::id(), 10, 'Mantenimiento insertado', 'Insert'); // ID_objetos 10: 'mantenimientos' */

        return redirect()->route('mantenimientos.index');
    }


    public function destroy($COD_MANTENIMIENTO)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de eliminación en el objeto COMPRA
        $permisoEliminacion = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'MANTENIMIENTO')
                    ->limit(1);
            })
            ->where('Permiso_Eliminacion', 'PERMITIDO')
            ->exists();

        if (!$permisoEliminacion) {
            return redirect()->route('mantenimientos.index')->withErrors('No tiene permiso para eliminar mantenimientos');
        }

        try {
            DB::statement('CALL ELI_MANTENIMIENTO(?)', [$COD_MANTENIMIENTO]);
           /* $this->bitacora->registrarEnBitacora(Auth::id(), 10, 'Mantenimiento eliminado', 'Delete'); // ID_objetos 10: 'mantenimientos'*/
            return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('mantenimientos.index')->with('error', 'Error al eliminar mantenimiento');
        }
    }
/*
    public function edit($COD_MANTENIMIENTO)
{
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de actualización en el objeto MANTENIMIENTO
    $permisoActualizacion = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'MANTENIMIENTO')
                ->limit(1);
        })
        ->where('Permiso_Actualizacion', 'PERMITIDO')
        ->exists();

    if (!$permisoActualizacion) {
        return redirect()->route('mantenimientos.index')->withErrors('No tiene permiso para editar los mantenimientos');
    }

    $response = Http::get("http://127.0.0.1:3000/mantenimientos/{$COD_MANTENIMIENTO}");
    $mantenimiento = $response->json();

    if (!isset($mantenimiento['COD_MANTENIMIENTO'])) {
        dd('COD_MANTENIMIENTO no está definido en la respuesta de la API', $mantenimiento);
    }

    // Formatear fechas
    $mantenimiento['FEC_FINAL_PLANIFICADA'] = !empty($mantenimiento['FEC_FINAL_PLANIFICADA']) ? Carbon::parse($mantenimiento['FEC_FINAL_PLANIFICADA'])->format('Y-m-d') : 'Fecha no disponible';
    $mantenimiento['FEC_FINAL_REAL'] = !empty($mantenimiento['FEC_FINAL_REAL']) ? Carbon::parse($mantenimiento['FEC_FINAL_REAL'])->format('Y-m-d') : 'Fecha no disponible';

    $equipo = $this->equipo;
    $empleados = $this->empleados;
    $estado_mantenimientos = $this->estado_mantenimientos;

    return view('mantenimientos.edit', compact('mantenimiento', 'empleados', 'equipo', 'estado_mantenimientos'));
}*/

/*
    public function update(Request $request, $COD_MANTENIMIENTO)
    {
        $validator = Validator::make($request->all(), [
            'COD_EMPLEADO' => [(new Validaciones)->requerirSoloNumeros()],
            'COD_ESTADO_MANTENIMIENTO' => [(new Validaciones)->requerirCampo()],
            'COD_EQUIPO' => [(new Validaciones)->requerirSoloNumeros()],
            'DESC_MANTENIMIENTO' => [(new Validaciones)->requerirCampo()],
            'FEC_INGRESO' => [(new Validaciones)->requerirCampo()],
            'FEC_FINAL_PLANIFICADA' => [(new Validaciones)->requerirCampo()],
            'FEC_FINAL_REAL' => [(new Validaciones)->requerirCampo()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $response = Http::put("http://127.0.0.1:3000/mantenimientos/{$COD_MANTENIMIENTO}", [
            'COD_EMPLEADO' => $request->COD_EMPLEADO,
            'COD_ESTADO_MANTENIMIENTO' => $request->COD_ESTADO_MANTENIMIENTO,
            'COD_EQUIPO' => $request->COD_EQUIPO,
            'DESC_MANTENIMIENTO' => $request->DESC_MANTENIMIENTO,
            'FEC_INGRESO' => Carbon::now(),
            'FEC_FINAL_PLANIFICADA' => $request->FEC_FINAL_PLANIFICADA,
            'FEC_FINAL_REAL' => $request->FEC_FINAL_REAL,
        ]);

      /*  $this->bitacora->registrarEnBitacora(Auth::id(), 10, 'Mantenimiento actualizado', 'Update'); // ID_objetos 10: 'mantenimientos'*/

       /* return redirect()->route('mantenimientos.index');*/
   /* }*/
     public function gestion($id)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de actualización en el objeto COMPRA
        $permisoActualizacion = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'MANTENIMIENTO')
                    ->limit(1);
            })
            ->where('Permiso_Actualizacion', 'PERMITIDO')
            ->exists();

        if (!$permisoActualizacion) {
            return redirect()->route('mantenimientos.index')->withErrors('No tiene permiso para gestionar los mantenimientos');
        }

        $mantenimiento = Mantenimientos::with(['empleado', 'equipo', 'estado_mantenimiento'])->findOrFail($id);
        $estados = Estado_Mantenimiento::all();
        return view('gestionMantenimiento.gestionMantenimiento', compact('mantenimiento', 'estados'));
    }

    public function actualizarEstado(Request $request, $id)
    {
        
        $mantenimiento = Mantenimientos::findOrFail($id);
        $mantenimiento->COD_ESTADO_MANTENIMIENTO = $request->input('estado');
        $mantenimiento->save();
    
        return redirect()->route('mantenimientos.index', $id)->with('success', 'Estado actualizado correctamente');
    }
}
