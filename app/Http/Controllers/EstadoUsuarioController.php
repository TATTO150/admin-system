<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EstadoUsuario;
use App\Models\Parametros;
use App\Http\Requests\EstadoUsuarioRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Permisos;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\BitacoraController;  //BITACORA

class EstadoUsuarioController extends Controller
{
    protected $bitacora;

    public function __construct(BitacoraController $bitacora)
    {
       
        $this->bitacora = $bitacora;

    }
     public function create()
     {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de inserción en el objeto SOLICITUD
        $permisoInsercion = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'USUARIOESTADO')
                    ->limit(1);
            })
            ->where('Permiso_Insercion', 'PERMITIDO')
            ->exists();

        if (!$permisoInsercion) {
            return redirect()->route('estado_usuarios.index')->withErrors('No tiene permiso para crear solicitudes');
        }
     
  
        return view('estado_usuarios.create');
     }
 
     public function store(EstadoUsuarioRequest $request)
     {
         // Verificar si ya existe un estado con el mismo nombre
         // Verificar si ya existe un estado con el mismo nombre
    $estadoExistente = EstadoUsuario::where('ESTADO', $request->ESTADO)->first();

    // Si ya existe un estado con el mismo nombre, retornar con error
    if ($estadoExistente) {
        return redirect()->back()->withErrors(['ESTADO' => 'Este estado ya está registrado en el sistema.'])->withInput();
    }
     
         // Crear el nuevo estado de usuario
         EstadoUsuario::create([
             'ESTADO' => $request->ESTADO,
             'DESCRIPCION' => $request->DESCRIPCION,
         ]);
     
         return redirect()->route('estado_usuarios.index')->with('success', 'Estado de usuario creado exitosamente.');
     }
     // Mostrar el formulario de edición
     public function edit($id)
     {
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Verificar permiso
        $permisoActualizacion = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'USUARIOESTADO')
                    ->limit(1);
            })
            ->where('Permiso_Actualizacion', 'PERMITIDO')
            ->exists();
    
        if (!$permisoActualizacion) {
            return redirect()->route('estado_usuarios.index')->withErrors('No tiene permiso para editar tipo de equipo');
        }
    
        // Obtener el estado por su ID
        $estado = EstadoUsuario::findOrFail($id);
    
        // Estados protegidos que no se pueden editar
        $estadosProtegidos = ['ACTIVO', 'NUEVO', 'BLOQUEADO', 'INACTIVO'];
    
        // Verificar si el estado es uno de los protegidos
        if (in_array($estado->ESTADO, $estadosProtegidos)) {
            return redirect()->route('estado_usuarios.index')->with('error', 'Este estado de usuario está protegido y no puede ser editado.');
        }
    
        // Si no está protegido, mostrar la vista de edición
        return view('estado_usuarios.edit', compact('estado'));
    }
     
 
     // Actualizar un estado de usuario
     public function update(EstadoUsuarioRequest $request, $id)
     {
         $estado = EstadoUsuario::findOrFail($id);
         $estado->update([
             'ESTADO' => $request->ESTADO,
             'DESCRIPCION' => $request->DESCRIPCION,
         ]);
     
         return redirect()->route('estado_usuarios.index')->with('success', 'Estado de usuario actualizado exitosamente.');
     }
     public function destroy($id)
{
    // Obtener el estado de usuario por su ID
    $estado = EstadoUsuario::findOrFail($id);

    // Estados que no se pueden eliminar
    $estadosNoEliminables = ['NUEVO', 'ACTIVO', 'BLOQUEADO', 'INACTIVO'];

    // Verificar si el estado es uno de los que no se pueden eliminar
    if (in_array($estado->ESTADO, $estadosNoEliminables)) {
        return redirect()->route('estado_usuarios.index')
            ->with('error', 'No se puede eliminar este estado de usuario porque su eliminación está bloqueada.');
    }

    // Verificar si el estado está siendo utilizado en la tabla usuarios
    if ($estado->relatedRecords()->count() > 0) {
        return redirect()->route('estado_usuarios.index')
            ->with('error', 'No se puede eliminar este estado de usuario porque está siendo utilizado por uno o más usuarios.');
    }

    // Si no está en la lista de estados bloqueados y no está en uso, eliminar el estado
    $estado->delete();

    // Redirigir con mensaje de éxito
    return redirect()->route('estado_usuarios.index')
        ->with('success', 'Estado de usuario eliminado correctamente.');
}

     

 
     // Mostrar la lista de estados de usuario
     public function index()
     {
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Verificar si el rol del usuario tiene el permiso de consulta en el objeto SOLICITUD
        $permisoConsultar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'USUARIOESTADO')
                    ->limit(1);
            })
            ->where('Permiso_Consultar', 'PERMITIDO')
            ->exists();
    
        if (!$permisoConsultar) {
            $this->bitacora->registrarEnBitacora(18, 'Intento de ingreso a la ventana de tipoequipo sin permisos', 'Ingreso');
            return redirect()->route('dashboard')->withErrors('No tiene permiso para consultar solicitudes');
        }
         $estados = EstadoUsuario::all();
         return view('estado_usuarios.index', compact('estados'));
     }
     public function reporte()
    {
        // Obtener los datos necesarios para el reporte
        $estado_usuarios = EstadoUsuario::all(); // Aquí ajusta el modelo y los datos que necesitas

        // Cargar el logo y la fecha
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
        $fechaHora = now()->format('d-m-Y H:i:s');

        // Crear el PDF
        $pdf = PDF::loadView('estado_usuarios.estado_usuarios', compact('estado_usuarios', 'logoBase64', 'fechaHora'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
            ]);

        // Descargar el PDF con un nombre específico
        return $pdf->stream('reporte_estado_usuarios.pdf');
    }
}
