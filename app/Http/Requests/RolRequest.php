<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\Rules\Validaciones;
use Illuminate\Support\Facades\Http;

class RolRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Autoriza la validación
    }

    public function rules()
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $currentRol = $this->route('rol') ? $this->route('rol')->Rol : null;
        $rolId = $this->route('Id_Rol'); 

        return [
            'Rol' => [
                (new Validaciones)->requerirCampo()->requerirTodoMayusculas(),
                function ($attribute, $value, $fail) use ($isUpdate, $currentRol) {
                    if (preg_match('/^\d/', $value)) {
                        $fail('El campo "Rol" no puede comenzar con un número. Has ingresado: ' . $value);
                    }
                    if (preg_match('/^\W/', $value)) {
                        $fail('El campo "Rol" no puede comenzar con un símbolo especial. Has ingresado: ' . $value);
                    }
                    if (preg_match('/([A-Z])\1{2,}/', $value)) {
                        $fail('El campo "Rol" no puede tener más de dos letras seguidas. Has ingresado: ' . $value);
                    }
                    if (preg_match('/[\W_]{2,}/', $value)) {
                        $fail('El campo "Rol" no puede tener símbolos especiales consecutivos. Has ingresado: ' . $value);
                    }
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('El campo "Rol" no puede tener más de un espacio seguido. Has ingresado: ' . $value);
                    }
                    if (preg_match('/\b(I|V|X|L|C|D|M)\b/', $value)) {
                        $fail('El campo "Rol" no puede contener números romanos. Has ingresado: ' . $value);
                    }
                    if (!preg_match('/[AEIOU]/', $value) || !preg_match('/[BCDFGHJKLMNPQRSTVWXYZ]/', $value)) {
                        $fail('El campo "Rol" debe contener al menos una vocal y una consonante. Has ingresado: ' . $value);
                    }
                    if (preg_match('/[0-9]$/', $value)) {
                        $fail('El campo "Rol" no puede terminar con un número. Has ingresado: ' . $value);
                    }

                },
            ],
            'Descripcion' => [
                (new Validaciones)->requerirCampo()->requerirTodoMayusculas(),
                function ($attribute, $value, $fail) use ($isUpdate, $currentRol) {
                    if (preg_match('/^\d/', $value)) {
                        $fail('El campo "DESCRIPCION" no puede comenzar con un número. Has ingresado: ' . $value);
                    }
                    if (preg_match('/^\W/', $value)) {
                        $fail('El campo "DESCRIPCION" no puede comenzar con un símbolo especial. Has ingresado: ' . $value);
                    }
                    if (preg_match('/([A-Z])\1{2,}/', $value)) {
                        $fail('El campo "DESCRIPCION" no puede tener más de dos letras seguidas. Has ingresado: ' . $value);
                    }
                    if (preg_match('/[\W_]{2,}/', $value)) {
                        $fail('El campo "DESCRIPCION" no puede tener símbolos especiales consecutivos. Has ingresado: ' . $value);
                    }
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('El campo "DESCRIPCION" no puede tener más de un espacio seguido. Has ingresado: ' . $value);
                    }
                    if (preg_match('/\b(I|V|X|L|C|D|M)\b/', $value)) {
                        $fail('El campo "DESCRIPCION" no puede contener números romanos. Has ingresado: ' . $value);
                    }
                    if (!preg_match('/[AEIOU]/', $value) || !preg_match('/[BCDFGHJKLMNPQRSTVWXYZ]/', $value)) {
                        $fail('El campo "DESCRIPCION" debe contener al menos una vocal y una consonante. Has ingresado: ' . $value);
                    }

                    // Si es una actualización y el nombre de Rol no ha cambiado, omite la validación de unicidad
                    if ($isUpdate && strtolower($currentRol) === strtolower($value)) {
                        return;
                    }
                  
              
                    }

            ],
        ];
    }

    public function messages()
    {
        return [
            'Rol.required' => 'El campo "Rol" es obligatorio.',
            'Descripcion.required' => 'El campo "Descripción" es obligatorio.',
        ];
    }

    protected function fetchApiData($endpoint)
    {
        // Aquí deberías implementar la lógica para obtener los datos de la API
        $response = Http::get("http://127.0.0.1:3000/{$endpoint}");
        return $response->json();
    }
}
