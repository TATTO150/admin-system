<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BitacoraController;
use App\Models\Rol;
use App\Models\Permisos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Rules\Validaciones;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Parametros;
use App\Http\Requests\RolRequest;

class RolControlador extends Controller
{
    /*protected $bitacora;

    public function __construct(BitacoraController $bitacora)
    {
        $this->bitacora = $bitacora;
    }*/

    public function index()
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de consulta en el objeto ROL
        $permisoConsultar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'ROL')
                    ->limit(1);
            })
            ->where('Permiso_Consultar', 'PERMITIDO')
            ->exists();

        if (!$permisoConsultar) {
            return redirect()->route('dashboard')->withErrors('No tiene permiso para ingresar a la ventana de roles');
        }

        // Obtener los roles desde la API
        $response = Http::get('http://127.0.0.1:3000/roles');
        $roles = $response->json();

        // Cargar la vista de roles con los datos obtenidos
        return view('roles.index', compact('roles'));
    }

   
    public function pdf(){
        $roles=Rol::all();
        //fecha
        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');

        //cambio de img a formato pdf
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    
        //paginacion
        $pdf = Pdf::loadView('roles.pdf',  compact('roles', 'fechaHora','logoBase64'))
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

        // Verificar si el rol del usuario tiene el permiso de consulta en el objeto ROL
        $permisoConsultar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'ROL')
                    ->limit(1);
            })
            ->where('Permiso_Insercion', 'PERMITIDO')
            ->exists();

        if (!$permisoConsultar) {
            return redirect()->route('roles.index')->withErrors('No tiene permiso para crear roles');
        }

        return view('roles.crear');
    }


    public function insertar(RolRequest $request)
    {
      
        $response = Http::post('http://127.0.0.1:3000/INS_ROL', [
            'Rol' => $request->Rol,
            'Descripcion' => $request->Descripcion,
        ]);
    
        /* if ($response->successful()) {
            $this->bitacora->registrarEnBitacora(Auth::id(), 2, 'Rol creado', 'Insert'); // ID_objetos 2: 'roles'
        }*/
    
        return redirect()->route('roles.index');
    }

    public function edit($Id_Rol)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de actualización en el objeto ROL
        $permisoActualizar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'ROL')
                    ->limit(1);
            })
            ->where('Permiso_Actualizacion', 'PERMITIDO')
            ->exists();

        if (!$permisoActualizar) {
            return redirect()->route('roles.index')->withErrors('No tiene permiso para editar roles');
        }

        // Obtener los datos del rol desde la API
        $response = Http::get("http://127.0.0.1:3000/Roles/{$Id_Rol}");
        $roles = $response->json();

        // Verifica el contenido de la respuesta
        if (!isset($roles['Id_Rol'])) {
            dd('Id_Rol no está definido en la respuesta de la API', $roles);
        }

        return view('roles.edit', compact('roles'));
    }

    public function update(RolRequest $request, $Id_Rol)
    {
        $response = Http::put("http://127.0.0.1:3000/Roles/{$Id_Rol}", [
            'Rol' => $request->Rol,
            'Descripcion' => $request->Descripcion,
        ]);
    
        /* if ($response->successful()) {
            $this->bitacora->registrarEnBitacora(Auth::id(), 2, 'Rol actualizado', 'Update'); // ID_objetos 2: 'roles'
        }*/
    
        return redirect()->route('roles.index');
    }
    public function destroy($Id_Rol)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Verificar si el rol del usuario tiene el permiso de eliminación en el objeto ROL
        $permisoEliminar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'ROL')
                    ->limit(1);
            })
            ->where('Permiso_Eliminacion', 'PERMITIDO')
            ->exists();
    
        if (!$permisoEliminar) {
            return redirect()->route('roles.index')->withErrors('No tiene permiso para eliminar roles');
        }
    
        // Verificar si el rol está protegido
        // Verificar si se deben proteger los roles
        $parametro = Parametros::where('Parametro', 'proteger_roles')->first();
        $protegerRoles = $parametro ? $parametro->Valor == 1 : false;
    
        // Si el parámetro proteger_roles está activado, no permitir la eliminación
        if ($protegerRoles) {
            return redirect()->route('roles.index')->withErrors('No se puede eliminar el rol porque la protección está activada.');
        }
    
    
        try {
            DB::statement('CALL ELI_ROL(?)', [$Id_Rol]);
            return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error', 'Error al eliminar rol');
        }
    }

}
