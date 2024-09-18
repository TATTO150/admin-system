<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;   //PARA CORREO
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class Validaciones implements Rule
{
    protected $requerirUnEspacio = false;
    protected $requerirMayuscula = false;
    protected $requerirMinuscula = false;
    protected $requerirNumero = false;
    protected $requerirSimbolo = false;
    protected $requerirSinEspacios = false;
    protected $requerirArroba = false;
    protected $requerirTodoMayusculas = false;
    protected $longitudMinima = 0;
    protected $longitudMaxima = 0;
    protected $campoRequerido = false;
    protected $prohibirNumerosSimbolos = false;
    protected $requerirValorMinimo = false;
    protected $requerirSoloNumeros = false;
    protected $valorMinimo = 1;
    protected $requerirCorreoUnico = false;
    protected $validarDni = false;
    protected $validarCampoUnico = false;
    protected $requerirFechaValidaProyecto = false;
    protected $mensaje;
    protected $prohibirCorreoUnico = false;
    protected $tablaUnica;
    protected $campoUnico;
    protected $validator;
    protected $requerirEstadoValidoProyecto = false;
    protected $fechaInicial;
    protected $fechaFinal;
    protected $campo;
    protected $tabla;
    protected $id;
    protected $validarAnoConRegistros = false;
    protected $validarFechaConRegistros = false;
    protected $validarMesConRegistros = false;
    protected $validarExistenciaDeReportes = false;
    protected $validarRegistrosHoy = false;
    protected $prohibirMultiplesEspacios = false;  //NUEVO
    protected $requerirFechaIngresoValida = false;
    protected $requerirFechaIngresoMinima = false;
    protected $fechaMinima;
    protected $prohibirCeroYNegativos = false;
    protected $prohibirSimbolosSalvoDecimal = false;
    protected $mes;
    protected $anio;
    protected $prohibirEspaciosInicioFin = false;
  
public function prohibirCadenaLargaSinEspacios()
{
    return function ($attribute, $value, $fail) {
        if (strlen($value) > 15 && !preg_match('/\s/', $value)) {
            $fail('El campo ' . $attribute . ' no puede ser una cadena larga sin espacios o una secuencia de caracteres aparentemente aleatoria.');
        }
    };
}


public function prohibirSecuenciasRepetidas()
{
    return function ($attribute, $value, $fail) {
        // Expresión regular para encontrar secuencias de más de 3 caracteres repetidos
        if (preg_match('/(.)\1{3,}/', $value)) {
            $fail("El campo $attribute no puede contener secuencias de más de 3 caracteres repetidos consecutivos.");
        }
    };
}

public function prohibirInicioConEspacio()
    {
        return function ($attribute, $value, $fail) {
            // Verifica si el valor comienza con un espacio en blanco
            if (preg_match('/^\s/', $value)) {
                $fail("El campo $attribute no puede comenzar con un espacio.");
            }
        };
    }



public function validarAnoActual()
    {
        return function ($attribute, $value, $fail) {
            $anoActual = Carbon::now()->year;
            $anoFecha = Carbon::parse($value)->year;

            if ($anoFecha != $anoActual) {
                $fail("El campo $attribute debe estar dentro del año $anoActual.");
            }
        };
    }

public function nombreUnico($id = null)
    {
        return function ($attribute, $value, $fail) use ($id) {
            // Consulta para verificar si el nombre del proyecto ya existe
            $query = DB::table('tbl_proyectos')
                        ->where('NOM_PROYECTO', $value);

            // Si se proporciona un ID, excluye el proyecto actual (para la edición)
            if ($id) {
                $query->where('id', '<>', $id);
            }

            if ($query->exists()) {
                $fail('El nombre del proyecto ya existe.');
            }
        };
    }

    

//PROHIBIDO LOS SIMBOLOS 
public function prohibirSimbolos()
    {
        return function ($attribute, $value, $fail) {
            // Expresión regular para permitir solo letras y números
            if (!preg_match('/^[a-zA-Z0-9]*$/', $value)) {
                $fail('El :attribute no puede contener símbolos especiales, ni espacios.');
            }
        };
    }
public function validarFechaFutura()
    {
        return function ($attribute, $value, $fail) {
            $fechaHoy = Carbon::now()->startOfDay(); // Obtiene la fecha de hoy sin la hora

            if ($value < $fechaHoy->toDateString()) {
                $fail('La fecha final debe ser al menos la fecha actual o en el futuro.');
            }
        };
    }

    public function prohibirEspaciosInicioFin()
{
    return function ($attribute, $value, $fail) {
        // Verifica si el valor comienza o termina con un espacio en blanco
        if (preg_match('/^\s|\s$/', $value)) {
            $fail('El :attribute no puede comenzar ni terminar con espacios en blanco.');
        }
    };
}


//FECHA INICIO NO PUEDE SER MENOR QUE HOY 
public function validarFechaNoMenorQueHoy()
{
    return function ($attribute, $value, $fail) {
        $fechaHoy = Carbon::now()->startOfDay(); // Obtiene la fecha de hoy sin la hora

        if ($value < $fechaHoy->toDateString()) {
            $fail('La fecha de inicio no puede ser menor que la fecha de hoy.');
        }
    };
}
// Indica que la fecha final no debe ser menor que la fecha de inicio
public function validarRangoFecha($fechaInicio, $fechaFinal)
    {
        return function ($attribute, $value, $fail) use ($fechaInicio, $fechaFinal) {
            if ($fechaFinal && $value < $fechaInicio) {
                $fail('La fecha final no puede ser menor que la fecha de inicio.');
            }
        };
    }


// Método para activar la regla de prohibir múltiples espacios
public function prohibirMultiplesEspacios()
{
    $this->prohibirMultiplesEspacios = true;
    return $this;
}

    //Validar los de existencia de reporte general
    public function validarExistenciaDeReportes()
    {
        $this->validarExistenciaDeReportes = true;
        return $this;
    }

    public function validarExistenciaReportes()
    {
        $this->validarExistenciaDeReportes = true;
        return $this;
    }

    public function prohibirCeroYNegativos()
    {
        $this->prohibirCeroYNegativos = true;
        return $this;
    }

    public function requerirFechaIngresoValida()
    {
        $this->requerirFechaIngresoValida = true;
        return $this;
    }

    public function prohibirSimbolosSalvoDecimal()
    {
        $this->prohibirSimbolosSalvoDecimal = true;
        return $this;
    }

    public function requerirFechaIngresoMinima($fechaMinima)
    {
        $this->requerirFechaIngresoMinima = true;
        $this->fechaMinima = $fechaMinima;
        return $this;
    }

    //valida que exista una fecha en el registro
    public function validarFechaConRegistros()
    {
        $this->validarFechaConRegistros = true;
        return $this;
    }

     //Valida el registro generado de hoy en gastos
     public function validarRegistrosHoy()
     {
         $this->validarRegistrosHoy = true;
         return $this;
     }

    //valida el año del registro
    public function validarAnoConRegistros()
    {
        $this->validarAnoConRegistros = true;
        return $this;
    }
    //valida el mes del registro
    public function validarMesConRegistros($mes, $anio)
    {
        $this->validarMesConRegistros = true;
        $this->mes = $mes;
        $this->anio = $anio;
        return $this;
    }

    // Nueva regla para validar el estado del proyecto
    public function requerirCorreoUnico($tabla, $campo)
    {
        $this->prohibirCorreoUnico = true;
        $this->tablaUnica = $tabla;
        $this->campoUnico = $campo;
        return $this;
    }

    //validacion para el proyecto en planilla y compras
    public function requerirEstadoValidoProyecto()
    {
        $this->requerirEstadoValidoProyecto = true;
        return $this;
    }

    // indica que la fecha ingresada debe ser menor que la fecha final rela (mantenimiento)
    public function Fechas(Request $request)
    {
        $request->validate([
            'FEC_INGRESO' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $fechaPlanificada = $request->input('FEC_FINAL_PLANIFICADA');
                    $fechaFinal = $request->input('FEC_FINAL_REAL');

                    if ($value >= $fechaPlanificada) {
                        $fail('La fecha ingresada debe ser menor que la fecha planificada.');
                    }

                    if ($value >= $fechaFinal) {
                        $fail('La fecha ingresada debe ser menor que la fecha final.');
                    }
                }
            ],
            'FEC_FINAL_PLANIFICADA' => 'required|date',
            'FEC_FINAL_REAL' => 'required|date',
        ]);
        

        // Continúa con el proceso de ainsertar
    }

    //Validacion para el DNI fin
    public function validarDni()
        {
            $this->validarDni = true;
            return $this;
        }

        // Validación de campo único
        public function validarCampoUnico($tabla, $campo, $id = null)
        {
            $this->tabla = $tabla;
            $this->campo = $campo;
            $this->validarCampoUnico = true;
            $this->id = $id;
            return $this;
        }

    public function validarRangoFechas($fechaInicial, $fechaFinal)
    {
        $this->fechaInicial = $fechaInicial;
        $this->fechaFinal = $fechaFinal;
        return $this;
    }

    // Indica que se requiere que el proyecto no haya alcanzado su fecha final
    public function requerirFechaValidaProyecto()
    {
          $this->requerirFechaValidaProyecto = true;
        return $this;
    }

    // Indica que requiere un solo espacio.
    public function requerirUnEspacio()
    {
        $this->requerirUnEspacio = true;
        return $this;
    }

    // Indica que se requiere al menos una letra mayúscula.
    public function requerirMayuscula()
    {
        $this->requerirMayuscula = true;
        return $this;
    }

    // Indica que se requiere al menos una letra minúscula.
    public function requerirMinuscula()
    {
        $this->requerirMinuscula = true;
        return $this;
    }

    // Indica que se requiere al menos un dígito numérico.
    public function requerirNumero()
    {
        $this->requerirNumero = true;
        return $this;
    }

    // Indica que se requiere al menos un carácter especial.
    public function requerirSimbolo()
    {
        $this->requerirSimbolo = true;
        return $this;
    }

    // Indica que no se permiten espacios.
    public function requerirSinEspacios()
    {
        $this->requerirSinEspacios = true;
        return $this;
    }

    // Indica que se requiere al menos un símbolo "@" (para la validación de correo electrónico).
    public function requerirArroba()
    {
        $this->requerirArroba = true;
        return $this;
    }

    // Indica que se requiere que todos los caracteres sean mayúsculas.
    public function requerirTodoMayusculas()
    {
        $this->requerirTodoMayusculas = true;
        return $this;
    }

    // Indica que se requiere una longitud mínima.
    public function requerirLongitudMinima($longitud)
    {
        $this->longitudMinima = $longitud;
        return $this;
    }

    // Indica que el campo es requerido.
    public function requerirCampo()
    {
        $this->campoRequerido = true;
        return $this;
    }

    // Indica que se requiere una longitud máxima.
    public function requerirLongitudMaxima($longitud)
    {
        $this->longitudMaxima = $longitud;
        return $this;
    }

    // Indica que se prohíben números y símbolos.
    public function prohibirNumerosSimbolos()
    {
        $this->prohibirNumerosSimbolos = true;
        return $this;
    }

    // Indica que se requiere solo números.
    public function requerirSoloNumeros()
    {
        $this->requerirSoloNumeros = true;
        return $this;
    }

    //Indica que se requiere que el valor sea mayor o igual a un mínimo.
    public function requerirValorMinimo($minimo)
    {
        $this->requerirValorMinimo = true;
        $this->valorMinimo = $minimo;
        return $this;
    }

    // Determina si la regla de validación se cumple.
    public function passes($attribute, $value)
    {
        // Validación de un solo espacio
        if ($this->requerirUnEspacio) {
            if (substr_count($value, ' ') != 1) {
                $this->mensaje = 'El :attribute debe contener primer nombre y primer apellido, y no debe contener dobles espacios.';
                return false;
            }
        }

        // Validación de campo requerido
        if ($this->campoRequerido && (is_null($value) || $value === '')) {
            $this->mensaje = 'El campo :attribute es obligatorio.';
            return false;
        }

        // Validación de longitud mínima
        if ($this->longitudMinima > 0 && strlen($value) < $this->longitudMinima) {
            $this->mensaje = 'El :attribute debe tener al menos ' . $this->longitudMinima . ' caracteres.';
            return false;
        }

        // Validación de longitud máxima
        if ($this->longitudMaxima > 0 && strlen($value) > $this->longitudMaxima) {
            $this->mensaje = 'El :attribute no debe tener más de ' . $this->longitudMaxima . ' caracteres.';
            return false;
        }

        // Validación de prohibición de números y símbolos
        if ($this->prohibirNumerosSimbolos && (preg_match('/[\d!@#\$%\^\&*\)\(+=._-]/', $value))) {
            $this->mensaje = 'El :attribute no puede contener números ni símbolos.';
            return false;
        }

        // Validación de solo números
        if ($this->requerirSoloNumeros && !preg_match('/^\d+$/', $value)) {
            $this->mensaje = 'El :attribute solo debe contener números.';
            return false;
        }

        // Validación de que el valor sea numérico y mayor o igual a cero
        if ($this->requerirValorMinimo && (!is_numeric($value) || $value < $this->valorMinimo)) {
            $this->mensaje = 'El :attribute debe ser un número mayor o igual a ' . $this->valorMinimo . '.';
            return false;
        }

        if ($this->requerirMayuscula && !preg_match('/[A-Z]/', $value)) {
            $this->mensaje = 'El :attribute debe contener al menos una letra mayúscula.';
            return false;
        }

        if ($this->requerirMinuscula && !preg_match('/[a-z]/', $value)) {
            $this->mensaje = 'El :attribute debe contener al menos una letra minúscula.';
            return false;
        }

        if ($this->requerirSimbolo && !preg_match('/[\W_]/', $value)) {
            $this->mensaje = 'El :attribute debe contener al menos un símbolo.';
            return false;
        }

        if ($this->requerirSinEspacios && preg_match('/\s/', $value)) {
            $this->mensaje = 'El :attribute no debe contener espacios.';
            return false;
        }

        if ($this->requerirArroba && !strpos($value, '@')) {
            $this->mensaje = 'El :attribute debe contener un símbolo "@" válido.';
            return false;
        }

        if ($this->requerirTodoMayusculas && preg_match('/[a-z]/', $value)) {
            $this->mensaje = 'El :attribute debe estar completamente en mayúsculas.';
            return false;
        }

        // Validación para prohibir espacios al inicio o al final
        if (isset($this->prohibirEspaciosInicioFin) && $this->prohibirEspaciosInicioFin) {
            $espaciosInicioFin = $this->prohibirEspaciosInicioFin();
            $espaciosInicioFin($attribute, $value, function($message) {
                $this->mensaje = $message;
                return false;
            });
        }


        // Validación de correo único
        if ($this->prohibirCorreoUnico) {
        $usuario = DB::table('tbl_ms_usuario')->where('Correo_Electronico', $value)->first();
        if ($usuario) {
            $this->mensaje = 'El correo está repetido.';
            return false;
            }
        }

        // Validación de fecha de proyecto
        if ($this->requerirFechaValidaProyecto) {
            $proyecto = DB::table('tlb_proyectos')->where('COD_PROYECTO', $value)->first();
            if ($proyecto && Carbon::parse($proyecto->FEC_FINAL)->isPast()) {
                $this->mensaje = 'El proyecto seleccionado ha llegado a su fecha final.';
                return false;
            }
        }

        // Validación de estado del proyecto
        if ($this->requerirEstadoValidoProyecto) {
            $proyecto = DB::table('tbl_proyectos')->where('COD_PROYECTO', $value)->first();
            if ($proyecto && in_array($proyecto->ESTADO_PROYECTO, ['SUSPENDIDO', 'FINALIZADO'])) {
                $this->mensaje = 'El proyecto seleccionado está suspendido o finalizado.';
                return false;
            }
        }

        // Validación para prohibir 0 y números negativos
    if ($this->prohibirCeroYNegativos) {
        if (!is_numeric($value) || $value <= 0) {
            $this->mensaje = 'El :attribute no puede ser 0 ni un número negativo.';
            return false;
        }
    }

        //Fecha 
        if (isset($this->fechaInicial) && isset($this->fechaFinal)) {
            if (strtotime($this->fechaFinal) < strtotime($this->fechaInicial)) {
                $this->mensaje = 'La fecha final no puede ser menor que la fecha inicial.';
                return false;
            }
        }

        // Validación para prohibir símbolos salvo el punto decimal
        if ($this->prohibirSimbolosSalvoDecimal) {
            if (!preg_match('/^\d+(\.\d+)?$/', $value)) {
                $this->mensaje = 'El :attribute solo puede contener números y un punto decimal.';
                return false;
            }
        }


        // Validación de DNI
        if ($this->validarDni) {
            // Check if it contains only numbers and is exactly 13 digits long
            if (!preg_match('/^\d{13}$/', $value)) {
                $this->mensaje = 'El DNI debe tener exactamente 13 dígitos y solo contener números.';
                return false;
            }

            // Extract the year from the DNI
            $year = intval(substr($value, 4, 4));

            // Check if the year is between 1930 and the current year
            $currentYear = intval(date('Y'));
            if ($year < 1930 || $year > $currentYear) {
                $this->mensaje = 'El DNI contiene un año no válido. Debe ser entre 1930 y ' . $currentYear . '.';
                return false;
            }

            // Check for repeated sequences of 4 or more digits
            if (preg_match('/(\d)\1{3,}/', $value)) {
                $this->mensaje = 'El DNI no debe contener secuencias repetidas de 4 o más dígitos.';
                return false;
            }
        }

        // Validación de campo único
        if ($this->validarCampoUnico) {
            $query = DB::table($this->tabla)->where($this->campo, $value);
        
            // Si se proporciona un ID, excluye ese registro de la verificación de unicidad
            if (isset($this->id)) {
                $query->where('COD_EMPLEADO', '!=', $this->id);
            }
        
            $exists = $query->exists();
        
            if ($exists) {
                $this->mensaje = 'El :attribute ya está en uso.';
                return false;
            }
        }
        


       

        // Validación de fecha de ingreso mínima
        if ($this->requerirFechaIngresoMinima) {
            if (Carbon::parse($value)->lt(Carbon::parse($this->fechaMinima))) {
                $this->mensaje = 'La :attribute no puede ser menor a ' . Carbon::parse($this->fechaMinima)->format('d/m/Y') . '.';
                return false;
            }
        }


        // Validación de existencia de registros para una fecha gastos
        if ($this->validarFechaConRegistros) {
            $existeFecha = DB::table('tbl_gastos')->whereDate('FEC_REGISTRO', $value)->exists();
            if (!$existeFecha) {
                $this->mensaje = 'No existen registros generados para la fecha proporcionada.';
                return false;
            }
        }

        if ($this->validarAnoConRegistros) {
            $existeAno = DB::table('tbl_gastos')
            ->whereYear('FEC_REGISTRO', $this->anio)
            ->exists();
            if (!$existeAno) {
                $this->mensaje = 'No existen registros generados para el año proporcionado.';
                return false;
            }
        }
        
        // Validación de fecha de ingreso válida
        if ($this->requerirFechaIngresoValida) {
            if (Carbon::parse($value)->isFuture()) {
                $this->mensaje = 'La :attribute no puede ser mayor al día de hoy.';
                return false;
            }
        }

          // Validación de existencia de registros para un mes y año
          if ($this->validarMesConRegistros) {
            $existeMes = DB::table('tbl_gastos')
            ->whereYear('FEC_REGISTRO', $this->anio)
            ->whereMonth('FEC_REGISTRO', $this->mes)
            ->exists();
            if (!$existeMes) {
            $this->mensaje = 'No existen registros generados para el mes o año proporcionado.';
            return false;
        }
        }

        // Validación de registros para hoy tbl_gastos
        if ($this->validarRegistrosHoy) {
            $hoy = Carbon::today();
            $existenRegistrosHoy = DB::table('tbl_gastos')->whereDate('FEC_REGISTRO', $hoy)->exists();
            if (!$existenRegistrosHoy) {
                $this->mensaje = 'No existen registros generados para el día de hoy.';
                return false;
            }
        }

        // Validación de existencia de reportes
        if ($this->validarExistenciaDeReportes) {
            $existenReportes = DB::table('tbl_gastos')
            ->exists();
            if (!$existenReportes) {
                $this->mensaje = 'No existen registros de generados por los momentos.';
                return false;
            }
        }                


  // Validación de múltiples espacios
  if ($this->prohibirMultiplesEspacios) {
    if (substr_count($value, ' ') > 1) {
        $this->mensaje = 'El :attribute no debe contener más de un espacio.';
        return false;
    }
}

        return true;
    }
    

    // Obtiene el mensaje de error de validación.
    public function message()
    {
        return $this->mensaje;
    }

}


