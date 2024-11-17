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
use App\Providers\PermisoService;

class PermisosControlador extends Controller
{
    protected $bitacora;
    protected $permisoService;

    public function __construct(BitacoraController $bitacora, PermisoService $permisoService)
    {
        $this->bitacora = $bitacora;
        $this->permisoService = $permisoService;

    }

    public function index()
    {
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Nueva validación de permisos
        $this->permisoService->tienePermiso('PERMISO', 'Consultar', true);

        $response = Http::get("http://localhost:3000/Permisos");
        $permisos = $response->json();

        // Obtener los roles y objetos desde la base de datos
        $roles = \App\Models\Rol::all()->keyBy('Id_Rol');
        $objetos = \App\Models\Objeto::all()->keyBy('Id_Objetos');
        $this->bitacora->registrarEnBitacora(11 , 'ingreso a la ventana de permisos', 'ingreso'); // ID_objetos 13: 'permisos'
        return view('permisos.index', compact('permisos', 'roles', 'objetos'));
    }

    public function pdf(){
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
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
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Nueva validación de permisos
        $this->permisoService->tienePermiso('PERMISO', 'Insercion', true);
        
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

        // Inserta los permisos utilizando el modelo
        Permisos::create([
            'Id_Rol' => $request->Id_Rol,
            'Id_Objeto' => $request->Id_Objeto,
            'Permiso_Insercion' => $request->Permiso_Insercion,
            'Permiso_Eliminacion' => $request->Permiso_Eliminacion,
            'Permiso_Actualizacion' => $request->Permiso_Actualizacion,
            'Permiso_Consultar' => $request->Permiso_Consultar,
        ]);

       $this->bitacora->registrarEnBitacora( 11, 'Permiso insertado', 'Insert'); // ID_objetos 13: 'permisos'

        return redirect()->route('permisos.index');
    }

    public function destroy($COD_PERMISOS)
    {
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
        $user = Auth::user();
    $roleId = $user->Id_Rol;

   // Nueva validación de permisos
   $this->permisoService->tienePermiso('PERMISO', 'Eliminacion', true);
   
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
        // Si se encuentra, se elimina
        $permiso->delete();

        return response()->json(['success' => 'Permiso eliminado correctamente']);
    } catch (\Exception $e) {
        // Captura cualquier excepción que ocurra y responde con un error
        return response()->json(['error' => 'Error al eliminar permisos'], 500);
    }
    }

    public function edit($COD_PERMISOS)
    {
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Nueva validación de permisos
        $this->permisoService->tienePermiso('PERMISO', 'Actualizacion', true);

       // Busca los permisos usando el modelo Permisos
       $permisos = Permisos::findOrFail($COD_PERMISOS);

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

        try {
            // Buscar el permiso por su ID
            $permiso = Permisos::findOrFail($COD_PERMISOS);
    
            // Actualizar los campos con los datos del request
            $permiso->update([
                'Id_Rol' => $request->Id_Rol,
                'Id_Objeto' => $request->Id_Objeto,
                'Permiso_Insercion' => $request->Permiso_Insercion,
                'Permiso_Eliminacion' => $request->Permiso_Eliminacion,
                'Permiso_Actualizacion' => $request->Permiso_Actualizacion,
                'Permiso_Consultar' => $request->Permiso_Consultar,
            ]);

        $this->bitacora->registrarEnBitacora(11 , 'Permiso actualizado', 'Update'); // ID_objetos 13: 'permisos'

        return redirect()->route('permisos.index');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar permisos'], 500);
        }
    }
}
