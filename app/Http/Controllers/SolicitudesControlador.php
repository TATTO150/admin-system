<?php

namespace App\Http\Controllers;

use App\Models\Proyectos;
use App\Models\solitudes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Rules\Validaciones;
use App\Models\Area;
use App\Models\EstadoCompra;
use App\Models\Empleados;
use App\Models\Permisos;
use App\Models\Compras;
use App\Models\user;
use Illuminate\Support\Facades\Mail;
use App\Models\TipoCompra;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Providers\PermisoService;
use App\Mail\NotificacionNuevaSolicitud;

class SolicitudesControlador extends Controller
{
    protected $guard;
    protected $createNewUser;
    protected $areas;
    protected $proyectos;
    protected $empleados;
    protected $bitacora;
    protected $permisoService;

    public function __construct(BitacoraController $bitacora, PermisoService $permisoService)
    {
        $this->areas = Area::all();
        $this->proyectos = proyectos::all();
        $this->empleados = Empleados::all();
        $this->bitacora = $bitacora;
        $this->permisoService = $permisoService;

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
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
        $user = Auth::user();

        // Nueva validación de permisos
        $this->permisoService->tienePermiso('COMPRA', 'Consultar', true);

        // Obtener compras con relaciones
        $compras = Compras::with(['proyecto'])->get();

        // Obtener todos los usuarios, proyectos, estados y tipos
        $usuarios = \App\Models\User::all()->keyBy('Id_usuario');
        $proyectos = \App\Models\Proyectos::all()->keyBy('COD_PROYECTO');
        $estados = \App\Models\EstadoCompra::all()->keyBy('COD_ESTADO');
        $tipos = \App\Models\TipoCompra::all()->keyBy('COD_TIPO');
        $areas = \App\Models\Area::all();

        // Registrar en bitácora
        $this->bitacora->registrarEnBitacora(22, 'Ingreso a la ventana de compras', 'Ingreso');

        // Retornar la vista con los datos necesarios
        return view('solicitudes.index', compact('compras', 'areas', 'usuarios', 'proyectos', 'estados', 'tipos'));
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
        $this->permisoService->tienePermiso('COMPRA', 'Insercion', true);

        $proyectos = Proyectos::whereNotIn('ESTADO_PROYECTO', ['SUSPENDIDO', 'FINALIZADO', 'INACTIVO'])->get();
        $empleados = empleados::where('ESTADO_EMPLEADO', 'ACTIVO')->get();
        $tiposCompra = TipoCompra::all();

         // Concatenar DNI con nombre del empleado
        $empleados = $empleados->map(function ($empleado) {
        $empleado->nombre_con_dni = $empleado->DNI_EMPLEADO. ' - ' . $empleado->NOM_EMPLEADO;
        return $empleado;
    });

        return view('solicitudes.crear', compact('proyectos', 'empleados', 'tiposCompra'));
    }

    public function insertar(Request $request)
{
    // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
    // Validación de datos
    $validator = Validator::make($request->all(), [
        'DESC_COMPRA' => [
            (new Validaciones)->requerirCampo()->requerirTodoMayusculas()->prohibirMultiplesEspacios()->prohibirEspaciosInicioFin(),
            function($attribute, $value, $fail) {
                if (preg_match('/\b\w+[^\w\s]+/', $value)) {
                    $fail('El campo "Descripción de Compra" no puede contener símbolos después de una palabra.');
                }
                if (preg_match('/([A-Z])\1{2,}/', $value)) {
                    $fail('"Descripción de Compra" no puede tener más de dos letras seguidas. Has ingresado: ' . $value);
                }
                if (preg_match('/\s{2,}/', $value)) {
                    $fail('"Descripción de Compra" no puede tener más de un espacio seguido. Has ingresado: ' . $value);
                }
                if (preg_match('/[BCDFGHJKLMNPQRSTVWXYZ]{5}/i', $value)) {
                    $fail('"Descripción de Compra" no puede tener más de cuatro consonantes seguidas en una palabra. Has ingresado: ' . $value);
                }
            },
        ],
        'COD_PROYECTO' => [
            (new Validaciones)->requerirCampo()->requerirSoloNumeros()->requerirEstadoValidoProyecto(),
        ],
        'COD_TIPO' => [
            (new Validaciones)->requerirCampo()->requerirSoloNumeros(), // Validación para tipo de compra
        ],
        'PRECIO_COMPRA' => [
            'nullable', // Este campo será opcional, dependiendo de si se ingresa por cuotas o precio total
            'numeric',
            'min:0',
        ],
        'PRECIO_CUOTA' => [
            'nullable', // Este campo también será opcional
            'numeric',
            'min:0',
        ],
        'TOTAL_CUOTAS' => [
            (new Validaciones)->requerirCampo()->requerirSoloNumeros(),
        ],
    ]);

    // Si la validación falla, redirigir con errores
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Capturar el usuario actual
    $id_usuario = auth()->user()->Id_usuario;

    // Calcular la fecha de pago a un mes a partir de hoy
    $fecha_pago = now()->addMonth();

    // Calcular precios según los campos llenados
    $precio_compra = $request->PRECIO_COMPRA;
    $precio_cuota = $request->PRECIO_CUOTA;
    $total_cuotas = $request->TOTAL_CUOTAS;

    if (!$precio_compra && $precio_cuota) {
        // Si el usuario ingresó el precio por cuota, calcular el precio de compra
        $precio_compra = $precio_cuota * $total_cuotas;
    } elseif ($precio_compra && !$precio_cuota) {
        // Si el usuario ingresó el precio total, calcular el precio por cuota
        $precio_cuota = $precio_compra / $total_cuotas;
    }

    // Crear una nueva instancia del modelo `Compra`
    $compra = new Compras();
    $compra->Id_usuario = $id_usuario;
    $compra->DESC_COMPRA = $request->DESC_COMPRA;
    $compra->COD_PROYECTO = $request->COD_PROYECTO;
    $compra->COD_TIPO = $request->COD_TIPO;
    $compra->FEC_REGISTRO = now(); // Fecha de registro es la fecha actual
    $compra->COD_ESTADO = 1; // Estado inicial
    $compra->PRECIO_COMPRA = $precio_compra;
    $compra->PRECIO_CUOTA = $precio_cuota;
    $compra->PRECIO_NETO = $precio_compra; // Aquí puedes agregar lógica adicional si necesitas calcular otro valor neto
    $compra->TOTAL_CUOTAS = $total_cuotas;
    $compra->CUOTAS_PAGADAS = 0; // Inicialmente sin cuotas pagadas
    $compra->FECHA_PAGO = $fecha_pago;
    $compra->LIQUIDEZ_COMPRA = 0; // Liquidez inicial en cero

    // Guardar en la base de datos
    $compra->save();
    // Obtener el nombre de usuario y el nombre del proyecto
    $usuario = User::find($id_usuario);
    $proyecto = Proyectos::where('COD_PROYECTO', $request->COD_PROYECTO)->first();

    // Enviar el correo con los detalles
    $detalles = [
        'usuario' => $usuario ? $usuario->Nombre_Usuario : 'Desconocido',
        'descripcion' => $request->DESC_COMPRA,
        'proyecto' => $proyecto ? $proyecto->NOM_PROYECTO : 'Desconocido',
        'url' => route('login'),
    ];

    // Obtener todos los correos de los administradores con Id_Rol = 1
    $adminEmails = User::where('Id_Rol', 1)->pluck('Correo_Electronico')->toArray();

    // Verificar que existan correos antes de enviar
    if (count($adminEmails) > 0) {
        // Enviar el correo a los administradores
        Mail::to($adminEmails)->send(new NotificacionNuevaSolicitud($detalles));
    }

    // Registrar en la bitácora
    $this->bitacora->registrarEnBitacora(22, 'Nueva compra creada', 'insertar');

    // Redirigir a la lista de compras
    return redirect()->route('solicitudes.index');
}

    


public function destroy($COD_COMPRA)
{
    // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Nueva validación de permisos para eliminar compras
    $this->permisoService->tienePermiso('COMPRA', 'Eliminacion', true);

    // Obtener la compra a través del modelo Compras
    $compra = Compras::where('COD_COMPRA', $COD_COMPRA)->first();

     // Verificar si la compra ya ha sido liquidada
     if ($compra->LIQUIDEZ_COMPRA === 1) {
        return redirect()->back()->with('error', 'Una compra ya liquidada no puede ser editada.');
    }
    
    if (!$compra) {
        return redirect()->route('solicitudes.index')->withErrors('Compra no encontrada');
    }

    // Verificar si la compra ya ha sido liquidada
    if ($compra->LIQUIDEZ_COMPRA == 1) {
        return redirect()->back()->with('error', 'Una compra ya liquidada no puede ser eliminada.');
    }

    try {
        // Eliminar la compra usando Eloquent
        $compra->delete();

        // Registrar en la bitácora la eliminación de la compra
        $this->bitacora->registrarEnBitacora(22, 'compra eliminada', 'eliminada');
        
        return redirect()->route('solicitudes.index')->with('success', 'Compra eliminada correctamente');
    } catch (\Exception $e) {
        // Registrar en la bitácora el error al eliminar la compra
        $this->bitacora->registrarEnBitacora(22, 'Error al eliminar compra', 'eliminada');
        return redirect()->route('solicitudes.index')->with('error', 'Error al eliminar compra: ' . $e->getMessage());
    }
}


    public function edit($COD_COMPRA)
    {
        // Verificar si el usuario no está autenticado
    if (!Auth::check()) {
        // Redirigir a la vista `sesion_suspendida`
        return redirect()->route('sesion.suspendida');
    }
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Verificación de permisos
        $this->permisoService->tienePermiso('COMPRA', 'Actualizacion', true);
    
        // Obtener datos necesarios
        $areas = Area::all();
        $proyectos = Proyectos::all();
        $empleados = Empleados::all();
        
        // Cambiar el modelo de 'Solitudes' a 'Compras'
        $compra = Compras::where('COD_COMPRA', $COD_COMPRA)->first();
    
        if (!$compra) {
            return redirect()->route('solicitudes.index')->withErrors('Compra no encontrada');
        }
        // Verificar si la compra ya ha sido liquidada
        if ($compra->LIQUIDEZ_COMPRA === 1) {
            return redirect()->back()->with('error', 'Una compra ya liquidada no puede ser editada.');
        }

    
        // Concatenar DNI con nombre del empleado
        $empleados = $empleados->map(function ($empleado) {
            $empleado->nombre_con_dni = $empleado->DNI_EMPLEADO . ' - ' . $empleado->NOM_EMPLEADO;
            return $empleado;
        });
    
        return view('solicitudes.edit', compact('compra', 'areas', 'proyectos', 'empleados'));
    }
    


    public function update(Request $request, $COD_COMPRA)
{
    // Validación de datos
    $validator = Validator::make($request->all(), [
        'DESC_COMPRA' => [
            (new Validaciones)->requerirCampo()->requerirTodoMayusculas()
                ->prohibirMultiplesEspacios()
                ->prohibirEspaciosInicioFin(),
            'required',
            'string',
            'min:10',
            function($attribute, $value, $fail) {
                if (preg_match('/([A-Z])\1{2,}/', $value)) {
                    $fail('"Descripción Compra" no puede tener más de dos letras seguidas. Has ingresado: ' . $value);
                }
                if (preg_match('/\s{2,}/', $value)) {
                    $fail('"Descripción Compra" no puede tener más de un espacio seguido. Has ingresado: ' . $value);
                }
                if (preg_match('/[BCDFGHJKLMNPQRSTVWXYZ]{5}/i', $value)) {
                    $fail('"Descripción Compra" no puede tener más de cuatro consonantes seguidas en una palabra. Has ingresado: ' . $value);
                }
                if (preg_match('/\b\w+\b[\W_]+$/', $value)) {
                    $fail('"Descripción Compra" no puede contener símbolos después de una palabra. Has ingresado: ' . $value);
                }
            },
        ],
        'COD_PROYECTO' => [
            (new Validaciones)->requerirCampo()->requerirSoloNumeros(),
            'required',
            'integer',
        ],
        'TOTAL_CUOTAS' => [
            'required',
            'integer',
            'min:1',
            'max:999',
        ],
    ], [], [
        'COD_EMPLEADO' => 'Código de Empleado',
        'DESC_COMPRA' => 'Descripción de Compra',
        'COD_PROYECTO' => 'Código de Proyecto',
        'PRECIO_COMPRA' => 'Precio de Compra',
        'CUOTAS_PAGADAS' => 'Cuotas Pagadas',
        'TOTAL_CUOTAS' => 'Total de Cuotas',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Buscar la compra en el modelo
    $compra = Compras::find($COD_COMPRA);
    if (!$compra) {
        return redirect()->back()->with('error', 'Compra no encontrada.');
    }

    if($request->COD_TIPO == 1 && $request->TOTAL_CUOTAS > 1){
        return redirect()->back()->withErrors('Una compra al contado no puede tener mas de una cuota');
    }

    // Actualizar los campos del modelo
    $compra->DESC_COMPRA = $request->DESC_COMPRA;
    $compra->COD_PROYECTO = $request->COD_PROYECTO;

    // Calcular el nuevo precio compra y el precio neto
    $compra->PRECIO_COMPRA = $request->TOTAL_CUOTAS * $request->PRECIO_CUOTA;
    $compra->PRECIO_NETO = $compra->PRECIO_COMPRA; // Aquí puedes añadir otros cálculos si es necesario

    // Actualizar cuotas pagadas y total de cuotas
    $compra->CUOTAS_PAGADAS = $compra->CUOTAS_PAGADAS;
    $compra->TOTAL_CUOTAS = $request->TOTAL_CUOTAS;

    // Guardar los cambios
    $compra->save();

    // Registrar en bitácora si es necesario
    // $this->bitacora->registrarEnBitacora(Auth::id(), 5, 'Compra actualizada', 'Update'); // ID_objetos 5: 'compras'

    return redirect()->route('solicitudes.index')->with('success', 'Compra actualizada exitosamente.');
}


public function generateReport(Request $request)
{
    $reportType = $request->query('reportType');
    $filterName = null;
    $filterValue = null;

    $solicitudes = Compras::query();

    if ($reportType === 'usuario' && $request->query('usuario')) {
        $usuarioId = $request->query('usuario');
        $solicitudes->where('Id_usuario', $usuarioId);
        $filterName = 'Usuario';
        $filterValue = User::find($usuarioId)->Usuario ?? 'Desconocido';
    } elseif ($reportType === 'proyecto' && $request->query('proyecto')) {
        $proyectoId = $request->query('proyecto');
        $solicitudes->where('COD_PROYECTO', $proyectoId);
        $filterName = 'Proyecto';
        $filterValue = Proyectos::find($proyectoId)->NOM_PROYECTO ?? 'Desconocido';
    } elseif ($reportType === 'estado' && $request->query('estado')) {
        $estadoId = $request->query('estado');
        $solicitudes->where('COD_ESTADO', $estadoId);
        $filterName = 'Estado de Compra';
        $filterValue = EstadoCompra::find($estadoId)->DESC_ESTADO ?? 'Desconocido';
    } elseif ($reportType === 'tipo' && $request->query('tipo')) {
        $tipoId = $request->query('tipo');
        $solicitudes->where('COD_TIPO', $tipoId);
        $filterName = 'Tipo de Compra';
        $filterValue = TipoCompra::find($tipoId)->DESC_TIPO ?? 'Desconocido';
    } else {
        $filterName = 'General';
        $filterValue = 'Todos los registros';
    }

    $solicitudes = $solicitudes->with(['usuario', 'proyecto', 'tipocompras', 'estadocompras'])->get();

    if ($solicitudes->isEmpty()) {
        return response()->json(['registros' => 0], 200);
    }

    $fechaHora = now()->format('d-m-Y H:i:s');
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    $pdf = Pdf::loadView('solicitudes.reporte_general', compact('solicitudes', 'fechaHora', 'logoBase64', 'filterName', 'filterValue'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    $fileName = 'reporte_' . time() . '.pdf';
    $pdf->save(public_path($fileName));

    return response()->json(['registros' => $solicitudes->count(), 'pdfUrl' => asset($fileName)], 200);
}



    }


