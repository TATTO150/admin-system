<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\Rules\Validaciones;
use Illuminate\Support\Facades\Http;

class EstadoUsuarioRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Autoriza la validación
    }

    public function rules()
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $currentEstado = $this->route('estado') ? $this->route('estado')->ESTADO : null;

        return [
            'ESTADO' => [
                'required',
                'string',
                'max:50',
                (new Validaciones)->requerirCampo()->requerirTodoMayusculas(),
                function ($attribute, $value, $fail) use ($isUpdate, $currentEstado) {
                    if (preg_match('/^\d/', $value)) {
                        $fail('El campo "ESTADO" no puede comenzar con un número. Has ingresado: ' . $value);
                    }
                    if (preg_match('/^\W/', $value)) {
                        $fail('El campo "ESTADO" no puede comenzar con un símbolo especial. Has ingresado: ' . $value);
                    }
                    if (preg_match('/([A-Z])\1{2,}/', $value)) {
                        $fail('El campo "ESTADO" no puede tener más de dos letras seguidas. Has ingresado: ' . $value);
                    }
                    if (preg_match('/[\W_]{2,}/', $value)) {
                        $fail('El campo "ESTADO" no puede tener símbolos especiales consecutivos. Has ingresado: ' . $value);
                    }
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('El campo "ESTADO" no puede tener más de un espacio seguido. Has ingresado: ' . $value);
                    }
                    if (preg_match('/\b(I|V|X|L|C|D|M)\b/', $value)) {
                        $fail('El campo "ESTADO" no puede contener números romanos. Has ingresado: ' . $value);
                    }
                    if (!preg_match('/[AEIOU]/', $value) || !preg_match('/[BCDFGHJKLMNPQRSTVWXYZ]/', $value)) {
                        $fail('El campo "ESTADO" debe contener al menos una vocal y una consonante. Has ingresado: ' . $value);
                    }
                    if (preg_match('/[0-9]$/', $value)) {
                        $fail('El campo "ESTADO" no puede terminar con un número. Has ingresado: ' . $value);
                    }

                    // Si no es una actualización o el nombre ha cambiado
                    if (!$isUpdate || strtolower($currentEstado) !== strtolower($value)) {
                        $estadosExistentes = collect($this->fetchApiData('Estados'));
                        if ($estadosExistentes->first(fn($estado) => strtolower($estado['ESTADO']) == strtolower($value))) {
                            $fail('El campo "ESTADO" ya existe en la base de datos. Has ingresado: ' . $value);
                        }
                    }
                },
            ],
            'DESCRIPCION' => [
                'required',
                'string',
                (new Validaciones)->requerirCampo()->requerirTodoMayusculas(),
                function ($attribute, $value, $fail) use ($isUpdate, $currentEstado) {
                    if (preg_match('/^\d/', $value)) {
                        $fail('El campo "DESCRPCION" no puede comenzar con un número. Has ingresado: ' . $value);
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
                 
                },
                
            ],
        ];
    }

    public function messages()
    {
        return [
            'ESTADO.required' => 'El campo "ESTADO" es obligatorio.',
            'DESCRIPCION.required' => 'El campo "DESCRIPCION" es obligatorio.',
        ];
    }

    protected function fetchApiData($endpoint)
    {
        // Aquí deberías implementar la lógica para obtener los datos de la API
        $response = Http::get("http://127.0.0.1:3000/{$endpoint}");
        return $response->json();
    }
}
