<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FechaController extends Controller

{

    public static function fechaHastaTresMeses()
    {
        // Obtener la fecha actual
        $fechaActual = Carbon::now();

        // Clonar la fecha actual y sumar tres meses
        $fechaTresMeses = $fechaActual->copy()->addMonths(3);

        // Formatear la fecha para mostrar solo día, mes y año
        $fechaFormateada = $fechaTresMeses->format('Y-m-d');

        // Retornar la fecha formateada
        return $fechaFormateada;
    }

}
