<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EstadoEmpleado;
use App\Models\Permisos;
use App\Models\Empleados;
use App\Http\Controllers\BitacoraController;
use App\Providers\PermisoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Rules\Validaciones;
use Barryvdh\DomPDF\Facade\Pdf;

class EstadoEmpleadoControlador extends Controller
{
    protected $bitacora;
    protected $permisoService;

    public function __construct(BitacoraController $bitacora, PermisoService $permisoService)
    {
        $this->bitacora = $bitacora;
        $this->permisoService = $permisoService;  // Inyectar el PermisoService
    }

    public function index()
    {
        $user = Auth::user();
        //Nueva validacvion de permisos
        $this->permisoService->tienePermiso('TIPOEMPLEADO', 'Consultar', true);

        $estados = EstadoEmpleado::where('ESTADO', 'ACTIVO')->get();

        $this->bitacora->registrarEnBitacora(22, 'Ingreso a la ventana de estados', 'Ingreso');
        
        return view('estado_empleados.index', compact('estados'));
    }

    public function pdf()
    {
        $estados = EstadoEmpleado::all();
        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
        // Cambio de img a formato pdf
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    
        // Paginación
        $pdf = Pdf::loadView('estado_empleados.pdf', compact('estados', 'fechaHora', 'logoBase64'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
            ]);

        $this->bitacora->registrarEnBitacora(22, 'Generación de reporte de estados', 'Update');
        
        return $pdf->stream();
    }

    public function crear()
    {
        $user = Auth::user();

         //Nueva validacvion de permisos
         $this->permisoService->tienePermiso('TIPOEMPLEADO', 'Insercion', true);

        return view('estado_empleados.create');
    }

    public function insertar(Request $request)
    {
        // Validar la entrada
        $validator = Validator::make($request->all(), [
            'ESTADO_EMPLEADO' =>[
                'required',
                'string',
                'max:15', // Longitud máxima de 25 caracteres
                'unique:tbl_estado_empleado,ESTADO_EMPLEADO', // Asegura que el nombre del cargo sea único en la tabla tbl_cargos
                'regex:/^[A-ZÑÁÉÍÓÚ\s]+$/', // Solo letras mayúsculas y espacios
                'regex:/^(?!.*\s{2,}).*$/', // Prohibir múltiples espacios consecutivos
            ],
        ], [
            'ESTADO_EMPLEADO.required' => 'El Tipo empleado es obligatorio.',
            'ESTADO_EMPLEADO.string' => 'El Tipo empleado debe ser una cadena de texto.',
            'ESTADO_EMPLEADO.max' => 'El Tipo empleado no puede exceder los 15 caracteres.',
            'ESTADO_EMPLEADO.unique' => 'El Tipo empleado ya existe.',
            'ESTADO_EMPLEADO.regex' => 'El Tipo empleado debe contener solo letras mayúsculas y espacios, sin números ni símbolos especiales, y sin espacios consecutivos.',
             ], [
            'ESTADO_EMPLEADO' => 'Nombre del Tipo',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        EstadoEmpleado::create([
            'ESTADO_EMPLEADO' => $request->ESTADO_EMPLEADO,
            'ESTADO' => 'ACTIVO', 
        ]);

        $this->bitacora->registrarEnBitacora(22, 'Nuevo estado creado', 'Update');
        
        return redirect()->route('estado_empleados.index');
    }

    public function destroy($COD_ESTADO_EMPLEADO)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

         //Nueva validacvion de permisos
         $this->permisoService->tienePermiso('TIPOEMPLEADO', 'Eliminacion', true);

        // Verificar si hay empleados asignados a este estado
        $empleadosAsignados = Empleados::where('COD_ESTADO_EMPLEADO', $COD_ESTADO_EMPLEADO)->exists();

        if ($empleadosAsignados) {
            return redirect()->route('estado_empleados.index')->withErrors('No se puede eliminar el estado porque hay empleados asignados a él.');
        }

        // Encontrar el estado por su ID
        $estado = EstadoEmpleado::findOrFail($COD_ESTADO_EMPLEADO);

        // Cambiar el estado a INACTIVO
        $estado->ESTADO = 'INACTIVO';
        $estado->save();

        $this->bitacora->registrarEnBitacora(22, 'Estado eliminado', 'Update');
        
        return redirect()->route('estado_empleados.index')->with('success', 'Estado eliminado correctamente');
    }

    public function edit($COD_ESTADO_EMPLEADO)
    {
        $user = Auth::user();
        
         //Nueva validacvion de permisos
         $this->permisoService->tienePermiso('TIPOEMPLEADO', 'Actualizacion', true);

        $estado = EstadoEmpleado::findOrFail($COD_ESTADO_EMPLEADO);
    
        return view('estado_empleados.edit', compact('estado'));
    }

    public function update(Request $request, $COD_ESTADO_EMPLEADO)
    {
        // Validar la entrada
        $validator = Validator::make($request->all(), [
            'ESTADO_EMPLEADO' =>[
                'required',
                'string',
                'max:15', // Longitud máxima de 25 caracteres
                'unique:tbl_estado_empleado,ESTADO_EMPLEADO', // Asegura que el nombre del cargo sea único en la tabla tbl_cargos
                'regex:/^[A-ZÑÁÉÍÓÚ\s]+$/', // Solo letras mayúsculas y espacios
                'regex:/^(?!.*\s{2,}).*$/', // Prohibir múltiples espacios consecutivos
            ],
        ], [
            'ESTADO_EMPLEADO.required' => 'El Tipo empleado es obligatorio.',
            'ESTADO_EMPLEADO.string' => 'El Tipo empleado debe ser una cadena de texto.',
            'ESTADO_EMPLEADO.max' => 'El Tipo empleado no puede exceder los 15 caracteres.',
            'ESTADO_EMPLEADO.unique' => 'El Tipo empleado ya existe.',
            'ESTADO_EMPLEADO.regex' => 'El Tipo empleado debe contener solo letras mayúsculas y espacios, sin números ni símbolos especiales, y sin espacios consecutivos.',
             ], [
            'ESTADO_EMPLEADO' => 'Nombre del Tipo',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $estado = EstadoEmpleado::findOrFail($COD_ESTADO_EMPLEADO);
        $estado->ESTADO_EMPLEADO = $request->ESTADO_EMPLEADO;
        $estado->save();

        $this->bitacora->registrarEnBitacora(22, 'Estado actualizado', 'Update');
        
        return redirect()->route('estado_empleados.index');
    }
}
