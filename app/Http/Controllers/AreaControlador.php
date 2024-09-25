<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BitacoraController;
use App\Providers\PermisoService;
use App\Models\Area;
use App\Models\Permisos;
use App\Models\empleados;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Rules\Validaciones;
use Barryvdh\DomPDF\Facade\Pdf;

class AreaControlador extends Controller
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
    $this->permisoService->tienePermiso('AREA', 'Consultar', true);

    $areas = Area::where('ESTADO', 'ACTIVO')->get();

        $this->bitacora->registrarEnBitacora(22, 'Ingreso a la ventana de areas', 'Ingreso');
        
        return view('areas.index', compact('areas'));
    }

    public function pdf(){
        $areas=Area::all();
        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
        //cambio de img a formato pdf
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    
        //paginacion
        $pdf = Pdf::loadView('areas.pdf', compact('areas', 'fechaHora', 'logoBase64'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

        $this->bitacora->registrarEnBitacora(22, 'Generacion de reporte de areas', 'Update');
        
        return $pdf->stream();
    }

    public function crear()
    {
        $user = Auth::user();
        
        //Nueva validacvion de permisos
        $this->permisoService->tienePermiso('AREA', 'Insercion', true);

        return view('areas.crear');
    }

    public function insertar(Request $request)
    {
        // Validar la entrada
    $validator = Validator::make($request->all(), [
        'NOM_AREA' => [
            (new Validaciones)
                ->requerirCampo()
                ->prohibirNumerosSimbolos()
                ->requerirTodoMayusculas()
                ->requerirLongitudMaxima(25)
                ->prohibirMultiplesEspacios()
                ->prohibirEspaciosInicioFin()
        ],
    ], [
        'NOM_AREA.requerirCampo' => 'El campo :attribute es obligatorio.',
        'NOM_AREA.prohibirNumerosSimbolos' => 'El campo :attribute no puede contener números ni símbolos.',
        'NOM_AREA.requerirTodoMayusculas' => 'El campo :attribute debe estar en mayúsculas.',
        'NOM_AREA.requerirSinEspacios' => 'El campo :attribute no puede contener espacios.',
    ], [
        'NOM_AREA' => 'Nombre del Área',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $area = Area::create([
        'NOM_AREA' => $request->NOM_AREA,
        'ESTADO' => 'ACTIVO' // Puedes establecer el estado si es necesario
    ]);
        $this->bitacora->registrarEnBitacora(22, 'Nueva area creada', 'Update');
        
        return redirect()->route('areas.index');
    }

    public function destroy($COD_AREA)
{
    $user = Auth::user();
    
    //Nueva validacvion de permisos
    $this->permisoService->tienePermiso('AREA', 'Eliminacion', true);

    // Verificar si hay empleados asignados a esta área
    $empleadosAsignados = empleados::where('COD_AREA', $COD_AREA)->exists();

    if ($empleadosAsignados) {
        return redirect()->route('areas.index')->withErrors('No se puede eliminar el área porque hay empleados asignados a ella.');
    }

    // Encontrar el área por su ID
    $area = Area::findOrFail($COD_AREA);

    // Cambiar el estado del área a INACTIVO
    $area->ESTADO = 'INACTIVO';
    $area->save();

    return redirect()->route('areas.index')->with('success', 'Área eliminada correctamente');
}


public function edit($COD_AREA)
{
    // Obtener el usuario autenticado y su rol
    $user = Auth::user();
    
    //Nueva validacvion de permisos
    $this->permisoService->tienePermiso('AREA', 'Actualizacion', true);

    // Buscar el área en la base de datos usando el modelo Area
    $areas = Area::find($COD_AREA);

    // Verificar si el área existe
    if (!$areas) {
        return redirect()->route('areas.index')->withErrors('Área no encontrada');
    }

    // Retornar la vista de edición con el área encontrada
    return view('areas.edit', compact('areas'));
}

public function update(Request $request, $COD_AREA)
{
    // Validar la entrada
    $validator = Validator::make($request->all(), [
        'NOM_AREA' => [
            (new Validaciones)
                ->requerirCampo()
                ->prohibirNumerosSimbolos()
                ->requerirTodoMayusculas()
                ->requerirLongitudMaxima(25)
                ->prohibirMultiplesEspacios()
                ->prohibirEspaciosInicioFin()
        ],
    ], [
        'NOM_AREA.requerirCampo' => 'El campo :attribute es obligatorio.',
        'NOM_AREA.prohibirNumerosSimbolos' => 'El campo :attribute no puede contener números ni símbolos.',
        'NOM_AREA.requerirTodoMayusculas' => 'El campo :attribute debe estar en mayúsculas.',
        'NOM_AREA.requerirSinEspacios' => 'El campo :attribute no puede contener espacios.',
    ], [
        'NOM_AREA' => 'Nombre del Área',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Buscar el área por su COD_AREA
    $area = Area::find($COD_AREA);

    // Verificar si el área existe
    if (!$area) {
        return redirect()->route('areas.index')->withErrors('Área no encontrada');
    }

    // Actualizar el área con los nuevos datos
    $area->NOM_AREA = $request->NOM_AREA;
    $area->save(); // Guardar los cambios en la base de datos

    // Registrar en bitácora la actualización
    $this->bitacora->registrarEnBitacora(22, 'Área actualizada', 'Update'); // ID_objetos 22: 'área'

    // Redirigir al índice de áreas con éxito
    return redirect()->route('areas.index')->with('success', 'Área actualizada exitosamente');
}
}
