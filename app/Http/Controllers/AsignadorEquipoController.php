<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Proyectos;
use App\Models\Empleados;
use App\Models\Equipos;
use App\Models\EstadoAsignacion;
use Illuminate\Support\Facades\Auth;
use App\Models\Permisos;
use App\Models\Asignacion_Equipos;
use App\Models\TipoAsignacion;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 
class AsignadorEquipoController extends Controller
{
    protected $apiBaseUrl = 'http://localhost:3000';
    protected $bitacora;
    protected $equipos;
    protected $empleados;
    protected $proyectos;
    protected  $estadosAsignacion;

    public function __construct(BitacoraController $bitacora)
    {
        $this->bitacora = $bitacora;
        $this->proyectos = proyectos::all();
        $this->empleados = Empleados::all();
        $this->equipos = Equipos::all();
        $this->estadosAsignacion = EstadoAsignacion::all();
    }


    // Función para obtener datos de la API
    private function fetchApiData($endpoint)
    {
        $response = Http::get("{$this->apiBaseUrl}/{$endpoint}");
        return $response->successful() ? $response->json() : [];
    }

    // Validación de datos de asignación
// Validación de datos de asignación
private function validateAsignacion(Request $request)
{
    return Validator::make($request->all(), [
        'COD_EQUIPO' => [
            'required', 
            'integer'
        ],
        'COD_EMPLEADO' => [
            'required', 
            'integer'
        ],
        'DESCRIPCION' => [
            'required',
            'string',
            'min:10',
            function($attribute, $value, $fail) {
                // No permitir más de 2 veces la misma letra consecutivamente
                if (preg_match('/([a-zA-Z])\1{2,}/', $value)) {
                    $fail('El campo "Descripción" no puede tener más de dos letras iguales consecutivas. Has ingresado: ' . $value);
                }
                // No permitir más de un espacio seguido
                if (preg_match('/\s{2,}/', $value)) {
                    $fail('El campo "Descripción" no puede tener más de un espacio seguido. Has ingresado: ' . $value);
                }
                // Verificar que no haya más de 4 consonantes seguidas
                if (preg_match('/[BCDFGHJKLMNPQRSTVWXYZ]{5}/i', $value)) {
                    $fail('El campo "Descripción" no puede tener más de cuatro consonantes seguidas en una palabra. Has ingresado: ' . $value);
                }
                // Verificar que solo contenga mayúsculas
                if (!preg_match('/^[A-Z0-9\s]*$/', $value)) {
                    $fail('El campo "Descripción" solo puede contener letras mayúsculas y números. Has ingresado: ' . $value);
                }
                // No permitir que termine con un carácter especial
                if (preg_match('/[!@#$%^&*(),.?":{}|<>]$/', $value)) {
                    $fail('El campo "Descripción" no puede finalizar con un carácter especial. Has ingresado: ' . $value);
                }
                // No permitir que inicie con un número
                if (preg_match('/^\d/', $value)) {
                    $fail('El campo "Descripción" no puede iniciar con un número. Has ingresado: ' . $value);
                }
                // No permitir que inicie con un carácter especial
                if (preg_match('/^[!@#$%^&*(),.?":{}|<>]/', $value)) {
                    $fail('El campo "Descripción" no puede iniciar con un carácter especial. Has ingresado: ' . $value);
                }
                // No permitir más de 3 vocales seguidas
                if (preg_match('/[AEIOU]{4}/i', $value)) {
                    $fail('El campo "Descripción" no puede tener más de tres vocales seguidas. Has ingresado: ' . $value);
                }
            },
        ],
        'FECHA_ASIGNACION_INICIO' => [
            'required',
            'date',
            function($attribute, $value, $fail) {
                $fechaInicio = Carbon::parse($value);
                $fechaActual = Carbon::now();
                $unMesAntes = $fechaActual->copy()->subMonth();
                $unMesDespues = $fechaActual->copy()->addMonth();

                if ($fechaInicio->lessThan($unMesAntes)) {
                    $fail('La "Fecha de Inicio de Asignación" no puede ser anterior a un mes antes de la fecha actual. Has ingresado: ' . $fechaInicio->format('Y-m-d'));
                }

                if ($fechaInicio->greaterThan($unMesDespues)) {
                    $fail('La "Fecha de Inicio de Asignación" no puede ser mayor a un mes desde la fecha actual. Has ingresado: ' . $fechaInicio->format('Y-m-d'));
                }
            },
        ],
        'FECHA_ASIGNACION_FIN' => [
            'nullable', 
            'date'
        ],
        'COD_PROYECTO' => [
            'nullable', 
            'integer'
        ],
    ], [
        'COD_EQUIPO.required' => 'El campo "Código de Equipo" es obligatorio.',
        'COD_EQUIPO.integer' => 'El campo "Código de Equipo" debe ser un número entero.',
        'COD_EMPLEADO.required' => 'El campo "Código de Empleado" es obligatorio.',
        'COD_EMPLEADO.integer' => 'El campo "Código de Empleado" debe ser un número entero.',
        'DESCRIPCION.required' => 'El campo "Descripción" es obligatorio.',
        'DESCRIPCION.min' => 'El campo "Descripción" debe tener al menos 10 caracteres.',
        'FECHA_ASIGNACION_INICIO.required' => 'El campo "Fecha de Inicio de Asignación" es obligatorio.',
        'FECHA_ASIGNACION_INICIO.date' => 'El campo "Fecha de Inicio de Asignación" debe ser una fecha válida.',
        'FECHA_ASIGNACION_FIN.date' => 'El campo "Fecha de Finalización de Asignación" debe ser una fecha válida.',
        'COD_PROYECTO.integer' => 'El campo "Código de Proyecto" debe ser un número entero válido.',
    ]);
}

public function index(Request $request)
{
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    $permisoConsultar = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'ASIGNACION_EQUIPO')
                ->limit(1);
        })
        ->where('Permiso_Consultar', 'PERMITIDO')
        ->exists();

    if (!$permisoConsultar) {
        $this->bitacora->registrarEnBitacora(19, 'Intento de ingreso a la ventana de asignacion sin permisos', 'Ingreso');
        return redirect()->route('dashboard')->withErrors('No tiene permiso para ingresar a la ventana de áreas');
    }

    // Obtener los datos utilizando los modelos Eloquent
    $asignaciones = Asignacion_Equipos::with(['empleado', 'equipo', 'proyectos', 'estado_asignacion', 'tipoAsignacion'])->get();
    $equipos = Equipos::all();
    $empleados = Empleados::all();
    
  
    // Obtener solo los empleados que están en la tabla de asignaciones
    $empleadosAsignados = Empleados::whereIn('COD_EMPLEADO', function($query) {
        $query->select('COD_EMPLEADO')->from('tbl_equipo_asignacion');
    })->distinct()->get();

    // Obtener todos los proyectos
    $proyectos = Proyectos::all();

    // Filtrar proyectos asignados (diferentes de COD_PROYECTO = 0)
    $proyectosAsignados = Proyectos::whereIn('COD_PROYECTO', function($query) {
        $query->select('COD_PROYECTO')
              ->from('tbl_equipo_asignacion')
              ->where('COD_PROYECTO', '!=', 0);
    })->distinct()->get();

    $estadosAsignacion = EstadoAsignacion::all();
    $tiposAsignacion = TipoAsignacion::all();

    // Aplicar filtro por estado si se proporciona
    $codEstadoAsignacionFiltro = $request->query('cod_estado_asignacion');
    if ($codEstadoAsignacionFiltro) {
        $asignaciones = $asignaciones->where('COD_ESTADO_ASIGNACION', $codEstadoAsignacionFiltro);
    }

    // Ordenar las asignaciones por COD_ESTADO_ASIGNACION ascendente y por FECHA_ASIGNACION_INICIO descendente
    $asignaciones = $asignaciones->sortBy([
        ['COD_ESTADO_ASIGNACION', 'asc'],
        ['FECHA_ASIGNACION_INICIO', 'desc'],
    ]);

    // Formatear fechas y agregar información adicional
    $asignaciones->each(function ($asignacion) {
        $asignacion->FECHA_ASIGNACION_INICIO = $asignacion->FECHA_ASIGNACION_INICIO ? Carbon::parse($asignacion->FECHA_ASIGNACION_INICIO)->format('Y/m/d') : 'Fecha no disponible';

        if (empty($asignacion->FECHA_ASIGNACION_FIN) || $asignacion->FECHA_ASIGNACION_FIN == '0000-00-00') {
            $asignacion->FECHA_ASIGNACION_FIN = '-';
        } else {
            $asignacion->FECHA_ASIGNACION_FIN = Carbon::parse($asignacion->FECHA_ASIGNACION_FIN)->format('Y/m/d');
        }

        $asignacion->NOM_EQUIPO = $asignacion->equipo->NOM_EQUIPO ?? 'Desconocido';
        $asignacion->NOM_EMPLEADO = $asignacion->empleado->NOM_EMPLEADO ?? 'Desconocido';

        if ($asignacion->COD_PROYECTO == 0) {
            $asignacion->NOM_PROYECTO = '-';
        } else {
            $asignacion->NOM_PROYECTO = $asignacion->proyectos->NOM_PROYECTO ?? 'Desconocido';
        }

        $asignacion->ESTADO_ASIGNACION = $asignacion->estado_asignacion->ESTADO ?? 'Desconocido';

        // Agregar el nombre del tipo de asignación
        $asignacion->TIPO_ASIGNACION_NOMBRE = $asignacion->tipoAsignacion->TIPO_ASIGNACION ?? 'Desconocido';

        $asignacion->mostrar_finalizar = in_array($asignacion->COD_ESTADO_ASIGNACION, [1]);
    });

    $estadosAsignacionLimitados = $estadosAsignacion->take(4);

    $this->bitacora->registrarEnBitacora(19, 'Ingreso a la ventana de asignacion', 'Ingreso');

    return view('asignaciones.prueba', [
        'asignaciones' => $asignaciones,
        'equipos' => $equipos,
        'empleados' => $empleados, // Todos los empleados
        'empleadosAsignados' => $empleadosAsignados, // Solo los empleados asignados
        'proyectos' => $proyectos, // Proyectos completos
        'proyectosAsignados' => $proyectosAsignados, // Proyectos con asignaciones válidas
        'estadosAsignacion' => $estadosAsignacionLimitados,
        'tiposAsignacion' => $tiposAsignacion, // Añadir la variable a la vista
    ]);
}

    
    // Mostrar una asignación específica
    public function show($id)
    {
        $asignacion = $this->fetchApiData("Asignaciones/{$id}");
        if (empty($asignacion)) {
            return redirect()->route('asignaciones.index')->with('error', 'Asignación no encontrada');
        }
        return view('asignaciones.show', compact('asignacion'));
    }

    // Mostrar el formulario para crear una nueva asignación
    public function create(Request $request)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Verificar si el rol del usuario tiene el permiso de inserción en el objeto ASIGNACION_EQUIPO
        $permisoInsertar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'ASIGNACION_EQUIPO')
                    ->limit(1);
            })
            ->where('Permiso_Insercion', 'PERMITIDO')
            ->exists();
    
        if (!$permisoInsertar) {
            return redirect()->route('areas.index')->withErrors('No tiene permiso para añadir asignación equipo');
        }
    
        // Obtener solo los equipos que están en estado "Sin Asignar" (Código 1)
        $equipos = Equipos::where('COD_ESTADO_EQUIPO', 1)->get();
    
        $codigoEquipo = $request->query('codigoEquipo');
    
        // Obtener empleados que no están inactivos
        $empleados = Empleados::where('ESTADO_EMPLEADO', '!=', 'INACTIVO')->get();
    
        // Concatenar DNI con nombre del empleado
        $empleados = $empleados->map(function ($empleado) {
            $empleado->nombre_con_dni = $empleado->DNI_EMPLEADO . ' - ' . $empleado->NOM_EMPLEADO;
            return $empleado;
        }); // <-- Cerrar la función de mapeo correctamente
    
        // Obtener proyectos que no están finalizados, suspendidos o inactivos
        $proyectos = Proyectos::whereNotIn('ESTADO_PROYECTO', ['FINALIZADO', 'SUSPENDIDO', 'INACTIVO'])->get();
    
        // Obtener todos los estados de asignación, excepto 2 y 3
        $estadosAsignacion = EstadoAsignacion::whereNotIn('COD_ESTADO_ASIGNACION', [2, 3])->get();
    
        // Obtener todos los tipos de asignación
        $tiposAsignacion = TipoAsignacion::all();
    
        $this->bitacora->registrarEnBitacora(19, 'Ingreso a la ventana de crear en asignación', 'Ingreso');
    
        return view('asignaciones.crear', compact('equipos', 'empleados', 'proyectos', 'estadosAsignacion', 'tiposAsignacion', 'codigoEquipo'));
    }
    
public function store(Request $request)
{
    // Validar los datos de la solicitud usando la función personalizada de validación
    $validator = $this->validateAsignacion($request);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    try {
        // Iniciar una transacción de base de datos
        DB::beginTransaction();

        // Verificar si el tipo de asignación es "Asignar Mantenimiento"
        if ($request->TIPO_ASIGNACION == 2) { // Asumiendo que 2 es el código para "Asignar Mantenimiento"
            $request->merge(['COD_PROYECTO' => 0]);
        }

        // Crear la nueva asignación
        $asignacion = new Asignacion_Equipos();
        $asignacion->COD_EQUIPO = $request->COD_EQUIPO;
        $asignacion->COD_EMPLEADO = $request->COD_EMPLEADO;
        $asignacion->COD_PROYECTO = $request->COD_PROYECTO ?? 0; // Asegurarse de que no sea nulo
        $asignacion->DESCRIPCION = $request->DESCRIPCION;
        $asignacion->COD_ESTADO_ASIGNACION = 1; // Código para "Activo"
        $asignacion->TIPO_ASIGNACION = $request->TIPO_ASIGNACION;
        $asignacion->FECHA_ASIGNACION_INICIO = $request->FECHA_ASIGNACION_INICIO;
        $asignacion->save();

        // Actualizar el estado del equipo a "EN USO" (Código 2)
        $equipo = Equipos::find($request->COD_EQUIPO);
        if ($equipo) {
            $equipo->COD_ESTADO_EQUIPO = 2; // Código para "EN USO"
            $equipo->save();
        } else {
            // Si el equipo no se encuentra, revertir la transacción
            DB::rollBack();
            return redirect()->route('asignaciones.index')->with('error', 'Error al obtener los datos del equipo');
        }

        // Registrar en la bitácora
        $this->bitacora->registrarEnBitacora(19, 'Nueva asignación creada', 'creacion');

        // Confirmar la transacción
        DB::commit();

        return redirect()->route('asignaciones.index')->with('success', 'Asignación creada y estado del equipo actualizado correctamente');
    } catch (\Exception $e) {
        // En caso de error, revertir la transacción
        DB::rollBack();

        // Registrar el error en la bitácora y logs
        Log::error('Error al crear asignación: ' . $e->getMessage());
        $this->bitacora->registrarEnBitacora(19, 'Error al crear asignación', 'creacion');

        return redirect()->route('asignaciones.index')->with('error', 'Error al crear la asignación. Detalle: ' . $e->getMessage());
    }
}


 

public function edit($id)
{
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar permisos
    $permisoActualizar = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'ASIGNACION_EQUIPO')
                ->limit(1);
        })
        ->where('Permiso_Actualizacion', 'PERMITIDO')
        ->exists();

    if (!$permisoActualizar) {
        $this->bitacora->registrarEnBitacora(19, 'Intento de actualizar áreas sin permisos', 'Update');
        return redirect()->route('areas.index')->withErrors('No tiene permiso para editar áreas');
    }

    // Obtener la asignación con la relación tipoAsignacion
    $asignacion = Asignacion_Equipos::with('tipoAsignacion')->find($id);
    if (!$asignacion) {
        return redirect()->route('asignaciones.index')->with('error', 'Asignación no encontrada');
    }

    // Verificar si el estado es inactivo (3)
    if ($asignacion->COD_ESTADO_ASIGNACION == 3) {
        return redirect()->route('asignaciones.index')->with('error', 'No se puede editar una asignación inactiva.');
    }

    // Obtener los empleados que no están inactivos
    $empleados = Empleados::where('ESTADO_EMPLEADO', '!=', 'INACTIVO')->get();

    // Concatenar DNI con nombre del empleado
    $empleados = $empleados->map(function ($empleado) {
        $empleado->nombre_con_dni = $empleado->DNI_EMPLEADO . ' - ' . $empleado->NOM_EMPLEADO;
        return $empleado;
    });

    // Obtener los proyectos que no están finalizados, suspendidos o inactivos
    $proyectos = Proyectos::whereNotIn('ESTADO_PROYECTO', ['FINALIZADO', 'SUSPENDIDO', 'INACTIVO'])->get();

    // Obtener todos los equipos
    $equipos = Equipos::all();

    // Obtener todos los estados de asignación
    $estadosAsignacion = EstadoAsignacion::all();

    // Obtener todos los tipos de asignación
    $tiposAsignacion = TipoAsignacion::all();

    $this->bitacora->registrarEnBitacora(19, 'Ingreso a la ventana editar mantenimiento', 'Ingreso');

    return view('asignaciones.edit', compact('asignacion', 'empleados', 'proyectos', 'equipos', 'estadosAsignacion', 'tiposAsignacion'));
}




public function update(Request $request, $id)
{
    $asignacion = Asignacion_Equipos::findOrFail($id);

    // Verificar si el estado es inactivo (3)
    if ($asignacion->COD_ESTADO_ASIGNACION == 3) {
        return redirect()->route('asignaciones.index')->with('error', 'No se puede editar una asignación inactiva.');
    }

    // Validar datos usando la función personalizada de validación
    $validator = $this->validateAsignacion($request);

    // Si hay errores de validación, redirigir de vuelta con errores
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Actualizar campos
    $asignacion->DESCRIPCION = $request->DESCRIPCION;

    if ($asignacion->COD_ESTADO_ASIGNACION == 1) {
        $asignacion->FECHA_ASIGNACION_INICIO = $request->FECHA_ASIGNACION_INICIO;
        $asignacion->COD_EMPLEADO = $request->COD_EMPLEADO;
        // Solo actualizar proyecto si no es un mantenimiento
        if ($asignacion->TIPO_ASIGNACION != 2) {
            $asignacion->COD_PROYECTO = $request->COD_PROYECTO;
        }
    } else {
        // Mantener los valores originales para campos no permitidos para editar
        $asignacion->COD_EQUIPO = $asignacion->COD_EQUIPO;
        $asignacion->COD_EMPLEADO = $asignacion->COD_EMPLEADO;
        $asignacion->COD_PROYECTO = $asignacion->COD_PROYECTO;
        $asignacion->FECHA_ASIGNACION_INICIO = $asignacion->FECHA_ASIGNACION_INICIO;
    }

    // Guardar los cambios en la base de datos
    if ($asignacion->save()) {
        $this->bitacora->registrarEnBitacora(19, 'Asignación actualizada', 'actualizar');
        return redirect()->route('asignaciones.index')->with('success', 'Asignación actualizada correctamente.');
    }

    return redirect()->route('asignaciones.index')->with('error', 'Error al actualizar la asignación.');
}


public function eliminarAsignacion(Request $request, $id)
{
    $asignacion = Asignacion_Equipos::findOrFail($id);

    // Verificar el estado de la asignación
    if ($asignacion->COD_ESTADO_ASIGNACION == 2) {
        // Cambiar el estado de la asignación a 3 (Inactivo)
        $asignacion->COD_ESTADO_ASIGNACION = 3;
        $asignacion->save();

        $this->bitacora->registrarEnBitacora(19, 'Asignación eliminada', 'eliminación');
        return response()->json(['message' => 'Asignación eliminada correctamente.'], 200);
    } elseif ($asignacion->COD_ESTADO_ASIGNACION == 3) {
        return response()->json(['message' => 'La asignación ya está eliminada.'], 400);
    } else {
        return response()->json(['message' => 'Solo se pueden eliminar asignaciones activas.'], 400);
    }
}

 







public function generalPost(Request $request)
{
    return $this->generarReporteGeneral();
}

public function generarReporteGeneral()
{
    $asignaciones = Asignacion_Equipos::with(['empleado', 'equipo', 'proyectos', 'estado_asignacion', 'tipoAsignacion'])->get();

    // Verificar si no existen asignaciones
    if ($asignaciones->isEmpty()) {
        return redirect()->route('asignaciones.index')->with('error', 'No existen registros para generar el reporte.');
    }

    // Formatear fechas y agregar información adicional
    $asignaciones->each(function ($asignacion) {
        $asignacion->FECHA_ASIGNACION_INICIO = $asignacion->FECHA_ASIGNACION_INICIO ? Carbon::parse($asignacion->FECHA_ASIGNACION_INICIO)->format('Y/m/d') : 'Fecha no disponible';
        $asignacion->FECHA_ASIGNACION_FIN = $asignacion->FECHA_ASIGNACION_FIN && $asignacion->FECHA_ASIGNACION_FIN !== '0000-00-00'
            ? Carbon::parse($asignacion->FECHA_ASIGNACION_FIN)->format('Y/m/d')
            : '-';

        $asignacion->NOM_EQUIPO = $asignacion->equipo->NOM_EQUIPO ?? 'Desconocido';
        $asignacion->NOM_EMPLEADO = $asignacion->empleado->NOM_EMPLEADO ?? 'Desconocido';
        $asignacion->NOM_PROYECTO = $asignacion->COD_PROYECTO == 0 ? '-' : $asignacion->proyectos->NOM_PROYECTO ?? 'Desconocido';
        $asignacion->ESTADO_ASIGNACION = $asignacion->estado_asignacion->ESTADO ?? 'Desconocido';
        $asignacion->TIPO_ASIGNACION_NOMBRE = $asignacion->tipoAsignacion->TIPO_ASIGNACION ?? 'Desconocido';
    });

    $logoPath = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath));
    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
    $empresa = "Constructora Traterra S. de R.L";

    $pdf = Pdf::loadView('asignaciones.general', compact('asignaciones', 'logoBase64', 'fechaHora', 'empresa'))
        ->setPaper('a4', 'landscape');

    // Añadir paginación en el pie de página y ajustarla
    $pdf->output();
    $dompdf = $pdf->getDomPDF();
    $canvas = $dompdf->get_canvas();
    $canvas->page_text(720, 580, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0)); // Paginación a la derecha
    $canvas->page_text(20, 580, "Generado el: " . $fechaHora, null, 10, array(0, 0, 0)); // Fecha a la izquierda

    return $pdf->stream('reporte_general.pdf');
}
public function gestionar(Request $request, $id)
{
    // Obtener la asignación con las relaciones necesarias
    $asignacion = Asignacion_Equipos::with(['equipo', 'empleado', 'proyectos', 'estado_asignacion'])->findOrFail($id);

    // Si el método es PUT, se está intentando actualizar el estado
    if ($request->isMethod('put')) {
        $estadoSeleccionado = $request->estado;
        $marcarFinalizado = $request->has('finalizar') && $request->finalizar == 'on';

        // Verificar si se seleccionó el estado "Finalizado" o se marcó la opción de finalizar
        if ($estadoSeleccionado == 2 || $marcarFinalizado) {
            // Cambiar el estado de la asignación a "Finalizado" (2)
            $asignacion->COD_ESTADO_ASIGNACION = 2;

            // Obtener la fecha de inicio de la asignación
            $fechaInicio = \Carbon\Carbon::parse($asignacion->FECHA_ASIGNACION_INICIO);
            $fechaActual = \Carbon\Carbon::now();

            // Verificar si la fecha de inicio es mayor que la fecha actual
            if ($fechaInicio->greaterThan($fechaActual)) {
                // Establecer la fecha de finalización a la misma que la de inicio
                $asignacion->FECHA_ASIGNACION_FIN = $fechaInicio;
            } else {
                // Actualizar la fecha de finalización a la fecha actual
                $asignacion->FECHA_ASIGNACION_FIN = $fechaActual;
            }

            // Cambiar el estado del equipo a "sin asignar" (1)
            $equipo = Equipos::findOrFail($asignacion->COD_EQUIPO);
            $equipo->COD_ESTADO_EQUIPO = 1; // Estado sin asignar
            $equipo->save();
        } else {
            // Cambiar solo el estado de la asignación si no es "Finalizado"
            $asignacion->COD_ESTADO_ASIGNACION = $estadoSeleccionado;

            // Si el estado no es finalizado, no se debe modificar la fecha final
        }

        // Guardar los cambios en la asignación
        $asignacion->save();

        // Redirigir al index después de actualizar el estado
        return redirect()->route('asignaciones.index')->with('success', 'Estado actualizado correctamente.');
    }

    // Filtrar los estados para que no incluya el estado inactivo (3)
    $estadosAsignacion = EstadoAsignacion::where('COD_ESTADO_ASIGNACION', '!=', 3)->get();

    // Renderizar la vista de gestión con los datos necesarios
    return view('asignaciones.gestionar', compact('asignacion', 'estadosAsignacion'));
}



public function generarReportePorEstado(Request $request)
{
    $estadoAsignacionId = $request->input('estadoAsignacion');
    $estadoAsignacion = EstadoAsignacion::find($estadoAsignacionId);

    if (!$estadoAsignacion) {
        return redirect()->route('asignaciones.index')->with('error', 'Debe seleccionar un estado de asignación válido.');
    }

    $asignaciones = Asignacion_Equipos::with(['empleado', 'equipo', 'proyectos', 'estado_asignacion', 'tipoAsignacion'])
        ->where('COD_ESTADO_ASIGNACION', $estadoAsignacionId)
        ->get();

    // Verificar si no existen asignaciones
    if ($asignaciones->isEmpty()) {
        return redirect()->route('asignaciones.index')->with('error', 'No existen registros para generar el reporte.');
    }

    // Formatear fechas y agregar información adicional como cadenas de texto
    $asignaciones->each(function ($asignacion) {
        $asignacion->FECHA_ASIGNACION_INICIO = $asignacion->FECHA_ASIGNACION_INICIO ? (string) Carbon::parse($asignacion->FECHA_ASIGNACION_INICIO)->format('d/m/Y') : 'Fecha no disponible';
        $asignacion->FECHA_ASIGNACION_FIN = $asignacion->FECHA_ASIGNACION_FIN && $asignacion->FECHA_ASIGNACION_FIN !== '0000-00-00'
            ? (string) Carbon::parse($asignacion->FECHA_ASIGNACION_FIN)->format('d/m/Y')
            : '-';

        $asignacion->NOM_EQUIPO = $asignacion->equipo->NOM_EQUIPO ?? 'Desconocido';
        $asignacion->NOM_EMPLEADO = $asignacion->empleado->NOM_EMPLEADO ?? 'Desconocido';
        $asignacion->NOM_PROYECTO = $asignacion->COD_PROYECTO == 0 ? '-' : $asignacion->proyectos->NOM_PROYECTO ?? 'Desconocido';
        $asignacion->ESTADO_ASIGNACION = $asignacion->estado_asignacion->ESTADO ?? 'Desconocido';
        $asignacion->TIPO_ASIGNACION_NOMBRE = $asignacion->tipoAsignacion->TIPO_ASIGNACION ?? 'Desconocido';
    });

    $logoPath = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath));
    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
    $empresa = "Constructora Traterra S. de R.L";

    $estadoAsignacionNombre = $estadoAsignacion->ESTADO;

    $pdf = Pdf::loadView('asignaciones.reporte_estado', compact('asignaciones', 'logoBase64', 'fechaHora', 'empresa', 'estadoAsignacionNombre'))
        ->setPaper('a4', 'landscape');

    // Añadir paginación en el pie de página y ajustarla
    $pdf->output();
    $dompdf = $pdf->getDomPDF();
    $canvas = $dompdf->get_canvas();
    $canvas->page_text(720, 580, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0)); // Paginación a la derecha
    $canvas->page_text(20, 580, "Generado el: " . $fechaHora, null, 10, array(0, 0, 0)); // Fecha a la izquierda

    return $pdf->stream('reporte_por_estado.pdf');
}


public function generarReportePorTipo(Request $request)
{
    $tipoAsignacionId = $request->input('tipoAsignacion');
    $tipoAsignacion = TipoAsignacion::find($tipoAsignacionId);

    if (!$tipoAsignacion) {
        return redirect()->route('asignaciones.index')->with('error', 'Debe seleccionar un tipo de asignación válido.');
    }

    $asignaciones = Asignacion_Equipos::with(['empleado', 'equipo', 'proyectos', 'estado_asignacion', 'tipoAsignacion'])
        ->where('TIPO_ASIGNACION', $tipoAsignacionId)
        ->get();

    // Verificar si no existen asignaciones
    if ($asignaciones->isEmpty()) {
        return redirect()->route('asignaciones.index')->with('error', 'No existen registros para generar el reporte.');
    }

    // Formatear fechas y agregar información adicional como cadenas de texto
    $asignaciones->each(function ($asignacion) {
        $asignacion->FECHA_ASIGNACION_INICIO = $asignacion->FECHA_ASIGNACION_INICIO ? (string) Carbon::parse($asignacion->FECHA_ASIGNACION_INICIO)->format('d/m/Y') : 'Fecha no disponible';
        $asignacion->FECHA_ASIGNACION_FIN = $asignacion->FECHA_ASIGNACION_FIN && $asignacion->FECHA_ASIGNACION_FIN !== '0000-00-00'
            ? (string) Carbon::parse($asignacion->FECHA_ASIGNACION_FIN)->format('d/m/Y')
            : '-';

        $asignacion->NOM_EQUIPO = $asignacion->equipo->NOM_EQUIPO ?? 'Desconocido';
        $asignacion->NOM_EMPLEADO = $asignacion->empleado->NOM_EMPLEADO ?? 'Desconocido';
        $asignacion->NOM_PROYECTO = $asignacion->COD_PROYECTO == 0 ? '-' : $asignacion->proyectos->NOM_PROYECTO ?? 'Desconocido';
        $asignacion->ESTADO_ASIGNACION = $asignacion->estado_asignacion->ESTADO ?? 'Desconocido';
        $asignacion->TIPO_ASIGNACION_NOMBRE = $asignacion->tipoAsignacion->TIPO_ASIGNACION ?? 'Desconocido';
    });

    $logoPath = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath));
    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
    $empresa = "Constructora Traterra S. de R.L";
    $tipoAsignacionNombre = $tipoAsignacion->TIPO_ASIGNACION;

    // Cambiar 'tipoAsignacion' a 'tipoAsignacionNombre'
    $pdf = Pdf::loadView('asignaciones.reporte_tipo', compact('asignaciones', 'logoBase64', 'fechaHora', 'empresa', 'tipoAsignacionNombre'))
        ->setPaper('a4', 'landscape');

    // Añadir paginación en el pie de página y ajustarla
    $pdf->output();
    $dompdf = $pdf->getDomPDF();
    $canvas = $dompdf->get_canvas();
    $canvas->page_text(720, 580, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0)); // Paginación a la derecha
    $canvas->page_text(20, 580, "Generado el: " . $fechaHora, null, 10, array(0, 0, 0)); // Fecha a la izquierda

    return $pdf->stream('reporte_por_tipo.pdf');
}

public function generarReportePorProyecto(Request $request)
{
    $proyectoId = $request->input('proyecto');

    // Obtener el proyecto seleccionado
    $proyecto = Proyectos::find($proyectoId);

    if (!$proyecto) {
        return redirect()->route('asignaciones.index')->with('error', 'Debe seleccionar un proyecto válido.');
    }

    // Obtener el nombre del proyecto
    $proyectoNombre = $proyecto->NOM_PROYECTO;

    // Obtener las asignaciones relacionadas con el proyecto seleccionado
    $asignaciones = Asignacion_Equipos::with(['empleado', 'equipo', 'proyectos', 'estado_asignacion', 'tipoAsignacion'])
        ->where('COD_PROYECTO', $proyectoId)
        ->get();

    // Verificar si no existen asignaciones
    if ($asignaciones->isEmpty()) {
        return redirect()->route('asignaciones.index')->with('error', 'No existen registros para generar el reporte.');
    }

    // Formatear fechas y agregar información adicional como cadenas de texto
    $asignaciones->each(function ($asignacion) {
        $asignacion->FECHA_ASIGNACION_INICIO = $asignacion->FECHA_ASIGNACION_INICIO ? (string) Carbon::parse($asignacion->FECHA_ASIGNACION_INICIO)->format('d/m/Y') : 'Fecha no disponible';
        $asignacion->FECHA_ASIGNACION_FIN = $asignacion->FECHA_ASIGNACION_FIN && $asignacion->FECHA_ASIGNACION_FIN !== '0000-00-00'
            ? (string) Carbon::parse($asignacion->FECHA_ASIGNACION_FIN)->format('d/m/Y')
            : '-';

        $asignacion->NOM_EQUIPO = $asignacion->equipo->NOM_EQUIPO ?? 'Desconocido';
        $asignacion->NOM_EMPLEADO = $asignacion->empleado->NOM_EMPLEADO ?? 'Desconocido';
        $asignacion->NOM_PROYECTO = $asignacion->proyectos->NOM_PROYECTO ?? 'Desconocido';
        $asignacion->ESTADO_ASIGNACION = $asignacion->estado_asignacion->ESTADO ?? 'Desconocido';
        $asignacion->TIPO_ASIGNACION_NOMBRE = $asignacion->tipoAsignacion->TIPO_ASIGNACION ?? 'Desconocido';
    });

    $logoPath = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath));
    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
    $empresa = "Constructora Traterra S. de R.L";

    $pdf = Pdf::loadView('asignaciones.reporte_proyecto', compact('asignaciones', 'logoBase64', 'fechaHora', 'empresa', 'proyectoNombre'))
        ->setPaper('a4', 'landscape');

    // Añadir paginación en el pie de página y ajustarla
    $pdf->output();
    $dompdf = $pdf->getDomPDF();
    $canvas = $dompdf->get_canvas();
    $canvas->page_text(720, 580, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0)); // Paginación a la derecha
    $canvas->page_text(20, 580, "Generado el: " . $fechaHora, null, 10, array(0, 0, 0)); // Fecha a la izquierda

    return $pdf->stream('reporte_por_proyecto.pdf');
}

public function generarReportePorEmpleado(Request $request)
{
    $empleadoId = $request->input('empleado');

    // Obtener el empleado seleccionado
    $empleado = Empleados::find($empleadoId);

    if (!$empleado) {
        return redirect()->route('asignaciones.index')->with('error', 'Debe seleccionar un empleado válido.');
    }

    // Obtener el nombre del empleado
    $empleadoNombre = $empleado->NOM_EMPLEADO;

    // Obtener las asignaciones relacionadas con el empleado seleccionado
    $asignaciones = Asignacion_Equipos::with(['empleado', 'equipo', 'proyectos', 'estado_asignacion', 'tipoAsignacion'])
        ->where('COD_EMPLEADO', $empleadoId)
        ->get();

    // Verificar si no existen asignaciones
    if ($asignaciones->isEmpty()) {
        return redirect()->route('asignaciones.index')->with('error', 'No existen registros para generar el reporte.');
    }

    // Formatear fechas y agregar información adicional como cadenas de texto
    $asignaciones->each(function ($asignacion) {
        $asignacion->FECHA_ASIGNACION_INICIO = $asignacion->FECHA_ASIGNACION_INICIO ? (string) Carbon::parse($asignacion->FECHA_ASIGNACION_INICIO)->format('d/m/Y') : 'Fecha no disponible';
        $asignacion->FECHA_ASIGNACION_FIN = $asignacion->FECHA_ASIGNACION_FIN && $asignacion->FECHA_ASIGNACION_FIN !== '0000-00-00'
            ? (string) Carbon::parse($asignacion->FECHA_ASIGNACION_FIN)->format('d/m/Y')
            : '-';

        $asignacion->NOM_EQUIPO = $asignacion->equipo->NOM_EQUIPO ?? 'Desconocido';
        $asignacion->NOM_EMPLEADO = $asignacion->empleado->NOM_EMPLEADO ?? 'Desconocido';
        $asignacion->NOM_PROYECTO = $asignacion->proyectos->NOM_PROYECTO ?? 'Desconocido';
        $asignacion->ESTADO_ASIGNACION = $asignacion->estado_asignacion->ESTADO ?? 'Desconocido';
        $asignacion->TIPO_ASIGNACION_NOMBRE = $asignacion->tipoAsignacion->TIPO_ASIGNACION ?? 'Desconocido';
    });

    $logoPath = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath));
    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
    $empresa = "Constructora Traterra S. de R.L";

    $pdf = Pdf::loadView('asignaciones.reporte_empleado', compact('asignaciones', 'logoBase64', 'fechaHora', 'empresa', 'empleadoNombre'))
        ->setPaper('a4', 'landscape');

    // Añadir paginación en el pie de página y ajustarla
    $pdf->output();
    $dompdf = $pdf->getDomPDF();
    $canvas = $dompdf->get_canvas();
    $canvas->page_text(720, 580, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0)); // Paginación a la derecha
    $canvas->page_text(20, 580, "Generado el: " . $fechaHora, null, 10, array(0, 0, 0)); // Fecha a la izquierda

    return $pdf->stream('reporte_por_empleado.pdf');
}

public function mostrarModal()
{
    $tiposAsignacion = TipoAsignacion::all();
    $estadosAsignacion = EstadoAsignacion::all();

    // Obtener proyectos únicos que tienen asignaciones en la tabla tbl_equipo_asignacion, excluyendo aquellos con COD_PROYECTO = 0
        // Obtener proyectos únicos, excluyendo aquellos con COD_PROYECTO = 0
        $proyectosAsignados= Proyectos::where('COD_PROYECTO', '!=', 0)->distinct()->get(['COD_PROYECTO', 'NOM_PROYECTO']);

    // Obtener empleados únicos que tienen asignaciones en la tabla tbl_equipo_asignacion
    $empleadosAsignados = Empleados::whereIn('COD_EMPLEADO', function($query) {
        $query->select('COD_EMPLEADO')->from('tbl_equipo_asignacion');
    })->distinct()->get(['COD_EMPLEADO', 'NOM_EMPLEADO']);

    return view('asignaciones.reporte_modal', compact('tiposAsignacion', 'estadosAsignacion', 'proyectosAsignados', 'empleadosAsignados'));
}
}