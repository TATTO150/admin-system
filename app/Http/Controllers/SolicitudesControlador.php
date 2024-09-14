<?php

namespace App\Http\Controllers;

use App\Models\Proyectos;
use App\Models\solitudes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Rules\Validaciones;
use App\Models\Area;
use App\Models\Empleados;
use App\Models\Permisos;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
class SolicitudesControlador extends Controller
{
    protected $guard;
    protected $createNewUser;
    protected $areas;
    protected $proyectos;
    protected $empleados;
    protected $bitacora;

    public function __construct(BitacoraController $bitacora)
    {
        $this->areas = Area::all();
        $this->proyectos = proyectos::all();
        $this->empleados = Empleados::all();
        $this->bitacora = $bitacora;

    }
    
    private function validateSolicitud(Request $request)
    {
        $messages = [
            'COD_EMPLEADO.required' => '"Nombre Empleado" es obligatorio.',
            'COD_EMPLEADO.integer' => '"Nombre Empleado" debe ser un valor entero.',
            'DESC_SOLICITUD.required' => '"Descripción Solicitud" es obligatorio.',
            'DESC_SOLICITUD.string' => '"Descripción Solicitud" debe ser una cadena de texto.',
            'DESC_SOLICITUD.min' => '"Descripción Solicitud" debe tener al menos 10 caracteres. Has ingresado: :input',
            'COD_AREA.required' => '"Nombre Área" es obligatorio.',
            'COD_AREA.integer' => '"Nombre Área" debe ser un valor entero.',
            'COD_PROYECTO.required' => '"Nombre Proyecto" es obligatorio.',
            'COD_PROYECTO.integer' => '"Nombre Proyecto" debe ser un valor entero.',
            'PRESUPUESTO_SOLICITUD.required' => '"Presupuesto Solicitud" es obligatorio.',
            'PRESUPUESTO_SOLICITUD.numeric' => '"Presupuesto Solicitud" debe ser un número. Has ingresado: :input',
            'PRESUPUESTO_SOLICITUD.min' => '"Presupuesto Solicitud" debe ser mayor o igual a 0. Has ingresado: :input',
            'PRESUPUESTO_SOLICITUD.max' => '"Presupuesto Solicitud" no debe exceder los 100,000,000. Has ingresado: :input',
        ];
    
        return Validator::make($request->all(), [
            'COD_EMPLEADO' => [
                'required',
                'integer',
            ],
            'DESC_SOLICITUD' => [
                'required',
                'string',
                'min:10',
                function($attribute, $value, $fail) {
                    if (preg_match('/([A-Z])\1{2,}/', $value)) {
                        $fail('"Descripción Solicitud" no puede tener más de dos letras seguidas. Has ingresado: ' . $value);
                    }
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('"Descripción Solicitud" no puede tener más de un espacio seguido. Has ingresado: ' . $value);
                    }
                    if (preg_match('/[BCDFGHJKLMNPQRSTVWXYZ]{5}/i', $value)) {
                        $fail('"Descripción Solicitud" no puede tener más de cuatro consonantes seguidas en una palabra. Has ingresado: ' . $value);
                    }
                    if (preg_match('/\b\w+\b[\W_]+$/', $value)) {
                        $fail('"Descripción Solicitud" no puede contener símbolos después de una palabra. Has ingresado: ' . $value);
                    }
                },
            ],
            'COD_AREA' => [
                'required',
                'integer',
            ],
            'COD_PROYECTO' => [
                'required',
                'integer',
            ],
            'PRESUPUESTO_SOLICITUD' => [
                'required',
                'numeric',
                'min:0',
                'max:100000000',
            ],
        ], $messages);
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
                ->where('Objeto', 'SOLICITUD')
                ->limit(1);
        })
        ->where('Permiso_Consultar', 'PERMITIDO')
        ->exists();

    if (!$permisoConsultar) {
        $this->bitacora->registrarEnBitacora(22, 'Intento de ingreso a la ventana de solicitudes sin permisos', 'Ingreso');
        return redirect()->route('dashboard')->withErrors('No tiene permiso para consultar solicitudes');
    }

    $response = Http::get('http://127.0.0.1:3000/Solicitudes');
    $solicitudes = solitudes::with(['empleado', 'area', 'proyecto'])->get();

    $empleados = \App\Models\Empleados::all()->keyBy('COD_EMPLEADO');
    $proyectos = \App\Models\Proyectos::all()->keyBy('COD_PROYECTO');
    $areas = \App\Models\Area::all()->keyBy('COD_AREA');
    $this->bitacora->registrarEnBitacora(22, 'Ingreso a la ventana de solicitudes', 'Ingreso');

    // Asegúrate de usar el nombre correcto de las variables
    return view('solicitudes.index', compact('solicitudes', 'empleados', 'proyectos', 'areas'));
}


    public function pdf()
    {
        // Configura el tiempo máximo de ejecución
        ini_set('max_execution_time', 120); // 2 minutos

        $areas = Area::all();
        $proyectos = Proyectos::all();
        $solicitudes = solitudes::all();
        $empleados = Empleados::all();
        //fecha
        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
        
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

        $pdf = Pdf::loadView('solicitudes.pdf', compact('solicitudes', 'empleados', 'areas', 'proyectos', 'fechaHora', 'logoBase64'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
            ]);
            $this->bitacora->registrarEnBitacora(22, 'Generacion de reporte de solicitudes', 'Update');
        return $pdf->stream('reporte_solicitudes.pdf');
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
                    ->where('Objeto', 'SOLICITUD')
                    ->limit(1);
            })
            ->where('Permiso_Insercion', 'PERMITIDO')
            ->exists();

        if (!$permisoInsercion) {
            return redirect()->route('solicitudes.index')->withErrors('No tiene permiso para crear solicitudes');
        }

        $proyectos = Proyectos::whereNotIn('ESTADO_PROYECTO', ['SUSPENDIDO', 'FINALIZADO', 'INACTIVO'])->get();
        $empleados = empleados::where('ESTADO_EMPLEADO', 'ACTIVO')->get();
        $areas = Area::all();

         // Concatenar DNI con nombre del empleado
        $empleados = $empleados->map(function ($empleado) {
        $empleado->nombre_con_dni = $empleado->DNI_EMPLEADO. ' - ' . $empleado->NOM_EMPLEADO;
        return $empleado;
    });

        return view('solicitudes.crear', compact('areas', 'proyectos', 'empleados'));
    }

    public function insertar(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'COD_EMPLEADO' => [
                (new Validaciones)->requerirCampo()->requerirSoloNumeros(),
            ],
            'DESC_SOLICITUD' => [
                (new Validaciones)->requerirCampo()->requerirTodoMayusculas()->prohibirMultiplesEspacios()->prohibirEspaciosInicioFin(),
                function($attribute, $value, $fail) {
                    if (preg_match('/\b\w+[^\w\s]+/', $value)) {
                        $fail('El campo "Descripción de Solicitud" no puede contener símbolos después de una palabra.');
                    }
                    if (preg_match('/([A-Z])\1{2,}/', $value)) {
                        $fail('"Descripción Solicitud" no puede tener más de dos letras seguidas. Has ingresado: ' . $value);
                    }
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('"Descripción Solicitud" no puede tener más de un espacio seguido. Has ingresado: ' . $value);
                    }
                    if (preg_match('/[BCDFGHJKLMNPQRSTVWXYZ]{5}/i', $value)) {
                        $fail('"Descripción Solicitud" no puede tener más de cuatro consonantes seguidas en una palabra. Has ingresado: ' . $value);
                    }
                },
            ],
            'COD_AREA' => [
                (new Validaciones)->requerirCampo()->requerirSoloNumeros(),
            ],
            'COD_PROYECTO' => [
                (new Validaciones)->requerirCampo()->requerirSoloNumeros()->requerirEstadoValidoProyecto(),
            ],
            'PRESUPUESTO_SOLICITUD' => [
                (new Validaciones)->requerirCampo()->prohibirCeroYNegativos()->requerirSinEspacios()->prohibirSimbolosSalvoDecimal(),
            ],
        ], [
            'COD_EMPLEADO.required' => 'El "Código de Empleado" es obligatorio.',
            'DESC_SOLICITUD.required' => 'La "Descripción de Solicitud" es obligatoria.',
            'COD_AREA.required' => 'El "Código de Área" es obligatorio.',
            'COD_PROYECTO.required' => 'El "Código de Proyecto" es obligatorio.',
            'PRESUPUESTO_SOLICITUD.required' => 'El "Presupuesto de Solicitud" es obligatorio.',
        ], [
            'COD_EMPLEADO' => 'Código de Empleado',
            'DESC_SOLICITUD' => 'Descripción de Solicitud',
            'COD_AREA' => 'Código de Área',
            'COD_PROYECTO' => 'Código de Proyecto',
            'PRESUPUESTO_SOLICITUD' => 'Presupuesto de Solicitud',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $response = Http::post('http://127.0.0.1:3000/INS_SOLICITUDES', [
            'COD_EMPLEADO' => $request->COD_EMPLEADO,
            'DESC_SOLICITUD' => $request->DESC_SOLICITUD,
            'COD_AREA' => $request->COD_AREA,
            'COD_PROYECTO' => $request->COD_PROYECTO,
            'ESTADO_SOLICITUD' => 'ESPERA',
            'PRESUPUESTO_SOLICITUD' => $request->PRESUPUESTO_SOLICITUD,
        ]);
    
        $this->bitacora->registrarEnBitacora(22, 'Nueva solicitudes creada', 'insertar');
        return redirect()->route('solicitudes.index');
    }
    


    public function destroy($COD_SOLICITUD)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de eliminación en el objeto SOLICITUD
        $permisoEliminacion = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'SOLICITUD')
                    ->limit(1);
            })
            ->where('Permiso_Eliminacion', 'PERMITIDO')
            ->exists();

        if (!$permisoEliminacion) {
            $this->bitacora->registrarEnBitacora(22, 'Intento de eliminar solicitudes sin permisos', 'ingreso');
            return redirect()->route('dashboard')->withErrors('No tiene permiso para eliminar solicitudes');
        }

        try {
            // Llamada al procedimiento almacenado para eliminar la solicitud
            DB::statement('CALL ELI_SOLICITUDES(?)', [$COD_SOLICITUD]);
            $this->bitacora->registrarEnBitacora(22, 'solicitudes eliminada', 'eliminada');
            // Registro en bitácora (si se utiliza)
            // $this->bitacora->registrarEnBitacora(Auth::id(), 5, 'Solicitud eliminada', 'Delete'); // ID_objetos 5: 'solicitudes'

            return redirect()->route('solicitudes.index')->with('success', 'Solicitud eliminada correctamente');
        } catch (\Exception $e) {
            $this->bitacora->registrarEnBitacora(22, 'Error al eliminar solicitudes', 'eliminada');
            return redirect()->route('solicitudes.index')->with('error', 'Error al eliminar solicitud: ' . $e->getMessage());
        }
    }

    public function edit($COD_SOLICITUD)
{
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar permiso
    $permisoActualizacion = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'SOLICITUD')
                ->limit(1);
        })
        ->where('Permiso_Actualizacion', 'PERMITIDO')
        ->exists();

    if (!$permisoActualizacion) {
        return redirect()->route('solicitudes.index')->withErrors('No tiene permiso para editar solicitudes');
    }

    // Obtener datos del formulario
    $areas = Area::all();
    $proyectos = Proyectos::all();
    $empleados = Empleados::all();
    $solicitud = Solitudes::where('COD_SOLICITUD', $COD_SOLICITUD)->first(); // Usar first() para obtener un único modelo

    if (!$solicitud) {
        return redirect()->route('solicitudes.index')->withErrors('Solicitud no encontrada');
    }

    // Concatenar DNI con nombre del empleado
    $empleados = $empleados->map(function ($empleado) {
        $empleado->nombre_con_dni = $empleado->DNI_EMPLEADO. ' - ' . $empleado->NOM_EMPLEADO;
        return $empleado;
    });

    return view('solicitudes.edit', compact('solicitud', 'areas', 'proyectos', 'empleados'));
}


    public function update(Request $request, $COD_SOLICITUD)
{
    // Validación de datos
    $validator = Validator::make($request->all(), [
        'COD_EMPLEADO' => [
            (new Validaciones)->requerirCampo()->requerirSoloNumeros(),
            'required',
            'integer',
        ],
        'DESC_SOLICITUD' => [
            (new Validaciones)->requerirCampo()->requerirTodoMayusculas()
                ->prohibirMultiplesEspacios()
                ->prohibirEspaciosInicioFin(),
            'required',
            'string',
            'min:10',
            function($attribute, $value, $fail) {
                if (preg_match('/([A-Z])\1{2,}/', $value)) {
                    $fail('"Descripción Solicitud" no puede tener más de dos letras seguidas. Has ingresado: ' . $value);
                }
                if (preg_match('/\s{2,}/', $value)) {
                    $fail('"Descripción Solicitud" no puede tener más de un espacio seguido. Has ingresado: ' . $value);
                }
                if (preg_match('/[BCDFGHJKLMNPQRSTVWXYZ]{5}/i', $value)) {
                    $fail('"Descripción Solicitud" no puede tener más de cuatro consonantes seguidas en una palabra. Has ingresado: ' . $value);
                }
                if (preg_match('/\b\w+\b[\W_]+$/', $value)) {
                    $fail('"Descripción Solicitud" no puede contener símbolos después de una palabra. Has ingresado: ' . $value);
                }
            },
        ],
        'COD_AREA' => [
            (new Validaciones)->requerirCampo()->requerirSoloNumeros(),
            'required',
            'integer',
        ],
        'COD_PROYECTO' => [
            (new Validaciones)->requerirCampo()->requerirSoloNumeros()
                ->requerirEstadoValidoProyecto(),
            'required',
            'integer',
        ],
        'PRESUPUESTO_SOLICITUD' => [
            (new Validaciones)->requerirCampo()->requerirSoloNumeros()
                ->prohibirCeroYNegativos()
                ->requerirSinEspacios()
                ->prohibirSimbolosSalvoDecimal(),
            'required',
            'numeric',
            'min:0',
            'max:100000000',
        ],
    ], [], [
        'COD_EMPLEADO' => 'Código de Empleado',
        'DESC_SOLICITUD' => 'Descripción de Solicitud',
        'COD_AREA' => 'Código de Área',
        'COD_PROYECTO' => 'Código de Proyecto',
        'PRESUPUESTO_SOLICITUD' => 'Presupuesto de Solicitud',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $response = Http::put("http://127.0.0.1:3000/Solicitudes/{$COD_SOLICITUD}", [
        'COD_EMPLEADO' => $request->COD_EMPLEADO,
        'DESC_SOLICITUD' => $request->DESC_SOLICITUD,
        'COD_AREA' => $request->COD_AREA,
        'COD_PROYECTO' => $request->COD_PROYECTO,
        'ESTADO_SOLICITUD' => 'ESPERA',
        'PRESUPUESTO_SOLICITUD' => $request->PRESUPUESTO_SOLICITUD,
    ]);

    /* if ($response->successful()) {
        $this->bitacora->registrarEnBitacora(Auth::id(), 5, 'Solicitud actualizada', 'Update'); // ID_objetos 5: 'solicitudes'
    */

    return redirect()->route('solicitudes.index');
}

public function generateReport(Request $request)
{
    // Definir la consulta básica con relaciones
    $query = Solitudes::with(['empleado:COD_EMPLEADO,NOM_EMPLEADO', 'area:COD_AREA,NOM_AREA', 'proyecto:COD_PROYECTO,NOM_PROYECTO']);

    // Aplicar filtros según el tipo de reporte seleccionado
    $reportType = $request->input('reportType', 'general');

    switch ($reportType) {
        case 'proyecto':
            if ($request->has('proyecto')) {
                $query->where('COD_PROYECTO', $request->input('proyecto'));
            }
            break;
        case 'area':
            if ($request->has('area')) {
                $query->where('COD_AREA', $request->input('area'));
            }
            break;
        default:
            // No se aplican filtros adicionales para el reporte general
            break;
    }

    // Generar una clave de caché basada en la consulta SQL y sus parámetros
    $cacheKey = 'reporte_' . md5($query->toSql() . serialize($query->getBindings()));

    // Cachear los datos de la consulta
    $solicitudes = Cache::remember($cacheKey, 60, function() use ($query) {
        return $query->get();
    });

    // Si no hay registros, retornar un mensaje de error específico
    if ($solicitudes->isEmpty()) {
        $message = match($reportType) {
            'proyecto' => 'No se encontraron registros para el reporte por proyecto seleccionado.',
            'area' => 'No se encontraron registros para el reporte por área seleccionada.',
            default => 'No se encontraron registros para el reporte general.',
        };

        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], 400);
    }

    // Cargar las demás colecciones necesarias
    $areas = Area::all(['COD_AREA', 'NOM_AREA']);
    $proyectos = Proyectos::all(['COD_PROYECTO', 'NOM_PROYECTO']);
    $empleados = Empleados::all(['COD_EMPLEADO', 'NOM_EMPLEADO']);

    // Generar el PDF usando los datos cacheados
    $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    $pdf = Pdf::loadView('solicitudes.reporte_general', compact('solicitudes', 'empleados', 'areas', 'proyectos', 'fechaHora', 'logoBase64'))
        ->setPaper('a3', 'landscape')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    // Devolver el PDF para ser descargado o visualizado en el navegador
    return $pdf->stream('reporte_solicitudes_' . $reportType . '.pdf');
}







    public function reporteGeneral()
    {
        $query = Solitudes::with(['empleado', 'area', 'proyecto']);
        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

        $pdf = Pdf::loadView('solicitudes.reporte_general', compact('solicitudes', 'empleados', 'areas', 'proyectos', 'fechaHora', 'logoBase64'))
        ->setPaper('a3', 'landscape')    
        ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
            ]);
            $this->bitacora->registrarEnBitacora(22, 'Generacion de reporte general solicitudes', 'Update');
        return $pdf->stream('reporte_solicitudes_general.pdf');
    }

    public function generarReporte(Request $request)
{
    $query = Solitudes::with(['empleado', 'area', 'proyecto']);

    // Filtrar según el tipo de reporte seleccionado
    if ($request->has('reportType')) {
        $reportType = $request->input('reportType');

        if ($reportType === 'proyecto' && $request->has('proyecto')) {
            $query->where('COD_PROYECTO', $request->input('proyecto'));
        }

        if ($reportType === 'area' && $request->has('area')) {
            $query->where('COD_AREA', $request->input('area'));
        }
    }

    // Obtener los datos filtrados
    $solicitudes = $query->get();
    $areas = Area::all();
    $proyectos = Proyectos::all();
    $empleados = Empleados::all();

    $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    $pdf = Pdf::loadView('solicitudes.reporte_general', compact('solicitudes', 'empleados', 'areas', 'proyectos', 'fechaHora', 'logoBase64'))
        ->setPaper('a3', 'landscape')    
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    // Registrar en la bitácora
    $this->bitacora->registrarEnBitacora(22, 'Generación de reporte filtrado de solicitudes', 'Update');

    return $pdf->stream();
}


    public function reportePorEstado()
    {
        $solicitudes = solitudes::with(['empleado', 'area', 'proyecto'])->get();
        $areas = Area::all();
        $proyectos = Proyectos::all();
        $empleados = Empleados::all();

        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

        $pdf = Pdf::loadView('solicitudes.reporte_estado', compact('solicitudes', 'empleados', 'areas', 'proyectos', 'fechaHora', 'logoBase64'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
            ]);
            $this->bitacora->registrarEnBitacora(22, 'Generacion de reporte estado de solicitudes', 'Update');
        return $pdf->stream('reporte_solicitudes_estado.pdf');
    }

    
    public function reportePorProyecto()
    {
        $proyectos = Proyectos::with('solicitudes')->get();
    
        dd($proyectos); // Verifica el contenido
    
        if ($proyectos->isEmpty()) {
            return redirect()->back()->with('error', 'No hay proyectos disponibles para generar el reporte.');
        }
    
        return view('reportes.proyecto', [
            'proyectos' => $proyectos,
            'logoBase64' => '', // Proporciona el logo en base64 si lo tienes
            'fechaHora' => now()->format('d/m/Y H:i:s')
        ]);
    }
    
    

    
    public function reportePorArea()
{
    $areas = area::all();
    dd($areas); // Esto mostrará los datos de áreas y detendrá la ejecución
    return view('solicitudes.index', compact('areas'));
}

    }


