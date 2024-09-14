<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Models\EstadoAsignacion;
use Illuminate\Support\Facades\DB;
USE  App\Models\Asignacion_Equipos;
use App\Models\Permisos;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;



class EstadoAsignacionController extends Controller
{
   // Mostrar la lista de registros
   public function index()
   {
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de consulta en el objeto ESTADO
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
    //    $this->bitacora->registrarEnBitacora(22, 'Intento de ingreso a la ventana de estados sin permisos', 'Ingreso');
        return redirect()->route('dashboard')->withErrors('No tiene permiso para ingresar a la ventana de asignacion equipo');
    }


    $estados = EstadoAsignacion::all();
       return view('estado_asignacion.index', compact('estados'));
   }




   // Mostrar el formulario para crear un nuevo registro
   public function create()
   {
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de inserción en el objeto ESTADO
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
        return redirect()->route('estado_asignacion.index')->withErrors('No tiene permiso para añadir estados de asignacion');
    }

       return view('estado_asignacion.create');
   }

   // Guardar un nuevo registro en la base de datos
   public function store(Request $request)
   {
       $request->validate([
           'ESTADO' =>  [
            'required',
            'string',
            'max:255',
            function ($attribute, $value, $fail) {
                // Verificar que el campo esté en mayúsculas
                if ($value !== strtoupper($value)) {
                    $fail('El campo ' . $attribute . ' debe estar en mayúsculas.');
                }

                // Verificar que el campo solo contenga letras en mayúsculas y espacios
                if (!preg_match('/^[A-Z\s]+$/', $value)) {
                    $fail('El campo ' . $attribute . ' solo puede contener letras en mayúsculas y espacios, sin números ni símbolos.');
                }

                // Verificar que no haya secuencias de más de 3 caracteres repetidos
                if (preg_match('/(.)\1{3,}/', $value)) {
                    $fail('El campo ' . $attribute . ' no puede contener secuencias de más de 3 caracteres repetidos consecutivos.');
                }

                // Verificar que la cadena no sea excesivamente larga sin espacios ni que no parezca un texto significativo
                if (strlen($value) > 15 && !preg_match('/\s/', $value)) {
                    $fail('El campo ' . $attribute . ' no puede ser una cadena larga sin espacios o una secuencia de caracteres aparentemente aleatoria.');
                }
            },
        ],
       ]);

       EstadoAsignacion::create($request->all());

       return redirect()->route('estado_asignacion.index')
           ->with('success', 'Estado de asignación creado exitosamente.');
   }

   // Mostrar el formulario para editar un registro existente
   public function edit($id)
   {
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de inserción en el objeto ESTADO
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
        return redirect()->route('estado_asignacion.index')->withErrors('No tiene permiso para actualizar estados de asignacion');
    }

       $estado = EstadoAsignacion::findOrFail($id);
       return view('estado_asignacion.edit', compact('estado'));
   }

   // Actualizar un registro existente en la base de datos
   public function update(Request $request, $id)
   {
       $request->validate([
           'ESTADO' => [
            'required',
            'string',
            'max:255',
            function ($attribute, $value, $fail) {
                // Verificar que el campo esté en mayúsculas
                if ($value !== strtoupper($value)) {
                    $fail('El campo ' . $attribute . ' debe estar en mayúsculas.');
                }

                // Verificar que el campo solo contenga letras en mayúsculas y espacios
                if (!preg_match('/^[A-Z\s]+$/', $value)) {
                    $fail('El campo ' . $attribute . ' solo puede contener letras en mayúsculas y espacios, sin números ni símbolos.');
                }

                // Verificar que no haya secuencias de más de 3 caracteres repetidos
                if (preg_match('/(.)\1{3,}/', $value)) {
                    $fail('El campo ' . $attribute . ' no puede contener secuencias de más de 3 caracteres repetidos consecutivos.');
                }

                // Verificar que la cadena no sea excesivamente larga sin espacios ni que no parezca un texto significativo
                if (strlen($value) > 15 && !preg_match('/\s/', $value)) {
                    $fail('El campo ' . $attribute . ' no puede ser una cadena larga sin espacios o una secuencia de caracteres aparentemente aleatoria.');
                }
            },
        ],
       ]);

       $estado = EstadoAsignacion::findOrFail($id);
       $estado->update($request->all());

       return redirect()->route('estado_asignacion.index')
           ->with('success', 'Estado de asignación actualizado exitosamente.');
   }

   // Eliminar un registro
   public function destroy($id)
   {
       $estado = EstadoAsignacion::findOrFail($id);
   

       $user = Auth::user();
       $roleId = $user->Id_Rol;

       // Verificar si el rol del usuario tiene el permiso de eliminación en el objeto ESTADO
       $permisoEliminar = Permisos::where('Id_Rol', $roleId)
           ->where('Id_Objeto', function ($query) {
               $query->select('Id_Objetos')
                   ->from('tbl_objeto')
                   ->where('Objeto', 'ASIGNACION_EQUIPO')
                   ->limit(1);
           })
           ->where('Permiso_Eliminacion', 'PERMITIDO')
           ->exists();

       if (!$permisoEliminar) {
         //  $this->bitacora->registrarEnBitacora(22, 'Intento de eliminar estado sin permisos', 'Ingreso'); 
           return redirect()->route('estado_asignacion.index')->withErrors('No tiene permiso para eliminar estados de asignacion');
       }



       // Definir los estados que no pueden ser eliminados
       $estadosProtegidos = [
           'MANTENIMIENTO FINALIZADO',
           'ASIGNACION ACTIVAS',
           'MANTENIMIENTO ACTIVO',
           'ASIGNACION FINALIZADA'
       ];
   
       // Verificar si el estado actual es uno de los protegidos
       if (in_array($estado->ESTADO, $estadosProtegidos)) {
           return redirect()->route('estado_asignacion.index')
               ->with('error', 'No puedes eliminar este estado porque está protegido.');
       }
   
       // Verificar si el estado fue creado recientemente (ejemplo: en los últimos 24 horas)
       $estadoReciente = $estado->created_at && $estado->created_at->gt(now()->subDay());
   
       if ($estadoReciente) {
           return redirect()->route('estado_asignacion.index')
               ->with('error', 'No puedes eliminar este estado porque fue creado recientemente.');
       }
   
       // Verificar si hay registros relacionados en la tabla 'tbl_equipo_asignacion'
       $relatedRecords = Asignacion_Equipos::where('COD_ESTADO_ASIGNACION', $estado->COD_ESTADO_ASIGNACION)->exists();
   
       if ($relatedRecords) {
           // Si hay registros relacionados, redirigir con un mensaje de error
           return redirect()->route('estado_asignacion.index')
               ->with('error', 'No puedes eliminar este estado porque tiene registros asociados.');
       }
   
       // Si no hay registros relacionados y el estado no es protegido, procedemos a eliminar
       $estado->delete();
   
       return redirect()->route('estado_asignacion.index')
           ->with('success', 'Estado de asignación eliminado exitosamente.');
   }
   public function generateReport()
   {
       // Obtener todos los estados de asignación
       $estados = EstadoAsignacion::all();

       $path = public_path('images/CTraterra.jpeg');
       $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

       // Obtener la fecha y hora actual
       $fechaHora = now()->format('d-m-Y H:i:s');

       // Cargar la vista y generar el PDF
       $pdf = Pdf::loadView('estado_asignacion.estado_asignacion', compact('estados', 'logoBase64', 'fechaHora'))
       ->setOptions([
        'isHtml5ParserEnabled' => true,
        'isPhpEnabled' => true,
        'defaultFont' => 'Arial',
        'isRemoteEnabled' => true,
    ]);

       // Devolver el PDF generado al navegador
       return $pdf->stream('reporte_estado_asignaciones.pdf');
   }
   
}
