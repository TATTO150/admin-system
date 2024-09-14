<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BitacoraController;
use App\Models\Permisos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Rol;
use Barryvdh\DomPDF\Facade\Pdf;

class PermisosControlador extends Controller
{
    protected $bitacora;

    public function __construct(BitacoraController $bitacora)
    {
        $this->bitacora = $bitacora;
    }

    public function index()
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Verificar si el rol del usuario tiene el permiso de consulta en el objeto SOLICITUD
        $permisoConsultar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'PERMISO')
                    ->limit(1);
            })
            ->where('Permiso_Consultar', 'PERMITIDO')
            ->exists();
    
        if (!$permisoConsultar) {
            $this->bitacora->registrarEnBitacora(11, 'Intento de ingreso a la ventana de permisos sin permisos', 'Ingreso');
            return redirect()->route('dashboard')->withErrors('No tiene permiso para consultar permisos');
        }
        $response = Http::get("http://localhost:3000/Permisos");
        $permisos = $response->json();

        // Obtener los roles y objetos desde la base de datos
        $roles = \App\Models\Rol::all()->keyBy('Id_Rol');
        $objetos = \App\Models\Objeto::all()->keyBy('Id_Objetos');
        $this->bitacora->registrarEnBitacora(11 , 'ingreso a la ventana de permisos', 'ingreso'); // ID_objetos 13: 'permisos'
        return view('permisos.index', compact('permisos', 'roles', 'objetos'));
    }

    public function pdf(){
        $permisos=Permisos::all();
        $roles = \App\Models\Rol::all()->keyBy('Id_Rol');
        $objetos = \App\Models\Objeto::all()->keyBy('Id_Objetos');
        //fecha
        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
        //cambio de img a formato pdf
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    
        //paginacion
        $pdf = Pdf::loadView('permisos.pdf', compact('permisos', 'roles', 'objetos','fechaHora','logoBase64'))
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

        // Verificar si el rol del usuario tiene el permiso de inserción en el objeto SOLICITUD
        $permisoInsercion = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'PERMISO')
                    ->limit(1);
            })
            ->where('Permiso_Insercion', 'PERMITIDO')
            ->exists();

        if (!$permisoInsercion) {
            $this->bitacora->registrarEnBitacora(11, 'Intento de ingreso a la ventana de crear de permisos sin permisos', 'Ingreso');
            return redirect()->route('estado_usuarios.index')->withErrors('No tiene permiso para crear solicitudes');
        }
        // Obtén los roles desde la base de datos
        $roles = Rol::all(); // Asegúrate de importar el modelo Rol si no lo has hecho
        $objetos = \App\Models\Objeto::all();

        // Pasa los roles y objetos a la vista
        return view('permisos.crear', compact('roles', 'objetos'));
    }

    public function insertar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Id_Rol' => 'required|numeric',
            'Id_Objeto' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $response = Http::post('http://127.0.0.1:3000/INS_PERMISOS', [
            'Id_Rol' => $request->Id_Rol,
            'Id_Objeto' => $request->Id_Objeto,
            'Permiso_Insercion' => $request->Permiso_Insercion ,
            'Permiso_Eliminacion' => $request->Permiso_Eliminacion ,
            'Permiso_Actualizacion' => $request->Permiso_Actualizacion ,
            'Permiso_Consultar' => $request->Permiso_Consultar,
        ]);

       $this->bitacora->registrarEnBitacora( 11, 'Permiso insertado', 'Insert'); // ID_objetos 13: 'permisos'

        return redirect()->route('permisos.index');
    }

    public function destroy($COD_PERMISOS)
    {
        $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de eliminación en el objeto SOLICITUD
    $permisoEliminacion = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'PERMISO')
                ->limit(1);
        })
        ->where('Permiso_Eliminacion', 'PERMITIDO')
        ->exists();

    if (!$permisoEliminacion) {
        $this->bitacora->registrarEnBitacora(18, 'Intento de eliminar permisos sin permisos', 'ingreso');
        return redirect()->route('estado_usuarios.index')->withErrors('No tiene permiso para eliminar solicitudes');
    }
    // Obtén el parámetro PROTEGER_PERMISOS
    $parametro = \App\Models\Parametros::where('Parametro', 'PROTEGER_PERMISOS')->first();

    // Verifica si el parámetro está activado
    if ($parametro && $parametro->estaProtegido()) {
        // Obtén el permiso que se desea eliminar
        $permiso = \App\Models\Permisos::find($COD_PERMISOS);

        // Verifica si el permiso pertenece al rol ADMINISTRADOR
        if ($permiso && strtoupper($permiso->rol->Rol) === 'ADMINISTRADOR') {
            return response()->json(['error' => 'No se puede eliminar el rol ADMINISTRADOR porque está protegido.'], 403);
        }
    }

    try {
        // Si no está protegido, procede con la eliminación
        DB::statement('CALL ELI_PERMISOS(?)', [$COD_PERMISOS]);

        return response()->json(['success' => 'Permiso eliminado correctamente']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al eliminar permisos'], 500);
    }
    }

    public function edit($COD_PERMISOS)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Verificar permiso
        $permisoActualizacion = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'PERMISO')
                    ->limit(1);
            })
            ->where('Permiso_Actualizacion', 'PERMITIDO')
            ->exists();
    
        if (!$permisoActualizacion) {
            $this->bitacora->registrarEnBitacora(11, 'Intento actualuzar permisos sin permisos', 'actualizar');
            return redirect()->route('estado_usuarios.index')->withErrors('No tiene permiso para editar tipoe equipo');
        }
        $response = Http::get("http://localhost:3000/Permisos/{$COD_PERMISOS}");  
        $permisos = $response->json();

        // Verificar si la respuesta es un array y no está vacío
        if (empty($permisos)) {
            dd('La respuesta de la API está vacía', $permisos);
        }

        // Verificar si el primer elemento tiene la clave COD_PERMISOS
        if (!isset($permisos[0]['COD_PERMISOS'])) {
            dd('COD_PERMISOS no está definido en la respuesta de la API', $permisos);
        }

        $permisos = $permisos[0]; // Accede al primer elemento del array

        // Obtener los roles y objetos desde la base de datos
        $roles = \App\Models\Rol::all();
        $objetos = \App\Models\Objeto::all();

        return view('permisos.edit', compact('permisos', 'roles', 'objetos'));
    }

    public function update(Request $request, $COD_PERMISOS)
    {
        $validator = Validator::make($request->all(), [
            'Id_Rol' => 'required|numeric',
            'Id_Objeto' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $response = Http::put("http://127.0.0.1:3000/Permisos/{$COD_PERMISOS}", [
            'Id_Rol' => $request->Id_Rol,
            'Id_Objeto' => $request->Id_Objeto,
            'Permiso_Insercion' => $request->Permiso_Insercion ,
            'Permiso_Eliminacion' => $request->Permiso_Eliminacion ,
            'Permiso_Actualizacion' => $request->Permiso_Actualizacion ,
            'Permiso_Consultar' => $request->Permiso_Consultar ,
        ]);

        $this->bitacora->registrarEnBitacora(11 , 'Permiso actualizado', 'Update'); // ID_objetos 13: 'permisos'

        return redirect()->route('permisos.index');
    }
}
