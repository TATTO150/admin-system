<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BitacoraController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Rules\Validaciones;
use Carbon\Carbon;
use App\Models\Area;
use App\Models\EstadoEmpleado;
use App\Models\Cargo;
use App\Models\Permisos;
use App\Models\EmpleadoProyectos;
use App\Models\Proyectos;
use App\Models\Empleados;
use App\Models\Planillas;
use App\Providers\PermisoService;
use Barryvdh\DomPDF\Facade\Pdf;

class EmpleadosControlador extends Controller
{
    protected $bitacora;
    protected $permisoService;

    public function __construct(BitacoraController $bitacora, PermisoService $permisoService)
    {
        $this->bitacora = $bitacora;
        $this->permisoService = $permisoService;  // Inyectar el PermisoService
    }
    protected $areas;
    protected $cargos;

    public function index()
    {
         // Verificar si el usuario no está autenticado
         if (!Auth::check()) {
            // Redirigir a la vista `sesion_suspendida`
            return redirect()->route('sesion.suspendida');
        }
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        //Nueva validacvion de permisos
        $this->permisoService->tienePermiso('EMPLEADO', 'Consultar', true);
    
        $empleados = Empleados::with('estadoEmpleado')->get();
    
        $empleadosActivos = [];
        $empleadosInactivos = [];
        
        foreach ($empleados as &$empleado) {
            if (!empty($empleado['FEC_INGRESO_EMPLEADO'])) {
                $empleado['FEC_INGRESO_EMPLEADO'] = Carbon::parse($empleado['FEC_INGRESO_EMPLEADO'])->format('Y-m-d');
            } else {
                $empleado['FEC_INGRESO_EMPLEADO'] = 'Fecha no disponible';
            }
    
            $empleadosActivos = Empleados::where('ESTADO_EMPLEADO', 'ACTIVO')->get();
            $empleadosInactivos = Empleados::where('ESTADO_EMPLEADO', 'INACTIVO')->get();

        }
    
        $tipo = EstadoEmpleado::all()->keyBy('COD_ESTADO_EMPLEADO');
        $areas = \App\Models\Area::all()->keyBy('COD_AREA');
        $cargos = \App\Models\Cargo::all()->keyBy('COD_CARGO');
        $proyectos = \App\Models\Proyectos::all()->keyBy('COD_PROYECTO');
    
        $this->bitacora->registrarEnBitacora(16, 'Ingreso a la ventana de empleados', 'Ingreso');
    
        return view('empleados.index', compact('empleadosActivos', 'empleadosInactivos', 'areas', 'cargos', 'proyectos', 'tipo'));
    }

    
    public function pdf(Request $request)
{
     // Verificar si el usuario no está autenticado
     if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
    // Obtener parámetros de la solicitud
    $tipo = $request->query('tipo');
    $id = $request->query('id');
    $estado = $request->query('estado');
    $tipoE = $request->query('tipoE');

    // Inicializar la consulta
    $empleadosQuery = \App\Models\Empleados::query();

    // Obtener los empleados según el tipo de reporte
    switch ($tipo) {
        case 'area':
            if ($id) {
                // Consulta para obtener el nombre del área
                $area = \App\Models\Area::find($id);
    
                if ($area) {
                    $empleadosQuery->where('COD_AREA', $id);
                    $tipo_reporte = 'Reporte por Área: ' . $area->NOM_AREA;
                } else {
                    return response()->json(['error' => 'Área no encontrada'], 404);
                }
            } else {
                return response()->json(['error' => 'ID de área no proporcionado'], 400);
            }
            break;
    
        case 'cargo':
            if ($id) {
                // Consulta para obtener el nombre del cargo
                $cargo = \App\Models\Cargo::find($id);
    
                if ($cargo) {
                    $empleadosQuery->where('COD_CARGO', $id);
                    $tipo_reporte = 'Reporte por Cargo: ' . $cargo->NOM_CARGO;
                } else {
                    return response()->json(['error' => 'Cargo no encontrado'], 404);
                }
            } else {
                return response()->json(['error' => 'ID de cargo no proporcionado'], 400);
            }
            break;

        case 'estado':
            if ($estado) {
                $empleadosQuery->where('ESTADO_EMPLEADO', $estado);
                $tipo_reporte = 'Reporte por Estado ' . $estado;
            } else {
                return response()->json(['error' => 'Estado no proporcionado'], 400);
            }
            break;

        case 'tipo':
            if ($tipoE) {
                $empleadosQuery->where('TIP_EMPLEADO', $tipoE);
                $tipo_reporte = 'Reporte por Estado ' . $tipoE;
            } else {
                return response()->json(['error' => 'Tipo no proporcionado'], 400);
            }
            break;

        case 'general':
            $tipo_reporte = 'Reporte General';
        default:
            // No se aplica ningún filtro adicional
            break;
    }

    // Obtener los empleados con los filtros aplicados
    $empleados = $empleadosQuery->get();

    // Validar si no se encontraron empleados
    if ($empleados->isEmpty()) {
        // Retornar un mensaje de error en formato JSON
        return response()->json(['error' => 'No se encontraron empleados para el reporte solicitado'], 404);
    }

    // Obtener la fecha y hora actual
    $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');

    // Convertir la imagen a formato base64 para el PDF
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    // Obtener áreas y cargos
    $areas = \App\Models\Area::all()->keyBy('COD_AREA');
    $cargos = \App\Models\Cargo::all()->keyBy('COD_CARGO');

    // Cargar la vista del PDF con los datos
    $pdf = Pdf::loadView('empleados.pdf', compact('empleados', 'areas', 'cargos', 'fechaHora', 'logoBase64', 'tipo_reporte'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    // Registrar en la bitácora
    $this->bitacora->registrarEnBitacora(16, 'Generación de reporte de empleados', 'Consulta');
    
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
    
    //Nueva validacvion de permisos
    $this->permisoService->tienePermiso('EMPLEADO', 'Insercion', true);

    $tipo = EstadoEmpleado::where('ESTADO', 'ACTIVO')->get();
    $cargos = Cargo::all();
    $areas = Area::all();
    $proyectos = Proyectos::whereNotIn('ESTADO_PROYECTO', ['FINALIZADOS', 'SUSPENDIDO', 'INACTIVO'])->get();

    return view('empleados.crear', compact('areas', 'cargos', 'proyectos', 'tipo'));
}


public function insertar(Request $request)
{
    $validator = Validator::make($request->all(), [
        'NOM_EMPLEADO' => [
        'required',
        'regex:/^[A-ZÑÁÉÍÓÚ]+(?:\s[A-ZÑÁÉÍÓÚ]+)*$/u', // Solo letras mayúsculas, con un solo espacio entre palabras
        'max:40', // Máximo de 40 caracteres
        'unique:tbl_empleado,NOM_EMPLEADO'// Validación de campo único, excluyendo el empleado actual
    ],


       'DNI_EMPLEADO' => [(new Validaciones)->requerirCampo()->requerirSoloNumeros()->validarDni()->validarCampoUnico('tbl_empleado', 'DNI_EMPLEADO')->requerirSinEspacios()],
        
        'CORREO_EMPLEADO' => [
            'required',
            'email', // Validación de formato de correo electrónico
            'unique:tbl_empleado,CORREO_EMPLEADO',
            'regex:/^(?!.*\s{2,}).*$/', // Prohibir múltiples espacios
        ],
        'DIRECCION_EMPLEADO' => [
            'required',
            'regex:/^[A-ZÑÁÉÍÓÚ]+(?:\s[A-ZÑÁÉÍÓÚ]+)*$/u', // Solo letras mayúsculas, permite un solo espacio entre palabras
            'max:125', // Máximo de 125 caracteres
        ],

        'CONTRATO_EMPLEADO' => 'nullable', // Opcional, no se requiere validación adicional
        'SALARIO_BASE' => [
            'required',
            'numeric',
            'min:0.01', // No permitir cero ni valores negativos
            'regex:/^\d+(\.\d{1,2})?$/', // Permitir sólo números y hasta dos decimales
            'regex:/^(?!.*\s{2,}).*$/', // Prohibir múltiples espacios
        ],
        'proyectos' => 'nullable|array',
        'proyectos.*' => 'nullable|exists:tbl_proyectos,COD_PROYECTO',
    ], [
        'NOM_EMPLEADO.required' => 'El nombre del empleado es obligatorio.',
        'NOM_EMPLEADO.regex' => 'El nombre del empleado debe contener solo letras mayúsculas, sin números ni símbolos especiales y sin espacios consecutivos.',
        'NOM_EMPLEADO.max' => 'El nombre del empleado no puede exceder los 40 caracteres.',
        'NOM_EMPLEADO.unique' => 'El nombre del empleado ya existe.',
        'TIP_EMPLEADO.required' => 'El tipo de empleado es obligatorio.',
        'TIP_EMPLEADO.regex' => 'El tipo de empleado debe contener solo letras mayúsculas, sin números ni símbolos especiales y sin espacios consecutivos.',
        'TIP_EMPLEADO.max' => 'El tipo de empleado no puede exceder los 40 caracteres.',
        'COD_AREA.required' => 'El área es obligatoria.',
        'COD_AREA.numeric' => 'El área debe ser un número.',
        'DNI_EMPLEADO.required' => 'El DNI del empleado es obligatorio.',
        'DNI_EMPLEADO.numeric' => 'El DNI del empleado debe contener solo números.',
        'DNI_EMPLEADO.size' => 'El DNI del empleado debe tener 8 dígitos.',
        'DNI_EMPLEADO.unique' => 'El DNI del empleado ya existe.',
        'DNI_EMPLEADO.regex' => 'El DNI del empleado debe contener solo números.',
        'COD_CARGO.required' => 'El cargo es obligatorio.',
        'COD_CARGO.numeric' => 'El cargo debe ser un número.',
        'CORREO_EMPLEADO.required' => 'El correo del empleado es obligatorio.',
        'CORREO_EMPLEADO.email' => 'El correo del empleado debe ser una dirección de correo electrónico válida.',
        'CORREO_EMPLEADO.unique' => 'El correo del empleado ya existe.',
        'CORREO_EMPLEADO.regex' => 'El correo del empleado no debe contener espacios consecutivos.',
        'DIRECCION_EMPLEADO.required' => 'La dirección del empleado es obligatoria.',
        'DIRECCION_EMPLEADO.regex' => 'La dirección del empleado debe contener solo letras mayúsculas, sin números ni símbolos especiales y sin espacios consecutivos.',
        'DIRECCION_EMPLEADO.max' => 'La dirección del empleado no puede exceder los 125 caracteres.',
        'SALARIO_BASE.required' => 'El salario base es obligatorio.',
        'SALARIO_BASE.numeric' => 'El salario base debe ser un número.',
        'SALARIO_BASE.min' => 'El salario base no puede ser cero ni negativo.',
        'SALARIO_BASE.regex' => 'El salario base debe ser un número positivo con hasta dos decimales y sin espacios consecutivos.',
        'proyectos.array' => 'El campo proyectos debe ser una lista de proyectos.',
        'proyectos.*.exists' => 'El proyecto seleccionado no es válido.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $salarioBase = $request->SALARIO_BASE;
    $deducciones = $salarioBase * 0.05;
    $salarioNeto = $salarioBase - $deducciones;
    $licenciaVehicular = $request->has('LICENCIA_VEHICULAR') ? 1 : 0;
    $contratoEmpleado = $request->has('CONTRATO_EMPLEADO') ? 1 : 0;

    // Crear un nuevo empleado
    $empleado = Empleados::create([
        'NOM_EMPLEADO' => $request->NOM_EMPLEADO,
        'COD_ESTADO_EMPLEADO' => $request->COD_ESTADO_EMPLEADO,
        'COD_AREA' => $request->COD_AREA,
        'DNI_EMPLEADO' => $request->DNI_EMPLEADO,
        'LICENCIA_VEHICULAR' => $licenciaVehicular,
        'COD_CARGO' => $request->COD_CARGO,
        'FEC_INGRESO_EMPLEADO' => Carbon::now(),
        'CORREO_EMPLEADO' => $request->CORREO_EMPLEADO,
        'DIRECCION_EMPLEADO' => $request->DIRECCION_EMPLEADO,
        'CONTRATO_EMPLEADO' => $contratoEmpleado,
        'SALARIO_BASE' => $salarioBase,
        'DEDUCCIONES' => $deducciones,
        'SALARIO_NETO' => $salarioNeto,
        'ESTADO_EMPLEADO' => 'ACTIVO',
    ]);

    // Asignar proyectos seleccionados al empleado si hay alguno
    if ($request->has('proyectos')) {
        foreach ($request->proyectos as $proyectoId) {
            EmpleadoProyectos::create([
                'COD_PROYECTO' => $proyectoId,
                'DNI_EMPLEADO' => $empleado->DNI_EMPLEADO,
            ]);
        }
    }

    $this->bitacora->registrarEnBitacora(16, 'Empleado añadido exitosamente', 'Ingreso');

    return redirect()->route('empleados.index')->with('success', 'Empleado anadido correctamente!');
}




public function destroy($COD_EMPLEADO)
{
     // Verificar si el usuario no está autenticado
     if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
    $user = Auth::user();
    
    //Nueva validacvion de permisos
    $this->permisoService->tienePermiso('EMPLEADO', 'Eliminacion', true);

    // Obtener el salario neto del empleado antes de eliminarlo
    $empleado = DB::table('tbl_empleado')->where('COD_EMPLEADO', $COD_EMPLEADO)->first();
    
    // Verificar si se encontró el empleado
    if (!$empleado) {
        return redirect()->route('dashboard')->with('error', 'Empleado no encontrado');
    }
    

    // Eliminar el empleado
    DB::statement('CALL ELI_EMPLEADO(?)', [$COD_EMPLEADO]);
    

    $this->bitacora->registrarEnBitacora(16, 'Empleado eliminado', 'Delete'); 

    return redirect()->route('empleados.index')->with('success', 'Empleado eliminado correctamente');
}


public function edit($COD_EMPLEADO)
{
     // Verificar si el usuario no está autenticado
     if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
    $user = Auth::user();
    
    //Nueva validacvion de permisos
    $this->permisoService->tienePermiso('EMPLEADO', 'Actualizacion', true);

   // En tu controlador, al pasar los datos a la vista
    $empleados = Empleados::find($COD_EMPLEADO);
   
    
    if (!isset($empleados['COD_EMPLEADO'])) {
        dd('COD_EMPLEADO no está definido en la respuesta de la API', $empleados);
    }

    // Obtener áreas, cargos y proyectos
    $tipo = EstadoEmpleado::where('ESTADO', 'ACTIVO')->get();
    $areas = Area::all();
    $cargos = Cargo::all();
    $proyectos = Proyectos::whereNotIn('ESTADO_PROYECTO', ['FINALIZADO', 'SUSPENDIDO', 'INACTIVO'])->get();

    // Obtener proyectos asignados al empleado
    $empleadosProyectos = EmpleadoProyectos::where('DNI_EMPLEADO', $empleados['DNI_EMPLEADO'])
        ->pluck('COD_PROYECTO')
        ->toArray();

    // Pasar todos los datos a la vista
    return view('empleados.edit', compact('empleados', 'areas', 'cargos', 'proyectos', 'empleadosProyectos', 'tipo'));
}

public function update(Request $request, $COD_EMPLEADO)
{

    $fechaMinima = Carbon::now()->subMonth()->format('Y-m-d');
    $validator = Validator::make($request->all(), [
        'NOM_EMPLEADO' => [
            'required',
            'regex:/^[A-ZÑÁÉÍÓÚ]+(?:\s[A-ZÑÁÉÍÓÚ]+)*$/u', // Solo letras mayúsculas, con un solo espacio entre palabras
            'max:40', // Máximo de 40 caracteres
         ],
        'DNI_EMPLEADO' => [(new Validaciones)->requerirCampo()->requerirSoloNumeros()->validarDni()->validarCampoUnico('tbl_empleado', 'DNI_EMPLEADO', $COD_EMPLEADO)->requerirSinEspacios()],
        'FEC_INGRESO_EMPLEADO' => [(new Validaciones)->requerirCampo()->requerirFechaIngresoMinima($fechaMinima)->requerirSinEspacios()->requerirFechaIngresoValida()],
        'CORREO_EMPLEADO' => [
            'required',
            'email', // Validación de formato de correo electrónico
            'unique:tbl_empleado,CORREO_EMPLEADO,' . $COD_EMPLEADO . ',COD_EMPLEADO', // Validación de campo único, excluyendo el empleado actual
            'regex:/^(?!.*\s{2,}).*$/', // Prohibir múltiples espacios
            'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', // Validación de estructura de correo
        ],
        'DIRECCION_EMPLEADO' => [
            'required',
            'regex:/^[A-ZÑÁÉÍÓÚ]+(?:\s[A-ZÑÁÉÍÓÚ]+)*$/u', // Solo letras mayúsculas, permite un solo espacio entre palabras
            'max:125', // Máximo de 125 caracteres
        ],

        'SALARIO_BASE' => [
            'required',
            'numeric',
            'min:0.01', // No permitir cero ni valores negativos
            'regex:/^\d+(\.\d{1,2})?$/', // Permitir sólo números y hasta dos decimales
            'regex:/^(?!.*\s{2,}).*$/', // Prohibir múltiples espacios
        ],
        'proyectos' => 'nullable|array',
        'proyectos.*' => 'nullable|exists:tbl_proyectos,COD_PROYECTO',
    ], [
        'NOM_EMPLEADO.required' => 'El nombre del empleado es obligatorio.',
        'NOM_EMPLEADO.regex' => 'El nombre del empleado debe contener solo letras mayúsculas, sin números ni símbolos especiales y sin espacios consecutivos.',
        'NOM_EMPLEADO.max' => 'El nombre del empleado no puede exceder los 40 caracteres.',
        'NOM_EMPLEADO.unique' => 'El nombre del empleado ya existe.',
        'TIP_EMPLEADO.required' => 'El tipo de empleado es obligatorio.',
        'TIP_EMPLEADO.regex' => 'El tipo de empleado debe contener solo letras mayúsculas, sin números ni símbolos especiales y sin espacios consecutivos.',
        'TIP_EMPLEADO.max' => 'El tipo de empleado no puede exceder los 40 caracteres.',
        'COD_AREA.required' => 'El área es obligatoria.',
        'COD_AREA.numeric' => 'El área debe ser un número.',
        'DNI_EMPLEADO.required' => 'El DNI del empleado es obligatorio.',
        'DNI_EMPLEADO.numeric' => 'El DNI del empleado debe contener solo números.',
        'DNI_EMPLEADO.size' => 'El DNI del empleado debe tener 8 dígitos.',
        'DNI_EMPLEADO.unique' => 'El DNI del empleado ya existe.',
        'DNI_EMPLEADO.regex' => 'El DNI del empleado debe contener solo números.',
        'COD_CARGO.required' => 'El cargo es obligatorio.',
        'COD_CARGO.numeric' => 'El cargo debe ser un número.',
        'CORREO_EMPLEADO.required' => 'El correo del empleado es obligatorio.',
        'CORREO_EMPLEADO.email' => 'El correo del empleado debe ser una dirección de correo electrónico válida.',
        'CORREO_EMPLEADO.unique' => 'El correo del empleado ya existe.',
        'CORREO_EMPLEADO.regex' => 'El correo del empleado no debe contener espacios consecutivos.',
        'DIRECCION_EMPLEADO.required' => 'La dirección del empleado es obligatoria.',
        'DIRECCION_EMPLEADO.regex' => 'La dirección del empleado debe contener solo letras mayúsculas, sin números ni símbolos especiales y sin espacios consecutivos.',
        'DIRECCION_EMPLEADO.max' => 'La dirección del empleado no puede exceder los 125 caracteres.',
        'SALARIO_BASE.required' => 'El salario base es obligatorio.',
        'SALARIO_BASE.numeric' => 'El salario base debe ser un número.',
        'SALARIO_BASE.min' => 'El salario base no puede ser cero ni negativo.',
        'SALARIO_BASE.regex' => 'El salario base debe ser un número positivo con hasta dos decimales y sin espacios consecutivos.',
        'proyectos.array' => 'El campo proyectos debe ser una lista de proyectos.',
        'proyectos.*.exists' => 'El proyecto seleccionado no es válido.',
    ]);

    if ($validator->fails()) {
        $this->bitacora->registrarEnBitacora(16, 'Error al actualizar al usuario', 'Update');
        
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $salarioBase = $request->SALARIO_BASE;
    $deducciones = $salarioBase * 0.05;
    $salarioNeto = $salarioBase - $deducciones;
    $licenciaVehicular = $request->has('LICENCIA_VEHICULAR') ? 1 : 0;
    $contratoEmpleado = $request->has('CONTRATO_EMPLEADO') ? 1 : 0;

    $empleado = Empleados::find($COD_EMPLEADO);
    $antiguoDni = $empleado->DNI_EMPLEADO;

    $empleado->update([
        'NOM_EMPLEADO' => $request->NOM_EMPLEADO,
        'COD_ESTADO_EMPLEADO' => $request->COD_ESTADO_EMPLEADO,
        'COD_AREA' => $request->COD_AREA,
        'SALARIO_NETO' => $salarioNeto,
        'DNI_EMPLEADO' => $request->DNI_EMPLEADO,
        'LICENCIA_VEHICULAR' => $licenciaVehicular,
        'COD_CARGO' => $request->COD_CARGO,
        'FEC_INGRESO_EMPLEADO' => $request->FEC_INGRESO_EMPLEADO,
        'CORREO_EMPLEADO' => $request->CORREO_EMPLEADO,
        'DIRECCION_EMPLEADO' => $request->DIRECCION_EMPLEADO,
        'CONTRATO_EMPLEADO' => $contratoEmpleado,
        'SALARIO_BASE' => $salarioBase,
        'DEDUCCIONES' => $deducciones,
        'SALARIO_NETO' => $salarioNeto,
    ]);

    $proyectosSeleccionados = $request->input('proyectos', []);

    if ($antiguoDni !== $request->DNI_EMPLEADO) {
        EmpleadoProyectos::where('DNI_EMPLEADO', $antiguoDni)
            ->whereNotIn('COD_PROYECTO', $proyectosSeleccionados)
            ->delete();

        EmpleadoProyectos::where('DNI_EMPLEADO', $antiguoDni)
            ->update(['DNI_EMPLEADO' => $request->DNI_EMPLEADO]);

        foreach ($proyectosSeleccionados as $codigoProyecto) {
            EmpleadoProyectos::updateOrCreate(
                [
                    'DNI_EMPLEADO' => $request->DNI_EMPLEADO,
                    'COD_PROYECTO' => $codigoProyecto,
                ],
                [
                    'DNI_EMPLEADO' => $request->DNI_EMPLEADO,
                    'COD_PROYECTO' => $codigoProyecto,
                ]
            );
        }
    } else {
        EmpleadoProyectos::where('DNI_EMPLEADO', $antiguoDni)
            ->whereNotIn('COD_PROYECTO', $proyectosSeleccionados)
            ->delete();

        foreach ($proyectosSeleccionados as $codigoProyecto) {
            EmpleadoProyectos::updateOrCreate(
                [
                    'DNI_EMPLEADO' => $request->DNI_EMPLEADO,
                    'COD_PROYECTO' => $codigoProyecto,
                ],
                [
                    'DNI_EMPLEADO' => $request->DNI_EMPLEADO,
                    'COD_PROYECTO' => $codigoProyecto,
                ]
            );
        }
    }

    $this->bitacora->registrarEnBitacora(16, 'Usuario actualizado', 'Update');

    return redirect()->route('empleados.index')->with('success', 'Empleado actualizado correctamente!');
}

    

public function desactivar($id)
{
     // Verificar si el usuario no está autenticado
     if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
    $user = Auth::user();
    
    //Nueva validacvion de permisos
    $this->permisoService->tienePermiso('EMPLEADO', 'Eliminacion', true);

    // Encontrar al empleado por su ID
    $empleado = Empleados::findOrFail($id);

    // Verificar si el empleado está asignado a algún proyecto
    $asignadoAProyectos = EmpleadoProyectos::where('DNI_EMPLEADO', $empleado->DNI_EMPLEADO)->exists();

    if ($asignadoAProyectos) {
        // Registrar en la bitácora el intento fallido
        $this->bitacora->registrarEnBitacora(21, "Error al desactivar empleado con ID $id: El empleado está asignado a un proyecto", 'Update');
        return redirect()->route('empleados.index')->withErrors('Error al desactivar: El empleado está asignado a uno o más proyectos. Si desea eliminarlo debe eliminarlo de los proyectos a los que se encuentra.');
    }

    // Cambiar el estado del empleado a INACTIVO
    $empleado->ESTADO_EMPLEADO = 'INACTIVO';
    $empleado->FECHA_SALIDA = Carbon::now();
    $empleado->save();

    // Registrar en la bitácora
    $this->bitacora->registrarEnBitacora(21, "Empleado con ID $id desactivado exitosamente", 'Update');

    // Redirigir con mensaje de éxito
    return redirect()->route('empleados.index')->with('success', 'Empleado desactivado exitosamente');
}


public function restaurar($id)
{
     // Verificar si el usuario no está autenticado
     if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de actualización en el objeto EMPLEADO
    $permisoActualizacion = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'EMPLEADO')
                ->limit(1);
        })
        ->where('Permiso_Actualizacion', 'PERMITIDO')
        ->exists();

    if (!$permisoActualizacion) {
        $this->bitacora->registrarEnBitacora(21, 'INTENTO DE RESTAURAR EMPLEADO SIN PERMISOS', 'Update');
        return redirect()->route('empleados.index')->withErrors('No tiene permiso para restaurar empleados');
    }

    // Encontrar al empleado por su ID
    $empleado = Empleados::findOrFail($id);

    // Cambiar el estado del empleado a INACTIVO
    $empleado->ESTADO_EMPLEADO = 'ACTIVO';
    $empleado->FEC_INGRESO_EMPLEADO = Carbon::now();
    $empleado->FECHA_SALIDA = null;
    $empleado->save();
    // Registrar en la bitácora
    $this->bitacora->registrarEnBitacora(21, "Empleado con ID $id restaurado", 'Update');

    // Redirigir con mensaje de éxito
    return redirect()->route('empleados.index')->with('success', 'Empleado restaurado exitosamente');
}   

}
