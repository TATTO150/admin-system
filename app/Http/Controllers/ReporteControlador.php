<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteControlador extends Controller
{
    public function index()
    {
        $fechaHoraActual = Carbon::now(); // Obtener la fecha y hora actuales
        return view('reportes.index', compact('fechaHoraActual'));
    }
}
