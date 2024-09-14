<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EstadoProyecto;
use App\Models\Proyectos;
use App\Models\Permisos;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;  
use App\Http\Controllers\BitacoraController;  //BITACORA

class EstadoProyectoControllador extends Controller
{

    protected $bitacora;


    public function __construct(BitacoraController $bitacora)
    {
        $this->bitacora = $bitacora;
    }

    public function index()
    {
        $estados = EstadoProyecto::where('ESTADO_PROYECTO', '!=', 'INACTIVO')->get();
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Verificar permisos de consulta
        $permisoConsultar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'ESTADOPROYECTO')
                    ->limit(1);
            })
            ->where('Permiso_Consultar', 'PERMITIDO')
            ->exists();
    
        if (!$permisoConsultar) {
            return redirect()->route('dashboard')->withErrors('No tiene permiso para consultar estado proyecto');
        }
        $this->bitacora->registrarEnBitacora(12, 'Ingreso a la ventana de estado proyecto', 'ingresar');
        return view('estado_proyecto.index', compact('estados'));
    }
    
    public function generatePdf()
    {
        // Obtén los estados de proyecto que no estén inactivos
        $estados = EstadoProyecto::where('ESTADO_PROYECTO', '!=', 'INACTIVO')->get();
    
        // Obtén la fecha y hora actual
        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
    
        // Conversión de la imagen a formato Base64 para el logo
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    
        // Genera el PDF con la vista y las variables
        $pdf = Pdf::loadView('estado_proyecto.pdf', compact('estados', 'fechaHora', 'logoBase64'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
            ]);
    // Registrar en bitácora
    $this->bitacora->registrarEnBitacora(12, 'Ingreso al reporte de estado proyecto', 'ingresar');
        // Retorna el PDF para mostrar en el navegador
        return $pdf->stream('estado_proyecto.pdf');
    }
    

    public function create()
    {
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////
        return view('estado_proyecto.create');
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
                $this->bitacora->registrarEnBitacora(12, 'Se creó un nuevo estado de proyecto', 'Insert');//BITACORA
            if (!$permisoInsercion) {
                return redirect()->route('estado_proyecto.index')->withErrors('No tiene permiso para crear un estado de proyecto');
            }
            return view('estado_proyecto.create');
    }

    protected function validateEstadoProyecto(Request $request)
{
    $request->validate([
        'ESTADO_PROYECTO' => [
            'required',
            'string',
            'max:255',
            function ($attribute, $value, $fail) {
                // Verificar que el campo esté en mayúsculas
                if ($value !== strtoupper($value)) {
                    $fail('El campo ' . $attribute . ' debe estar en mayúsculas.');
                }

                // Verificar que el campo solo contenga letras en mayúsculas y espacios
                if (!preg_match('/^[A-Z\s]+$/', $value)) {
                    $fail('El campo ' . $attribute . ' solo puede contener letras en mayúsculas y espacios, sin números ni símbolos.');
                }

                // Verificar que no haya secuencias de más de 3 caracteres repetidos
                if (preg_match('/(.)\1{3,}/', $value)) {
                    $fail('El campo ' . $attribute . ' no puede contener secuencias de más de 3 caracteres repetidos consecutivos.');
                }
// Verificar unicidad del nombre en la base de datos
if (\App\Models\EstadoProyecto::where('ESTADO_PROYECTO', $value)->exists()) {
    $fail('El nombre del estado ya existe.');
}
                // Verificar que la cadena no sea excesivamente larga sin espacios ni que no parezca un texto significativo
                if (strlen($value) > 15 && !preg_match('/\s/', $value)) {
                    $fail('El campo ' . $attribute . ' no puede ser una cadena larga sin espacios o una secuencia de caracteres aparentemente aleatoria.');
                }
            },
        ],
    ]);
}
    

public function store(Request $request)
{
    // Validar el estado del proyecto
    $this->validateEstadoProyecto($request);

    // Definir los estados protegidos
    $estadosProtegidos = ['APERTURA', 'ACTIVO', 'SUSPENDIDO', 'FINALIZADO', 'INACTIVO'];

    // Verificar si el estado a crear está en los protegidos
    if (in_array($request->ESTADO_PROYECTO, $estadosProtegidos)) {
        return redirect()->route('estado_proyecto.create')
                         ->withInput()  // Mantiene los valores del formulario
                         ->with('error', 'No se puede crear un estado de proyecto con este nombre.');
    }

    EstadoProyecto::create($request->all());

    return redirect()->route('estado_proyecto.index')
                     ->with('success', 'Estado del proyecto creado con éxito.');
}

    public function edit(EstadoProyecto $estadoProyecto)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;
                // Verificar permiso
                $permisoActualizacion = Permisos::where('Id_Rol', $roleId)
                ->where('Id_Objeto', function ($query) {
                    $query->select('Id_Objetos')
                        ->from('tbl_objeto')
                        ->where('Objeto', 'ESTADOPROYECTO')
                        ->limit(1);
                })
                ->where('Permiso_Actualizacion', 'PERMITIDO')
                ->exists();
        
            if (!$permisoActualizacion) {
                $this->bitacora->registrarEnBitacora(12, 'Se actualizo un estado proyecto', 'Update'); //BITACORA
                return redirect()->route('estado_proyecto.index')->withErrors('No tiene permiso para editar tipoe equipo');
            }

        return view('estado_proyecto.edit', compact('estadoProyecto'));
    }

    public function update(Request $request, EstadoProyecto $estadoProyecto)
    {
        // Validar el estado del proyecto
        $this->validateEstadoProyecto($request);
    
        // Encuentra el estado anterior para todos los proyectos asociados
        $estadoAnterior = $estadoProyecto->ESTADO_PROYECTO;
    
        // Actualiza el estado en todos los proyectos que tienen el estado anterior
        Proyectos::where('ESTADO_PROYECTO', $estadoAnterior)
                 ->update(['ESTADO_PROYECTO' => $request->input('ESTADO_PROYECTO')]);
    
        // Actualiza el estado del estadoProyecto
        $estadoProyecto->update($request->all());
    
        return redirect()->route('estado_proyecto.index')
                         ->with('success', 'Estado del proyecto actualizado con éxito.');
    }
    
    public function destroy(EstadoProyecto $estadoProyecto)
    {
        // Verificar si el estado está asociado a algún proyecto
        $proyectosConEstado = Proyectos::where('ESTADO_PROYECTO', $estadoProyecto->ESTADO_PROYECTO)->count();
    
        if ($proyectosConEstado > 0) {
            return redirect()->route('estado_proyecto.index')
                             ->with('error', 'Este estado de proyecto está asociado a un proyecto y no se puede eliminar.');
        }
    
        // Elimina el estado del proyecto
        $estadoProyecto->delete();
        $this->bitacora->registrarEnBitacora(12, 'Proyecto elimino un estado de proyecto', 'destroy');//BITACORA
        return redirect()->route('estado_proyecto.index')
                         ->with('success', 'Estado del proyecto eliminado con éxito.');
    }
    
}

