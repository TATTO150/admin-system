<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use  App\Models\TipoEquipo;
use Illuminate\Support\Facades\Auth;
use App\Models\Permisos;
use Barryvdh\DomPDF\Facade\Pdf;

class TipoEquipoControlador extends Controller
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
                ->where('Objeto', 'TIPOEQUIPO')
                ->limit(1);
        })
        ->where('Permiso_Consultar', 'PERMITIDO')
        ->exists();

    if (!$permisoConsultar) {
        $this->bitacora->registrarEnBitacora(18, 'Intento de ingreso a la ventana de tipoequipo sin permisos', 'Ingreso');
        return redirect()->route('dashboard')->withErrors('No tiene permiso para consultar tipo equipo');
    }

        $tipoEquipos = TipoEquipo::all();
        $this->bitacora->registrarEnBitacora(18, 'Intento de ingreso a la ventana de tipoequipo ', 'Ingreso');
        return view('tipoequipo.index', compact('tipoEquipos'));
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
                    ->where('Objeto', 'TIPOEQUIPO')
                    ->limit(1);
            })
            ->where('Permiso_Insercion', 'PERMITIDO')
            ->exists();

        if (!$permisoInsercion) {
            return redirect()->route('tipoequipo.index')->withErrors('No tiene permiso para crear tipo equipo');
        }
        return view('tipoequipo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'TIPO_EQUIPO' => 'required|string|max:255',
        ]);
    
        // Crear un nuevo tipo de equipo con `protegido` establecido en 1
        $tipoEquipo = new TipoEquipo();
        $tipoEquipo->TIPO_EQUIPO = $request->TIPO_EQUIPO;
        $tipoEquipo->PROTEGIDO = 1; // Proteger el nuevo tipo de equipo
        $tipoEquipo->save();
        $this->bitacora->registrarEnBitacora(18, 'tipoequipo insertado', 'insertado');
        return redirect()->route('tipo_equipo.index')->with('success', 'Tipo de equipo creado correctamente');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Verificar permiso
        $permisoActualizacion = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'TIPOEQUIPO')
                    ->limit(1);
            })
            ->where('Permiso_Actualizacion', 'PERMITIDO')
            ->exists();
    
        if (!$permisoActualizacion) {

            return redirect()->route('tipoequipo.index')->withErrors('No tiene permiso para editar tipoe equipo');
        }
        // Buscar el tipo de equipo por su ID
        $tipoEquipo = TipoEquipo::find($id);

        // Verificar si el tipo de equipo existe
        if (!$tipoEquipo) {
            return redirect()->route('tipoequipo.index')->with('error', 'Tipo de equipo no encontrado');
        }

        return view('tipoequipo.edit', compact('tipoEquipo'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'TIPO_EQUIPO' => 'required|string|max:255',
        ]);

        // Buscar el tipo de equipo por su ID
        $tipoEquipo = TipoEquipo::find($id);

        // Verificar si el tipo de equipo existe
        if (!$tipoEquipo) {
            return redirect()->route('tipoequipo.index')->with('error', 'Tipo de equipo no encontrado');
        }

        // Actualizar los datos
        $tipoEquipo->TIPO_EQUIPO = $request->TIPO_EQUIPO;
        $tipoEquipo->save();
        $this->bitacora->registrarEnBitacora(18, ' tipoequipo actualizado', 'actualizado');
        return redirect()->route('tipoequipo.index')->with('success', 'Tipo de equipo actualizado correctamente');
    }

    public function destroy($id)
    {
        
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de eliminación en el objeto SOLICITUD
        $permisoEliminacion = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'TIPOEQUIPO')
                    ->limit(1);
            })
            ->where('Permiso_Eliminacion', 'PERMITIDO')
            ->exists();

        if (!$permisoEliminacion) {
            $this->bitacora->registrarEnBitacora(18, 'Intento de eliminar tipoequipo sin permisos', 'ingreso');
            return redirect()->route('tipoequipo.index')->withErrors('No tiene permiso para eliminar solicitudes');
        }
        $tipoEquipo = TipoEquipo::find($id);
    
        // Verificar si el tipo de equipo existe
        if (!$tipoEquipo) {
            return redirect()->route('tipoequipo.index')->with('error', 'Tipo de equipo no encontrado');
        }
    
        // Verificar si el tipo de equipo está protegido
        if ($tipoEquipo->PROTEGIDO) {
            return redirect()->route('tipoequipo.index')->with('error', 'No se puede eliminar este tipo de equipo porque está protegido.');
        }
    
        // Eliminar el tipo de equipo si no está protegido
        $tipoEquipo->delete();
    
        return redirect()->route('tipo_equipo.index')->with('success', 'Tipo de equipo eliminado correctamente');
    }
    public function generateReport()
    {
        $tipoEquipos = TipoEquipo::with('equipos')->get();
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));// Cambia la ruta del logo
        $fechaHora = now()->format('d-m-Y H:i:s');

        $pdf = Pdf::loadView('tipoequipo.equipos', compact('tipoEquipos', 'logoBase64', 'fechaHora'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);
        return $pdf->stream('reporte_equipos.pdf');
    }
    

    
}
