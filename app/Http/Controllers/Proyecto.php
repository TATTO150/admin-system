<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BitacoraController;
use App\Models\Proyectos;
use App\Models\Planillas;
use App\Models\Empleados;
use App\Models\EmpleadoProyectos;
use App\Models\Permisos;
use App\Models\Compras;
use App\Models\Mantenimientos;
use App\Models\Asignacion_Equipos;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Rules\Validaciones;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Models\EstadoProyecto;


class ProyectoControlador extends Controller
{
    
    protected $bitacora;
    protected $FEC_INICIO;
    protected $empleadosAsignados;

    public function __construct(BitacoraController $bitacora)
    {
        $this->bitacora = $bitacora;
    }

// En tu controlador
public function obtenerColorEstado($estado)
{
    // Colores predefinidos para los estados conocidos
    $coloresEstados = [
        'APERTURA' => 'blue',    // Azul para APERTURA
        'ACTIVO' => 'green',     // Verde para ACTIVO
        'FINALIZADO' => 'black', // Negro para FINALIZADO
        'SUSPENDIDO' => 'red',   // Rojo para SUSPENDIDO
    ];

    // Si el estado existe en la lista predefinida, devuelve el color asociado
    if (isset($coloresEstados[$estado])) {
        return $coloresEstados[$estado];
    }

    // Recupera los colores dinámicos de la sesión
    $coloresDinamicos = session('colores_dinamicos', []);

    // Si el estado ya tiene un color dinámico asignado, devuélvelo
    if (isset($coloresDinamicos[$estado])) {
        return $coloresDinamicos[$estado];
    }

    // Generar un color aleatorio, asegurando que no sea uno de los colores predefinidos
    do {
        $color = $this->generarColorAleatorio();
    } while (in_array($color, $coloresEstados));

    // Asignar el nuevo color al estado y guardarlo en la sesión
    $coloresDinamicos[$estado] = $color;
    session(['colores_dinamicos' => $coloresDinamicos]);

    return $color;
}

// Función para generar un color aleatorio en formato hexadecimal
private function generarColorAleatorio()
{
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}




public function index(Request $request)
{
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar permisos de consulta
    $permisoConsultar = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'PROYECTO')
                ->limit(1);
        })
        ->where('Permiso_Consultar', 'PERMITIDO')
        ->exists();

    if (!$permisoConsultar) {
        $this->bitacora->registrarEnBitacora(12, 'Intento de ingresar a la ventana de proyectos sin permisos', 'Consulta');
        return redirect()->route('dashboard')->withErrors('No tiene permiso para ingresar a la ventana de proyectos');
    }

    $estadoFiltro = $request->query('estado_proyecto');
    $verInactivos = $request->query('ver_inactivos') === 'true'; // Verificar si se debe mostrar inactivos
    $proyectosQuery = Proyectos::query();

    // Filtrar proyectos con estado INACTIVO si se está viendo inactivos
    if ($verInactivos) {
        $proyectosQuery->where('ESTADO_PROYECTO', 'INACTIVO');
    } else {
        $proyectosQuery->where('ESTADO_PROYECTO', '<>', 'INACTIVO');
    }

    if ($estadoFiltro) {
        $proyectosQuery->where('ESTADO_PROYECTO', $estadoFiltro);
    }

    $proyectos = $proyectosQuery->get();

    // Procesar proyectos para incluir empleados asignados
    $proyectos->each(function ($proyecto) {
        $proyecto->FEC_INICIO = !empty($proyecto->FEC_INICIO) ? Carbon::parse($proyecto->FEC_INICIO)->format('Y-m-d') : 'Fecha no disponible';
        $proyecto->FEC_FINAL = !empty($proyecto->FEC_FINAL) ? Carbon::parse($proyecto->FEC_FINAL)->format('Y-m-d') : 'Fecha no disponible';

        // Obtener los empleados asignados al proyecto
        $proyecto->empleadosAsignados = EmpleadoProyectos::where('COD_PROYECTO', $proyecto->COD_PROYECTO)
            ->pluck('DNI_EMPLEADO')
            ->toArray();
    });

    // Filtrar proyectos vencidos
    $proyectosVencidos = $proyectos->filter(function ($proyecto) {
        return Carbon::now()->gt(Carbon::parse($proyecto->FEC_FINAL)) &&
            in_array($proyecto->ESTADO_PROYECTO, ['APERTURA', 'ACTIVO', 'SUSPENDIDO']);
    });

    // Obtener empleados activos
    $empleados = Empleados::where('ESTADO_EMPLEADO', 'ACTIVO')->get();

    // Obtener los estados de proyecto desde la tabla 'tbl_estado_proyecto'
    $estadosProyecto = EstadoProyecto::where('ESTADO_PROYECTO', '<>', 'INACTIVO')->get();

    $this->bitacora->registrarEnBitacora(12, 'Ingreso a la ventana de proyectos', 'ingresar');
    return view('proyectos.index', [
        'proyectos' => $proyectos,
        'empleados' => $empleados,
        'proyectosVencidos' => $proyectosVencidos,
        'verInactivos' => $verInactivos,
        'obtenerColorEstado' => [$this, 'obtenerColorEstado'],
        'estadosProyecto' => $estadosProyecto // Pasamos los estados a la vista
    ]);
}




public function pdf()
{
    // Filtra proyectos que no estén inactivos y ordénalos por nombre de proyecto
    $proyectos = Proyectos::where('ESTADO_PROYECTO', '!=', 'INACTIVO')
                          ->orderBy('NOM_PROYECTO', 'asc')
                          ->get();

    $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
    
    // Conversión de la imagen a formato Base64 para el logo
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    
    // Generación del PDF con la vista
    $pdf = Pdf::loadView('proyectos.pdf', compact('proyectos', 'fechaHora', 'logoBase64'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);
    
    // Registrar en bitácora
    $this->bitacora->registrarEnBitacora(12, 'Ingreso al pdf general ', 'ingresar');

    // Retornar el PDF para mostrar en el navegador
    return $pdf->stream('reporte_proyectos.pdf');
}



// FUNCION PARA LOS ESTADOS 
// Método para activar un proyecto
public function activar(Request $request, $id)
{
    $proyecto = Proyectos::findOrFail($id);

    // Cambiar estado a ACTIVO
    $proyecto->ESTADO_PROYECTO = 'ACTIVO';
    $proyecto->save();

    // Retorna una respuesta JSON para actualizar la vista
    return response()->json(['success' => true, 'message' => 'Proyecto activado con éxito']);
}


public function reporteGeneral($proyectoId)
{
    // Obtener los detalles del proyecto
    $proyecto = Proyectos::find($proyectoId);

    if (!$proyecto) {
        return redirect()->back()->with('error', 'Proyecto no encontrado.');
    }

    // Obtener los empleados que trabajaron en el proyecto
    $empleadoProyectos = EmpleadoProyectos::where('COD_PROYECTO', $proyectoId)->get();

    // Obtener los detalles de cada empleado relacionado con el proyecto
    $empleados = Empleados::whereIn('DNI_EMPLEADO', $empleadoProyectos->pluck('DNI_EMPLEADO'))->get();

    // Obtener los equipos asignados al proyecto
    $equiposAsignados = Asignacion_Equipos::where('COD_PROYECTO', $proyectoId)
        ->with('equipo', 'empleado')
        ->get();

    // Obtener los IDs de los equipos asignados al proyecto
    $equiposIds = $equiposAsignados->pluck('COD_EQUIPO');

    // Obtener los gastos del proyecto
    $gastos = Compras::where('COD_PROYECTO', $proyectoId)->get();

    // Obtener la fecha y hora actual para el reporte
    $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');

    // Convertir el logo a base64 (suponiendo que tienes un logo en la carpeta public/images)
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    // Generar el PDF usando la vista pdfGeneral
    $pdf = Pdf::loadView('proyectos.pdfGeneral', compact('proyecto', 'empleados', 'fechaHora', 'logoBase64', 'equiposAsignados', 'gastos'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    // Retornar el PDF en el navegador
    return $pdf->stream('reporte_proyecto_' . $proyecto->NOM_PROYECTO . '.pdf');


    //Bitacora
    $this->bitacora->registrarEnBitacora(12, 'Ingreso a reporte general ', 'ingresar');
}




public function pdfEstado(Request $request)
{
    // Obtener el estado seleccionado del request
    $estado = $request->input('estado');

    // Filtrar los proyectos por el estado seleccionado, excluyendo los inactivos
    if ($estado == 'TODOS') {
        $proyectos = Proyectos::where('ESTADO_PROYECTO', '!=', 'INACTIVO')
            ->orderBy('ESTADO_PROYECTO')
            ->orderBy('NOM_PROYECTO')
            ->get();
    } else {
        $proyectos = Proyectos::where('ESTADO_PROYECTO', $estado)
            ->where('ESTADO_PROYECTO', '!=', 'INACTIVO')
            ->orderBy('NOM_PROYECTO')
            ->get();
    }

    // Preparar el resto de la información para el reporte
    $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    // Generar el PDF con los proyectos filtrados
    $pdf = Pdf::loadView('proyectos.pdfEstado', compact('proyectos', 'fechaHora', 'logoBase64'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    // Registrar la acción en la bitácora
    $this->bitacora->registrarEnBitacora(12, 'Ingreso a reporte por estado ', 'ingresar');

    // Enviar el PDF como respuesta al navegador
    return $pdf->stream('reporte_proyectos_estado.pdf');
}






//REPORTE POR FECHA

public function pdfFecha(Request $request)
{
    // Obtener mes y año seleccionados del request
    $mes = $request->input('mes');
    $anio = $request->input('anio');
    
    // Filtrar los proyectos por el mes y año seleccionados
    $proyectos = Proyectos::whereYear('FEC_INICIO', $anio)
        ->whereMonth('FEC_INICIO', $mes)
        ->orderBy('FEC_INICIO')
        ->get();

    // Validar si existen proyectos en la fecha seleccionada
    if ($proyectos->isEmpty()) {
        return redirect()->route('proyectos.index')->with('error', 'EN ESTA FECHA NO EXISTE EL PROYECTO');
    }
    

    // Preparar el resto de la información para el reporte
    $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    // Generar el PDF con los proyectos filtrados
    $pdf = Pdf::loadView('proyectos.pdfFecha', compact('proyectos', 'fechaHora', 'logoBase64'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    // Registrar la acción en la bitácora
    $this->bitacora->registrarEnBitacora(12, 'Ingreso al reporte por fecha', 'ingresar');

    // Enviar el PDF como respuesta al navegador
    return $pdf->stream('reporte_proyectos_fecha.pdf');
}

//REPORTE DE EMPLEADOS DE PROYECTO
/*
public function reporteEmpleados($proyectoId)
{
    // Obtener los detalles del proyecto y los empleados asignados
    $proyecto = Proyectos::findOrFail($proyectoId);
    $empleados = $proyecto->empleados; // Asumiendo que tienes una relación definida en el modelo Proyectos

    // Formatear la fecha y hora actual
    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');

    // Convertir la imagen a formato base64
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    // Cargar la vista PDF con los datos
    $pdf = Pdf::loadView('reportes.pdf_empleados', compact('proyecto', 'empleados', 'fechaHora', 'logoBase64'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    // Descargar el archivo PDF
    return $pdf->download('reporte_empleados.pdf');
}

*/

//REPORTE GENERAL Y COMPLETO; ESCOGIENDO UN PROYECTO EN ESPECIFICO

public function pdfproyecto($id)
{
    // Obtener el proyecto específico por ID
    $proyecto = Proyectos::find($id);
    
    // Verificar si el proyecto existe
    if (!$proyecto) {
        abort(404, 'Proyecto no encontrado.');
    }
    
       // Verificar el proyecto
       dd($proyecto);
       
    // Obtener la fecha y hora actual
    $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
    
    // Convertir imagen a formato base64
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    // Cargar vista del PDF con los datos del proyecto específico
    $pdf = Pdf::loadView('proyectos.pdf', compact('proyecto', 'fechaHora', 'logoBase64'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

        $this->bitacora->registrarEnBitacora(12, 'Ingreso a al reporte proyecto ', 'ingresar');

    // Mostrar el archivo PDF en el navegador
    return $pdf->stream('reporte_proyecto_' . $id . '.pdf');
}

//FUNCION CREAR
public function crear()
{
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    $permisoInsertar = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'PROYECTO')
                ->limit(1);
        })
        ->where('Permiso_Insercion', 'PERMITIDO')
        ->exists();

    if (!$permisoInsertar) {
        $this->bitacora->registrarEnBitacora(12, 'Intento de creacion de proyecto sin permisos', 'Insert');
        return redirect()->route('proyectos.index')->withErrors('No tiene permiso para crear proyectos');
    }

    return view('proyectos.crear');
}

public function insertar(Request $request)
{
    $validator = Validator::make($request->all(), [
        'NOM_PROYECTO' => [
            (new Validaciones)->requerirCampo(),
            (new Validaciones)->requerirTodoMayusculas(),
            (new Validaciones)->nombreUnico(), 
          //  (new Validaciones)->prohibirSecuenciasRepetidas() // Aplicar la nueva validación
          
        ],
        'FEC_INICIO' => [
            (new Validaciones)->requerirCampo(),
            (new Validaciones)->validarFechaNoMenorQueHoy(),
            (new Validaciones)->validarAnoActual()
        ],
        'FEC_FINAL' => [
            (new Validaciones)->requerirCampo(),
            (new Validaciones)->validarFechaFutura(),
            (new Validaciones)->validarRangoFecha($request->input('FEC_INICIO'), $request->input('FEC_FINAL'))
        ],
       'DESC_PROYECTO' => [
            (new Validaciones)->requerirCampo(),
            (new Validaciones)->requerirTodoMayusculas()
        ],
        'PRESUPUESTO_INICIO' => [
            (new Validaciones)->requerirCampo(),
            (new Validaciones)->requerirSoloNumeros(),
            (new Validaciones)->requerirValorMinimo(1)
        ],
         // Otros campos...
    ], [
        'required' => 'El campo :attribute es obligatorio.',
        'min.string' => 'La descripción debe tener al menos :min caracteres.',
        'string' => 'El campo :attribute debe ser una cadena de texto.',
        'FEC_INICIO.validarAnoActual' => 'La fecha de inicio debe ser dentro del año actual.',
        'NOM_PROYECTO.prohibirInicioConEspacio' => 'El nombre del proyecto no puede comenzar con un espacio.',
        'DESC_PROYECTO.prohibirInicioConEspacio' => 'La descripción no puede comenzar con un espacio.',
        // Otros mensajes personalizados...

    ]);


    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $response = Http::post('http://127.0.0.1:3000/INS_PROYECTOS', [
        'NOM_PROYECTO' => $request->NOM_PROYECTO,
        'FEC_INICIO' => $request->FEC_INICIO,
        'FEC_FINAL' => Carbon::now(12),
        'DESC_PROYECTO' => $request->DESC_PROYECTO,
        'PRESUPUESTO_INICIO' => $request->PRESUPUESTO_INICIO,
        'ESTADO_PROYECTO' => $request->input('ESTADO_PROYECTO', 'APERTURA'),
    ]);

    $this->bitacora->registrarEnBitacora(12, 'Se creó un nuevo proyecto', 'Insert');//BITACORA
    
    return redirect()->route('proyectos.index');
}



    

public function asignarEmpleado(Request $request)
{
    $validated = $request->validate([
        'proyecto_id' => 'required|integer|exists:tbl_proyectos,COD_PROYECTO',
        'empleados' => 'required|array',
        'empleados.*' => 'exists:tbl_empleado,COD_EMPLEADO',
    ]);

    $proyectoId = $validated['proyecto_id'];
    $empleadosCodigos = $validated['empleados'];

    // Obtener todos los empleados asignados actualmente al proyecto
    $empleadosAsignados = EmpleadoProyectos::where('COD_PROYECTO', $proyectoId)
                        ->pluck('DNI_EMPLEADO')
                        ->toArray();

    // Determinar los empleados que se deben agregar
    $empleadosAAgregar = array_diff($empleadosCodigos, $empleadosAsignados);

    // Agregar los nuevos empleados
    foreach ($empleadosAAgregar as $codigoEmpleado) {
        $empleado = Empleados::where('COD_EMPLEADO', $codigoEmpleado)->first();
        if ($empleado) {
            EmpleadoProyectos::create([
                'COD_PROYECTO' => $proyectoId,
                'DNI_EMPLEADO' => $empleado->DNI_EMPLEADO,
            ]);
        }
    }

    // No eliminamos ningún empleado existente, solo agregamos los nuevos

    $this->bitacora->registrarEnBitacora(12, 'Se asigno un empleado a proyecto', 'asigno');
    return redirect()->back()->with('success', 'Asignaciones actualizadas correctamente.');
}


public function destroy($COD_PROYECTO)
{
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de eliminación en el objeto PROYECTO
    $permisoEliminar = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'PROYECTO')
                ->limit(1);
        })
        ->where('Permiso_Eliminacion', 'PERMITIDO')
        ->exists();

    if (!$permisoEliminar) {
        $this->bitacora->registrarEnBitacora(12, 'Intento de eliminacion de proyecto sin permisos', 'Delete');
        return redirect()->route('proyectos.index')->withErrors('No tiene permiso para eliminar proyectos');
    }

    try {
        // Desasignar empleados del proyecto antes de actualizar su estado
        EmpleadoProyectos::where('COD_PROYECTO', $COD_PROYECTO)->delete();

        // Actualizar el estado del proyecto a INACTIVO
        DB::table('tbl_proyectos')
            ->where('COD_PROYECTO', $COD_PROYECTO)
            ->update(['ESTADO_PROYECTO' => 'INACTIVO']);

        $this->bitacora->registrarEnBitacora(12, 'Estado del proyecto actualizado a INACTIVO y empleados desasignados', 'Update');
        return redirect()->route('proyectos.index')->with('success', 'Estado del proyecto actualizado correctamente');
    } catch (\Exception $e) {
        Log::error('Error al actualizar estado del proyecto y desasignar empleados: ' . $e->getMessage());
        $this->bitacora->registrarEnBitacora(12, 'Proyecto deshabilitado', 'destroy');//BITACORA
        return redirect()->route('proyectos.index')->with('error', 'Error al actualizar estado del proyecto');
    }
}



public function edit($COD_PROYECTO)
{
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de actualización en el objeto PROYECTO
    $permisoActualizar = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'PROYECTO')
                ->limit(1);
        })
        ->where('Permiso_Actualizacion', 'PERMITIDO')
        ->exists();

    if (!$permisoActualizar) {
        return redirect()->route('proyectos.index')->withErrors('No tiene permiso para editar proyectos');
    }

    // Obtener los datos del proyecto desde la API
    $response = Http::get("http://127.0.0.1:3000/Proyectos/{$COD_PROYECTO}");
    $proyectos = $response->json();

    // Asegurarse de que las fechas estén formateadas correctamente para el campo de entrada de fecha
    if (isset($proyectos['FEC_FINAL'])) {
        $proyectos['FEC_FINAL'] = Carbon::parse($proyectos['FEC_FINAL'])->format('Y-m-d');
    }

    // Obtener los estados del proyecto, excluyendo "INACTIVO"
    $estadosProyecto = EstadoProyecto::where('ESTADO_PROYECTO', '<>', 'INACTIVO')->get();

    $this->bitacora->registrarEnBitacora(12, 'Proyecto Actualizado', 'Update'); //BITACORA
    return view('proyectos.edit', compact('proyectos', 'estadosProyecto'));
}




///////////////////////////////////////////////////////////////////////////////
public function store(Request $request)
{
    // Validación de datos de entrada
    $validator = Validator::make($request->all(), [
        'NOM_PROYECTO' => [
            (new Validaciones)->requerirCampo()->requerirTodoMayusculas(),
            (new Validaciones)->nombreUnico(), // Agrega la validación para el nombre único
            (new Validaciones)->prohibirInicioConEspacio()
        ],
        'FEC_INICIO' => [
            (new Validaciones)->requerirCampo()->validarFechaNoMenorQueHoy(),
            (new Validaciones)->validarAnoActual()
        ],
        'FEC_FINAL' => [
            (new Validaciones)->requerirCampo(),
            (new Validaciones)->validarFechaFutura(),
            (new Validaciones)->validarRangoFecha($request->input('FEC_INICIO'), $request->input('FEC_FINAL'))
        ],
        'DESC_PROYECTO' => [
            (new Validaciones)->requerirCampo()->requerirTodoMayusculas(),
            (new Validaciones)->prohibirInicioConEspacio()
        ],
        'PRESUPUESTO_INICIO' => [
            (new Validaciones)->requerirCampo()->requerirSoloNumeros()->requerirValorMinimo(1)
        ],
        'ESTADO_PROYECTO' 
    ]);

    if ($validator->fails()) {
        $this->bitacora->registrarEnBitacora(12, 'Activacion de validaciones', 'Update'); //BITACORA
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Continúa con el proceso de guardar el nuevo proyecto
}
///////////////////////////////////////////////////////////////////////////////////

    public function update(Request $request, $COD_PROYECTO)
{
    // Validación de datos de entrada

   $validator = Validator::make($request->all(), [
    'NOM_PROYECTO' => [
        (new Validaciones)->requerirCampo()->requerirTodoMayusculas(),
        (new Validaciones)->prohibirInicioConEspacio()
            // Agrega la validación para el nombre único, excluyendo el proyecto actual
    ],
    'FEC_INICIO' => [
        (new Validaciones)->requerirCampo()->validarFechaNoMenorQueHoy(),
        (new Validaciones)->validarAnoActual()
    ],
    'FEC_FINAL' => [
        (new Validaciones)->requerirCampo(),
        (new Validaciones)->validarFechaFutura(),
        (new Validaciones)->validarRangoFecha($request->input('FEC_INICIO'), $request->input('FEC_FINAL'))
    ],
    'DESC_PROYECTO' => [
            (new Validaciones)->requerirCampo(),
            (new Validaciones)->requerirTodoMayusculas(),
            (new Validaciones)->prohibirInicioConEspacio()
        ],
    'PRESUPUESTO_INICIO' => [
        (new Validaciones)->requerirCampo()->requerirSoloNumeros()->requerirValorMinimo(1)
    ],
    'ESTADO_PROYECTO',
]);

if ($validator->fails()) {
    return redirect()->back()->withErrors($validator)->withInput();
}

// Continúa con el proceso de actualizar el proyecto


    // Obtener el estado actual del proyecto antes de la actualización
    $responseGet = Http::get("http://127.0.0.1:3000/Proyectos/{$COD_PROYECTO}");
    $proyecto = $responseGet->json();

    if ($responseGet->successful()) {
        // Actualización del proyecto mediante una solicitud HTTP
        $response = Http::put("http://127.0.0.1:3000/Proyectos/{$COD_PROYECTO}", [
            'NOM_PROYECTO' => $request->NOM_PROYECTO,
            'FEC_INICIO' => $request->FEC_INICIO,
            'FEC_FINAL' => $request->FEC_FINAL,
            'DESC_PROYECTO' => $request->DESC_PROYECTO,
            'PRESUPUESTO_INICIO' => $request->PRESUPUESTO_INICIO,
            'ESTADO_PROYECTO' => $request->ESTADO_PROYECTO,
        ]);

       if ($response->successful()) {
            // Desasignar empleados si el proyecto está FINALIZADO o SUSPENDIDO
            if (in_array($request->ESTADO_PROYECTO, ['FINALIZADO', 'SUSPENDIDO'])) {
                EmpleadoProyectos::where('COD_PROYECTO', $COD_PROYECTO)->delete();
            }

           $this->bitacora->registrarEnBitacora(12, 'Proyecto actualizado', 'Update'); //FU
            return redirect()->route('proyectos.index');
        }
    }
    $this->bitacora->registrarEnBitacora(12, 'Proyecto actualizado', 'Update');

    return redirect()->route('proyectos.index')->withErrors('Hubo un problema al actualizar el proyecto');
}



    
public function finalizar(Request $request, $COD_PROYECTO)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de actualización en el objeto PROYECTO
        $permisoActualizar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'PROYECTO')
                    ->limit(1);
            })
            ->where('Permiso_Actualizacion', 'PERMITIDO')
            ->exists();

        if (!$permisoActualizar) {
            return redirect()->route('proyectos.index')->withErrors('No tiene permiso para finalizar proyectos');
        }

        try {
            // Obtener el proyecto actual antes de la actualización
            $responseGet = Http::get("http://127.0.0.1:3000/Proyectos/{$COD_PROYECTO}");
            $proyecto = $responseGet->json();

            if ($responseGet->successful()) {
                // Actualizar solo el estado del proyecto a FINALIZADO
                $responsePut = Http::put("http://127.0.0.1:3000/Proyectos/{$COD_PROYECTO}", [
                    'NOM_PROYECTO' => $proyecto['NOM_PROYECTO'],
                    'FEC_INICIO' => $proyecto['FEC_INICIO'],
                    'FEC_FINAL' => Carbon::now(), // Fecha actual para finalizar
                    'DESC_PROYECTO' => $proyecto['DESC_PROYECTO'],
                    'PRESUPUESTO_INICIO' => $proyecto['PRESUPUESTO_INICIO'],
                    'ESTADO_PROYECTO' => 'FINALIZADO',
                ]);

                if ($responsePut->successful()) {
                    // Desasignar empleados si el proyecto está FINALIZADO
                    EmpleadoProyectos::where('COD_PROYECTO', $COD_PROYECTO)->delete();

                    $this->bitacora->registrarEnBitacora(12, 'Proyecto finalizado', 'Update');
                    return redirect()->route('proyectos.index')->with('success', 'Proyecto finalizado correctamente');
                } else {
                    Log::error('Error al actualizar el estado del proyecto a FINALIZADO: ' . $responsePut->body());
                    $this->bitacora->registrarEnBitacora(12, 'Proyecto finalizado', 'Update');
                    return redirect()->route('proyectos.index')->withErrors('Hubo un problema al finalizar el proyecto');
                }
            } else {
                Log::error('Error al obtener el proyecto con COD_PROYECTO ' . $COD_PROYECTO . ': ' . $responseGet->body());
                $this->bitacora->registrarEnBitacora(12, 'Proyecto finalizado', 'Update');
                return redirect()->route('proyectos.index')->withErrors('No se pudo obtener el proyecto');
            }
        } catch (\Exception $e) {
            Log::error('Error al finalizar el proyecto con COD_PROYECTO ' . $COD_PROYECTO . ': ' . $e->getMessage());
            $this->bitacora->registrarEnBitacora(12, 'Proyecto finalizado', 'Update');
            return redirect()->route('proyectos.index')->with('error', 'Error al finalizar el proyecto');
        }
    }


    public function empleados()
{
 // Obtener todos los datos del modelo EmpleadoProyectos
 $empleadoProyectos = EmpleadoProyectos::with('empleado', 'solicitud')->get();
        
 // Pasar los datos a la vista
 return view('proyectos.empleado_proyectos', compact('empleadoProyectos'));
 $this->bitacora->registrarEnBitacora(12, 'Vista de empleado proyectos', 'ingresar');//BITACORA
}



public function gestionarEmpleados(Request $request, $proyectoId)
{
    // Obtener el proyecto
    $proyecto = Proyectos::findOrFail($proyectoId);

    // Recibir los DNIs seleccionados en el modal
    $empleadoDNIsSeleccionados = $request->input('empleados', []); // Los DNIs seleccionados

    // Verifica si no se seleccionaron empleados
    if (empty($empleadoDNIsSeleccionados)) {
        return redirect()->route('proyectos.index', $proyectoId)->with('error', 'No se seleccionaron empleados para eliminar.');
    }

    // Eliminar los empleados seleccionados del proyecto
    $proyecto->Empleados()->detach($empleadoDNIsSeleccionados);

    // Redirigir a la vista con un mensaje de éxito
    $this->bitacora->registrarEnBitacora(12, 'Empleado desasignado de proyecto', 'desasignar');//BITACORA

    return redirect()->route('proyectos.empleados', $proyectoId)->with('success', 'Empleados eliminados del proyecto correctamente.');
}    

}
