<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoAsignacion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Permisos;
use Barryvdh\DomPDF\Facade\Pdf;

class TipoAsignacionController extends Controller
{
    // Mostrar todos los tipos de asignación
    public function index()
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de inserción en el objeto TIPOASIGNACION
        $permisoInsertar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'TIPOASIGNACION')
                    ->limit(1);
            })
            ->where('Permiso_Consultar', 'PERMITIDO')
            ->exists();

        if (!$permisoInsertar) {
            return redirect()->route('dashboard')->withErrors('No tiene permiso para entrar la ventana tipo de asignación.');
        }
        $tiposAsignacion = TipoAsignacion::all();
        return view('tiposasignacion.index', compact('tiposAsignacion'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de inserción en el objeto TIPOASIGNACION
        $permisoInsertar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'TIPOASIGNACION')
                    ->limit(1);
            })
            ->where('Permiso_Insercion', 'PERMITIDO')
            ->exists();

        if (!$permisoInsertar) {
            return redirect()->route('tiposasignacion.index')->withErrors('No tiene permiso para crear un tipo de asignación.');
        }

        return view('tiposasignacion.create');
    }

    // Guardar un nuevo tipo de asignación
    public function store(Request $request)
    {
        
        $validator = $this->validateAsignacion($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Crear nuevo tipo de asignación
        TipoAsignacion::create([
            'TIPO_ASIGNACION' => strtoupper($request->TIPO_ASIGNACION), // Convertir a mayúsculas antes de guardar
        ]);

        // Redirigir con mensaje de éxito
        return redirect()->route('tiposasignacion.index')->with('success', 'Tipo de asignación creado exitosamente.');
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de inserción en el objeto TIPOASIGNACION
        $permisoInsertar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'TIPOASIGNACION')
                    ->limit(1);
            })
            ->where('Permiso_Actualizacion', 'PERMITIDO')
            ->exists();

        if (!$permisoInsertar) {
            return redirect()->route('tiposasignacion.index')->withErrors('No tiene permiso para editar este tipo de asignación.');
        }

        $tipoAsignacion = TipoAsignacion::findOrFail($id);
        return view('tiposasignacion.edit', compact('tipoAsignacion'));
    }

    // Actualizar un tipo de asignación existente
    public function update(Request $request, $id)
    {
       

        $validator = $this->validateAsignacion($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Encontrar el tipo de asignación y actualizarlo
        $tipoAsignacion = TipoAsignacion::findOrFail($id);
        $tipoAsignacion->update([
            'TIPO_ASIGNACION' => strtoupper($request->TIPO_ASIGNACION), // Convertir a mayúsculas antes de actualizar
        ]);

        // Redirigir con mensaje de éxito
        return redirect()->route('tiposasignacion.index')->with('success', 'Tipo de asignación actualizado exitosamente.');
    }

    // Función de validación personalizada
    private function validateAsignacion(Request $request)
    {
        return Validator::make($request->all(), [
            'TIPO_ASIGNACION' => [
                'required',
                'string',
                'max:20',
                function($attribute, $value, $fail) {
                    // No permitir más de 2 veces la misma letra consecutivamente
                    if (preg_match('/([a-zA-Z])\1{2,}/', $value)) {
                        $fail('El campo "Tipo de Asignación" no puede tener más de dos letras iguales consecutivas. Has ingresado: ' . $value);
                    }
                    // No permitir más de un espacio seguido
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('El campo "Tipo de Asignación" no puede tener más de un espacio seguido. Has ingresado: ' . $value);
                    }
                    // Verificar que no haya más de 4 consonantes seguidas
                    if (preg_match('/[BCDFGHJKLMNPQRSTVWXYZ]{5}/i', $value)) {
                        $fail('El campo "Tipo de Asignación" no puede tener más de cuatro consonantes seguidas en una palabra. Has ingresado: ' . $value);
                    }
                    // No permitir caracteres especiales al inicio
                    if (preg_match('/^[^a-zA-Z]/', $value)) {
                        $fail('El campo "Tipo de Asignación" no puede iniciar con un carácter especial o un número. Has ingresado: ' . $value);
                    }
                    // No permitir iniciar con un número
                    if (preg_match('/^\d/', $value)) {
                        $fail('El campo "Tipo de Asignación" no puede iniciar con un número. Has ingresado: ' . $value);
                    }
                    // No permitir terminar con un carácter especial
                    if (preg_match('/[^a-zA-Z0-9]$/', $value)) {
                        $fail('El campo "Tipo de Asignación" no puede terminar con un carácter especial. Has ingresado: ' . $value);
                    }
                    // Debe tener un mínimo de 4 letras
                    if (strlen(preg_replace('/[^a-zA-Z]/', '', $value)) < 4) {
                        $fail('El campo "Tipo de Asignación" debe contener al menos 4 letras. Has ingresado: ' . $value);
                    }
                    // No permitir más de 3 vocales seguidas
                    if (preg_match('/[AEIOUaeiou]{4}/', $value)) {
                        $fail('El campo "Tipo de Asignación" no puede tener más de tres vocales seguidas. Has ingresado: ' . $value);
                    }
                    // No permitir minúsculas
                    if (preg_match('/[a-z]/', $value)) {
                        $fail('El campo "Tipo de Asignación" solo puede contener letras mayúsculas. Has ingresado: ' . $value);
                    }
                },
            ],
        ], [
            'TIPO_ASIGNACION.required' => 'El campo "Tipo de Asignación" es obligatorio.',
            'TIPO_ASIGNACION.string' => 'El campo "Tipo de Asignación" debe ser una cadena de texto.',
            'TIPO_ASIGNACION.max' => 'El campo "Tipo de Asignación" no debe exceder los 20 caracteres.',
        ]);
    }
    public function generateReport()
    {
        $tipoAsignaciones = TipoAsignacion::all();
        $fechaHora = Carbon::now()->format('d-m-Y H:i:s');
        $path = public_path('images/CTraterra.jpeg');
    $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));

        $pdf = Pdf::loadView('tiposasignacion.tipo_asignaciones', compact('tipoAsignaciones', 'logoBase64', 'fechaHora'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ]);

        return $pdf->stream('reporte_tipo_asignaciones.pdf');
    }
}
