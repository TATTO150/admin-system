<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mantenimientos;
use App\Models\Estado_Mantenimiento;
use App\Models\Empleados;
use App\Models\Equipos;


class GestionMantenimientoControlador extends Controller
{
    protected $estados;
    protected $empleados;
    protected $equipos;

    public function __construct()
    {
        $this->estados = Estado_Mantenimiento::all();
        $this->empleados = Empleados::all();
        $this->equipos = Equipos::all();
    }

    public function index()
{
    $mantenimientos = Mantenimientos::with(['empleado', 'equipo', 'estado_mantenimiento'])->get();
        
        $mantenimientosPorEstado = $mantenimientos->groupBy(function($item) {
            return $item->estado_mantenimiento->ESTADO ?? 'No asignado';
        });

        $estados = $this->estados->keyBy('COD_ESTADO_MANTENIMIENTO');
        $empleados = $this->empleados->keyBy('COD_EMPLEADO');
        $equipos = $this->equipos->keyBy('COD_EQUIPO');
        
        return view('gestionMantenimiento.index', compact('mantenimientosPorEstado', 'estados', 'empleados', 'equipos'));
}
    public function gestionar($COD_MANTENIMIENTO)
    {
        $mantenimiento = Mantenimientos::where('COD_MANTENIMIENTO', $COD_MANTENIMIENTO)
                                       ->with(['empleado', 'equipo', 'estado_mantenimiento'])
                                       ->first();

        if (!$mantenimiento) {
            abort(404, 'Mantenimiento no encontrado');
        }

        $estados = $this->estados->keyBy('COD_ESTADO_MANTENIMIENTO');
        $empleados = $this->empleados->keyBy('COD_EMPLEADO');
        $equipos = $this->equipos->keyBy('COD_EQUIPO');

        return view('gestionMantenimiento.gestionMantenimiento', compact('mantenimiento', 'estados', 'empleados', 'equipos'));
    }

    public function actualizarEstado(Request $request, $COD_MANTENIMIENTO)
    {
        $mantenimiento = Mantenimientos::findOrFail($COD_MANTENIMIENTO);
        $mantenimiento->COD_ESTADO_MANTENIMIENTO = $request->input('estado');
        $mantenimiento->save();

        return redirect()->route('gestionMantenimiento.index')->with('success', 'Estado de mantenimiento actualizado exitosamente.');
    }
}