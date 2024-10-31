<?php

namespace App\Http\Controllers;

use App\Models\Equipos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\TipoEquipo;
use App\Models\EstadoEquipo;
use App\Models\Permisos;
use Illuminate\Support\Facades\Auth;



class EquipoControlador extends Controller
{
    protected $apiBaseUrl = 'http://localhost:3000';
    protected $bitacora;
    protected $estadoEquipo;
    protected $tipos_equipo;

    public function __construct(BitacoraController $bitacora)
    {
        $this->bitacora = $bitacora;
        $this->estadoEquipo = EstadoEquipo::all();
        $this->tipos_equipo = TipoEquipo::all();
    }

    // Métodos auxiliares
    private function fetchApiData($endpoint)
    {
        $response = Http::get("{$this->apiBaseUrl}/{$endpoint}");
        return $response->successful() ? $response->json() : [];
    }

    private function validateEquipo(Request $request, $isUpdate = false, $currentEquipoName = null)
    {
        $messages = [
            'NOM_EQUIPO.required' => '"Nombre del Equipo" es obligatorio.',
            'NOM_EQUIPO.string' => '"Nombre del Equipo" debe ser una cadena de texto.',
            'NOM_EQUIPO.min' => '"Nombre del Equipo" debe tener al menos 3 caracteres. Has ingresado: :input',
            'NOM_EQUIPO.max' => '"Nombre del Equipo" no debe exceder los 20 caracteres. Has ingresado: :input',
            'COD_TIP_EQUIPO.required' => '"Tipo de Equipo" es obligatorio.',
            'DESC_EQUIPO.required' => '"Descripción del Equipo" es obligatorio.',
            'DESC_EQUIPO.string' => '"Descripción del Equipo" debe ser una cadena de texto.',
            'DESC_EQUIPO.min' => '"Descripción del Equipo" debe tener al menos 10 caracteres. Has ingresado: :input',
            'FECHA_COMPRA.required' => '"Fecha de Compra" es obligatorio.',
            'FECHA_COMPRA.date' => '"Fecha de Compra" debe ser una fecha válida. Has ingresado: :input',
            'FECHA_COMPRA.before_or_equal' => '"Fecha de Compra" no puede ser una fecha futura. Has ingresado: :input',
            'VALOR_EQUIPO.required' => '"Valor del Equipo" es obligatorio.',
            'VALOR_EQUIPO.numeric' => '"Valor del Equipo" debe ser un número. Has ingresado: :input',
            'VALOR_EQUIPO.min' => '"Valor del Equipo" debe ser mayor o igual a 0. Has ingresado: :input',
            'VALOR_EQUIPO.max' => '"Valor del Equipo" no debe exceder los 100,000,000. Has ingresado: :input',
            'VALOR_EQUIPO.regex' => 'El campo "Valor del Equipo" no puede contener decimales; debe ser un número entero. Has ingresado: :input',
        ];

        return Validator::make($request->all(), [
            'NOM_EQUIPO' => [
                'required',
                'string',
                'min:3',
                'max:20',
                function($attribute, $value, $fail) use ($isUpdate, $currentEquipoName) {
                    if (preg_match('/^\d/', $value)) {
                        $fail('El campo "Nombre del Equipo" no puede comenzar con un número. Has ingresado: ' . $value);
                    }
                    if (preg_match('/^\W/', $value)) {
                        $fail('El campo "Nombre del Equipo" no puede comenzar con un símbolo especial. Has ingresado: ' . $value);
                    }
                    if (preg_match('/([A-Z])\1{2,}/', $value)) {
                        $fail('El campo "Nombre del Equipo" no puede tener más de dos letras seguidas. Has ingresado: ' . $value);
                    }
                    if (preg_match('/[\W_]{2,}/', $value)) {
                        $fail('El campo "Nombre del Equipo" no puede tener símbolos especiales consecutivos. Has ingresado: ' . $value);
                    }
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('El campo "Nombre del Equipo" no puede tener más de un espacio seguido. Has ingresado: ' . $value);
                    }
                    if (preg_match('/\b(I|V|X|L|C|D|M)\b/', $value)) {
                        $fail('El campo "Nombre del Equipo" no puede contener números romanos. Has ingresado: ' . $value);
                    }
                    if (!preg_match('/[AEIOU]/', $value) || !preg_match('/[BCDFGHJKLMNPQRSTVWXYZ]/', $value)) {
                        $fail('El campo "Nombre del Equipo" debe contener al menos una vocal y una consonante. Has ingresado: ' . $value);
                    }
                    if (preg_match('/[0-9]$/', $value)) {
                        $fail('El campo "Nombre del Equipo" no puede terminar con un número. Has ingresado: ' . $value);
                    }

                    // Si no es una actualización o el nombre ha cambiado
                    if (!$isUpdate || strtolower($currentEquipoName) !== strtolower($value)) {
                        $equiposExistentes = collect($this->fetchApiData('Equipos'));
                        if ($equiposExistentes->first(fn($equipo) => strtolower($equipo['NOM_EQUIPO']) == strtolower($value))) {
                            $fail('El campo "Nombre del Equipo" ya existe en la base de datos. Has ingresado: ' . $value);
                        }
                    }
                },
            ],
            'COD_TIP_EQUIPO' => ['required', 'integer'],
            'DESC_EQUIPO' => [
                'required',
                'string',
                'min:10',
                function($attribute, $value, $fail) {
                    if (preg_match('/([A-Z])\1{2,}/', $value)) {
                        $fail('"Descripcion" no puede tener más de dos letras seguidas. Has ingresado: ' . $value);
                    }
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('"Descripción" no puede tener más de un espacio seguido. Has ingresado: ' . $value);
                    }
                    if (preg_match('/[BCDFGHJKLMNPQRSTVWXYZ]{5}/i', $value)) {
                        $fail('"Descripción" no puede tener más de cuatro consonantes seguidas en una palabra. Has ingresado: ' . $value);
                    }
                },
            ],
            'COD_ESTADO_EQUIPO' => ['required', 'integer'],
            'FECHA_COMPRA' => [
                'required',
                'date',
                'before_or_equal:today', // Validar que la fecha de compra no sea futura
            ],
            'VALOR_EQUIPO' => [
                'required',
                'numeric',
                'min:0',
                'max:100000000',
                'regex:/^\d+$/', // No permitir decimales en el valor del equipo
            ],
        ], $messages);
    }

    public function index(Request $request)
{
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de consulta en el objeto EQUIPO
    $permisoConsultar = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'EQUIPO')
                ->limit(1);
        })
        ->where('Permiso_Consultar', 'PERMITIDO')
        ->exists();

    if (!$permisoConsultar) {
        $this->bitacora->registrarEnBitacora(18, 'Intento de ingreso a la ventana de equipo sin permisos', 'Ingreso');
        return redirect()->route('dashboard')->withErrors('No tiene permiso para ingresar a la ventana de equipos');
    }

    // Obtener los datos de la API (Equipos, Tipos, y Estados)
    $equipos = collect($this->fetchApiData('Equipos'));
    $tipos = collect($this->fetchApiData('TiposEquipo'));
    $estados = collect($this->fetchApiData('EstadosEquipo'));

    // Filtrar equipos según el parámetro show_inactivos
    if ($request->has('show_inactivos') && $request->input('show_inactivos') === 'true') {
        // Mostrar solo los inactivos (COD_ESTADO_EQUIPO = 3)
        $equipos = $equipos->filter(function ($equipo) {
            return $equipo['COD_ESTADO_EQUIPO'] == 3; // Mostrar solo los inactivos
        });
    } else {
        // Mostrar todos los equipos excepto los inactivos
        $equipos = $equipos->filter(function ($equipo) {
            return $equipo['COD_ESTADO_EQUIPO'] != 3; // Excluir los inactivos
        });
    }

    // Mapear los nombres de los tipos y estados de los equipos para evitar información "Desconocida"
    $equipos = $equipos->map(function ($equipo) use ($tipos, $estados) {
        $equipo['TIPO_EQUIPO_NOMBRE'] = $tipos->firstWhere('COD_TIP_EQUIPO', $equipo['COD_TIP_EQUIPO'])['TIPO_EQUIPO'] ?? 'Desconocido';
        $equipo['ESTADO_EQUIPO_NOMBRE'] = $estados->firstWhere('COD_ESTADO_EQUIPO', $equipo['COD_ESTADO_EQUIPO'])['DESC_ESTADO_EQUIPO'] ?? 'Desconocido';
        return $equipo;
    });

    // Ordenar los equipos por la fecha de compra (FECHA_COMPRA) de manera descendente
    $equipos = $equipos->sortByDesc(function ($equipo) {
        return \Carbon\Carbon::parse($equipo['FECHA_COMPRA']);
    });

    // Asegurar que el estado del equipo se haya actualizado correctamente
    return view('equipos.index', compact('equipos', 'estados'));
}

    public function restaurar($id)
    {
        // Obtener el equipo específico de la API
        $response = Http::get("http://localhost:3000/Equipos/{$id}");
    
        if (!$response->successful()) {
            return redirect()->route('equipos.index')->with('error', 'El equipo no se encontró');
        }
    
        $equipo = $response->json();
    
        // Actualizar el estado del equipo a "Sin Asignar" (estado = 1) y otros campos importantes
        $response = Http::put("http://localhost:3000/Equipos/{$id}", [
            'NOM_EQUIPO' => $equipo['NOM_EQUIPO'],
            'COD_TIP_EQUIPO' => $equipo['COD_TIP_EQUIPO'],
            'DESC_EQUIPO' => $equipo['DESC_EQUIPO'],
            'COD_ESTADO_EQUIPO' => 1, // Estado "Sin Asignar"
            'FECHA_COMPRA' => $equipo['FECHA_COMPRA'],
            'VALOR_EQUIPO' => $equipo['VALOR_EQUIPO'],
            
        ]);
    
        if ($response->successful()) {
            $this->bitacora->registrarEnBitacora(18, 'Restauración de equipo a Sin Asignar', 'Actualización');
            return redirect()->route('equipos.index')->with('success', 'El equipo ha sido restaurado correctamente y ahora está en estado sin asignar');
        } else {
            return redirect()->route('equipos.index')->with('error', 'Error al intentar restaurar el equipo.');
        }
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
                    ->where('Objeto', 'EQUIPO')
                    ->limit(1);
            })
            ->where('Permiso_Insercion', 'PERMITIDO')
            ->exists();

        if (!$permisoInsercion) {
            $this->bitacora->registrarEnBitacora(18, 'Intento de generar equipo sin permisos', 'Insert');
            return redirect()->route('equipos.index')->withErrors('No tiene permiso para generar equipo');
        }

        $tipos_equipo = $this->fetchApiData('TiposEquipo');
        $estados_equipo = $this->fetchApiData('EstadosEquipo');

        return view('equipos.crear', compact('tipos_equipo', 'estados_equipo'));
    }

    public function insertar(Request $request)
    {
        // Establecer el estado del equipo como "Sin asignar" (3) por defecto
        $request->merge(['COD_ESTADO_EQUIPO' => 1]);
    
        // Validar los datos de entrada (ahora que COD_ESTADO_EQUIPO ya está establecido)
        $validator = $this->validateEquipo($request);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
            // Verificar si es una actualización, en cuyo caso no se debe verificar si el equipo ya existe
        if (!$request->has('isUpdate') || !$request->input('isUpdate')) {
            $equiposExistentes = collect($this->fetchApiData('Equipos'));
    
            if ($equiposExistentes->first(fn($equipo) => strtolower($equipo['NOM_EQUIPO']) == strtolower($request->NOM_EQUIPO))) {
                return redirect()->back()->withErrors(['NOM_EQUIPO' => 'El equipo ya existe'])->withInput();
            }
        }
    
        // Insertar el equipo
        Http::post("{$this->apiBaseUrl}/INS_EQUIPO", $request->all());
    
        // Registrar en bitácora
        $this->bitacora->registrarEnBitacora(18, 'Nuevo equipo creado', 'Insert');
    
        // Obtener el último equipo insertado
        $nuevoEquipo = collect($this->fetchApiData('Equipos'))->last();
    
        // Redirigir con mensaje de éxito
        return redirect()->route('equipos.index')->with([
            'success' => 'Equipo creado exitosamente',
            'nuevoEquipo' => $nuevoEquipo['COD_EQUIPO']
        ]);
    }
    

    public function asignarNuevoEquipo(Request $request)
    {
        if ($request->input('asignar') == 'si') {
            $nuevoEquipo = collect($this->fetchApiData('Equipos'))->last();

            if (!empty($nuevoEquipo)) {
                return redirect()->route('asignaciones.crear', ['codigoEquipo' => $nuevoEquipo['COD_EQUIPO']]);
            }
        }

        return redirect()->route('equipos.index')->with('success', 'Equipo creado correctamente');
    }

    public function edit($COD_EQUIPO)
{
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de actualización en el objeto EQUIPO
    $permisoActualizar = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'EQUIPO')
                ->limit(1);
        })
        ->where('Permiso_Actualizacion', 'PERMITIDO')
        ->exists();

    if (!$permisoActualizar) {
        $this->bitacora->registrarEnBitacora(18, 'Intento de actualizar equipo sin permisos', 'Update');
        return redirect()->route('equipos.index')->withErrors('No tiene permiso para editar equipos');
    }

    // Obtener los datos del equipo
    $equipo = $this->fetchApiData("Equipos/{$COD_EQUIPO}");
    $equipo = Equipos::with('estadoEquipo')->find($COD_EQUIPO);

    // Verificar si el equipo no existe o está inactivo
    if (empty($equipo)) {
        return redirect()->route('equipos.index')->with('error', 'Equipo no encontrado');
    }

 
    // Verificar si el estado del equipo es INACTIVO
    if ($equipo->estadoEquipo ->DESC_ESTADO_EQUIPO === 'INACTIVO') {
        return redirect()->route('equipos.index')->with('error', 'No se puede editar un equipo inactivo');
    }
    // Obtener los datos adicionales necesarios
    $tipos_equipo = $this->fetchApiData('TiposEquipo');
    $estados_equipo = $this->fetchApiData('EstadosEquipo');

    return view('equipos.edit', compact('equipo', 'tipos_equipo', 'estados_equipo'));
}


    public function update(Request $request, $COD_EQUIPO)
    {
        // Obtener los datos actuales del equipo
        $equipo = collect($this->fetchApiData("Equipos/{$COD_EQUIPO}"));
    
       
    
        // Mantener el estado actual del equipo
        $data = $request->all();
        $data['COD_ESTADO_EQUIPO'] = $equipo->get('COD_ESTADO_EQUIPO');
    
        // Validar los datos, incluyendo el estado del equipo
        $validator = $this->validateEquipo(new Request($data), true, $equipo->get('NOM_EQUIPO'));
    
        if ($validator->fails()) {
            $this->bitacora->registrarEnBitacora(22, 'Error en la actualización de equipo', 'Update');
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Enviar los datos completos, incluyendo el estado del equipo
        Http::put("{$this->apiBaseUrl}/Equipos/{$COD_EQUIPO}", $data);
        $this->bitacora->registrarEnBitacora(18, 'Equipo actualizado', 'Update');
    
        return redirect()->route('equipos.index')->with('success', 'Equipo actualizado correctamente');
    }
    

    public function destroy($COD_EQUIPO)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Verificar si el rol del usuario tiene el permiso de eliminación en el objeto PROYECTO
        $permisoEliminar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'EQUIPO')
                    ->limit(1);
            })
            ->where('Permiso_Eliminacion', 'PERMITIDO')
            ->exists();
    
        if (!$permisoEliminar) {
            $this->bitacora->registrarEnBitacora(18, 'Intento de eliminar equipo sin permisos', 'Delete');
            return redirect()->route('equipos.index')->withErrors('No tiene permiso para eliminar equipos');
        }
    
        $equipo = collect($this->fetchApiData("Equipos/{$COD_EQUIPO}"));
    
        if ($equipo->isEmpty()) {
            return redirect()->route('equipos.index')->with('error', 'Error al obtener datos del equipo');
        }
    
        $estadoEquipo = $equipo->get('COD_ESTADO_EQUIPO');
        if ($estadoEquipo ==2) { // Estado 2 significa "Asignado"
            return redirect()->route('equipos.index')->with('error', 'No se puede borrar el equipo porque está Asignado.');
        } 
    
        try {
            // Actualizar el estado del equipo a "Inactivo" (estado 4)
            $response = Http::put("{$this->apiBaseUrl}/Equipos/{$COD_EQUIPO}", [
                'NOM_EQUIPO' => $equipo->get('NOM_EQUIPO'),
                'COD_TIP_EQUIPO' => $equipo->get('COD_TIP_EQUIPO'),
                'DESC_EQUIPO' => $equipo->get('DESC_EQUIPO'),
                'COD_ESTADO_EQUIPO' => 3, // Estado "Inactivo"
                'FECHA_COMPRA' => $equipo->get('FECHA_COMPRA'),
                'VALOR_EQUIPO' => $equipo->get('VALOR_EQUIPO')
            ]);
    
            if ($response->successful()) {
                $this->bitacora->registrarEnBitacora(18, 'Equipo inactivado', 'Update');
                return redirect()->route('equipos.index')->with('success', 'Equipo inactivado correctamente');
            } else {
                return redirect()->route('equipos.index')->with('error', "Error al inactivar equipo. Código de respuesta: {$response->status()}");
            }
        } catch (\Exception $e) {
            $this->bitacora->registrarEnBitacora(18, 'Error al inactivar equipo', 'Update');
            return redirect()->route('equipos.index')->with('error', 'Error al inactivar equipo. Excepción: ' . $e->getMessage());
        }
    }
    
// Reporte General
public function generarReporteGeneral()
{
    $equipos = Equipos::all(); // Obtener todos los equipos

    if ($equipos->isEmpty()) {
        return response()->json(['error' => 'No se encontraron equipos.'], 400);
    }

    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    $pdf = Pdf::loadView('equipos.pdf_reporte_general', compact('equipos', 'fechaHora', 'logoBase64'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    return $pdf->stream();
}

// Reporte por Estado
public function generarReporteEstado(Request $request)
{
    $estado = $request->input('estado'); // Obtener el estado seleccionado

    if (!$estado) {
        return response()->json(['error' => 'Debe seleccionar un estado.'], 400);
    }

    $equipos = Equipos::where('COD_ESTADO_EQUIPO', $estado)->get(); // Filtrar los equipos por estado

    if ($equipos->isEmpty()) {
        return response()->json(['error' => 'No se encontraron equipos para el estado seleccionado.'], 400);
    }

    $estadoNombre = \App\Models\EstadoEquipo::find($estado)->DESC_ESTADO_EQUIPO;
    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    $pdf = Pdf::loadView('equipos.pdf_reporte_estado', compact('equipos', 'fechaHora', 'logoBase64', 'estadoNombre'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    return $pdf->stream();
}

// Reporte por Fecha
public function generarReporteFecha(Request $request)
{
    $fechaInicio = Carbon::parse($request->input('fecha_inicio'))->startOfDay();
    $fechaFin = Carbon::parse($request->input('fecha_fin'))->endOfDay();

    if ($fechaInicio > $fechaFin) {
        return response()->json(['error' => 'La fecha de inicio no puede ser mayor que la fecha de fin.'], 400);
    }

    $equipos = Equipos::whereBetween('FECHA_COMPRA', [$fechaInicio, $fechaFin])->get(); // Filtrar los equipos por rango de fechas

    if ($equipos->isEmpty()) {
        return response()->json(['error' => 'No se encontraron equipos en el rango de fechas seleccionado.'], 400);
    }

    $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
    $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

    $pdf = Pdf::loadView('equipos.pdf_reporte_fecha', compact('equipos', 'fechaHora', 'logoBase64', 'fechaInicio', 'fechaFin'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

    return $pdf->stream();
}


    
}
