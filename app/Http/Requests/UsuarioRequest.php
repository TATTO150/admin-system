<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Validaciones;
use Illuminate\Support\Facades\Http;

class UsuarioRequest extends FormRequest
{public function authorize()
    {
        return true; // Autoriza la validación
    }

    public function rules()
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $currentUsuario = $this->route('usuario') ? $this->route('usuario')->Usuario : null;
        $userId = $this->route('Id_usuario'); 

        return [
            'Usuario' => [
                (new Validaciones)->requerirSinEspacios()->requerirTodoMayusculas()->requerirlongitudMinima(4)->requerirlongitudMaxima(15)->prohibirNumerosSimbolos(),
                function($attribute, $value, $fail) use ($isUpdate, $currentUsuario) {
                    if (preg_match('/^\d/', $value)) {
                        $fail('El campo "Usuario" no puede comenzar con un número. Has ingresado: ' . $value);
                    }
                    if (preg_match('/^\W/', $value)) {
                        $fail('El campo "Usuario" no puede comenzar con un símbolo especial. Has ingresado: ' . $value);
                    }
                    if (preg_match('/([A-Z])\1{2,}/', $value)) {
                        $fail('El campo "Usuario" no puede tener más de dos letras seguidas. Has ingresado: ' . $value);
                    }
                      // Prohibir caracteres especiales consecutivos
                    if (preg_match('/[\W_]{1,}/', $value)) {
                    $fail('El campo "Usuario" no puede contener caracteres especiales. Has ingresado: ' . $value);
                     }

                    if (preg_match('/[\W_]{2,}/', $value)) {
                        $fail('El campo "Usuario" no puede tener símbolos especiales consecutivos. Has ingresado: ' . $value);
                    }
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('El campo "Usuario" no puede tener más de un espacio seguido. Has ingresado: ' . $value);
                    }
                    if (preg_match('/\b(I|V|X|L|C|D|M)\b/', $value)) {
                        $fail('El campo "Usuario" no puede contener números romanos. Has ingresado: ' . $value);
                    }
                    if (!preg_match('/[AEIOU]/', $value) || !preg_match('/[BCDFGHJKLMNPQRSTVWXYZ]/', $value)) {
                        $fail('El campo "Usuario" debe contener al menos una vocal y una consonante. Has ingresado: ' . $value);
                    }

                    if (!$isUpdate) {
                        $usuariosExistentes = collect($this->fetchApiData('Usuarios'));
                        if ($usuariosExistentes->first(fn($usuario) => strtolower($usuario['Usuario']) == strtolower($value))) {
                            $fail('El campo "Usuario" ya existe en la base de datos. Has ingresado: ' . $value);
                        }
                    }
                },
            ],
            'Nombre_Usuario' => [
                (new Validaciones)->requerirUnEspacio()->requerirTodoMayusculas()->requerirlongitudMinima(5)->requerirlongitudMaxima(15),
                function($attribute, $value, $fail) {
                    if (preg_match('/\b(\w+)\s+\1\b/', $value)) {
                        $fail('El campo "Nombre de Usuario" no puede contener la misma palabra dos veces consecutivas. Has ingresado: ' . $value);
                    }
                    if (strlen($value) < 5 || strlen($value) > 15) {
                        $fail('El campo "Nombre de Usuario" debe tener entre 5 y 15 caracteres. Has ingresado: ' . $value);
                    }
            
                    // Prohibir caracteres especiales y números
                    if (preg_match('/[^A-Z\s]/', $value)) {
                        $fail('El campo "Nombre de Usuario" solo puede contener letras mayúsculas y espacios. Has ingresado: ' . $value);
                    }
                },
                
                // Aquí podrías agregar validaciones similares si es necesario
            ],
            'Contrasena' => [
                (new Validaciones)->requerirSinEspacios()->requerirSimbolo()->requerirMinuscula()->requerirMayuscula()->requerirNumero()->requerirlongitudMinima(8)->requerirlongitudMaxima(12)->requerirCampo(),
                // Otras validaciones personalizadas para la contraseña pueden agregarse aquí
            ],
           'Correo_Electronico' => [
    (new Validaciones)
        ->requerirSinEspacios() // Prohíbe espacios
        ->requerirArroba() // Requiere que tenga un '@'
        ->requerirCampo(), // Asegura que el campo esté presente
    
    // Validación de unicidad
    'unique:tbl_ms_usuario,Correo_Electronico' . ($isUpdate ? ',' . $userId . ',Id_usuario' : ''),
    
    // Validar que tenga un punto después de la arroba
    'regex:/^[^@]+@[^@]+\.[^@]+$/', 


    // Validar tamaño mínimo y máximo
    function ($attribute, $value, $fail) {
        // Validar tamaño mínimo de 5 y máximo de 50 caracteres
        if (strlen($value) < 5 || strlen($value) > 30) {
            $fail('El campo "Correo Electrónico" debe tener entre 5 y 30 caracteres. Has ingresado: ' . $value);
        }

        // Validar que el correo tenga un punto después de la arroba
        if (!preg_match('/^[^@]+@[^@]+\.[^@]+$/', $value)) {
            $fail('El campo "Correo Electrónico" debe contener un punto (.) después del símbolo @. Has ingresado: ' . $value);
        }

    }
],
            'Fecha_Ultima_Conexion' => [(new Validaciones)->requerirCampo()],
            'Primer_Ingreso' => [(new Validaciones)->requerirCampo()],
            'Fecha_Vencimiento' => [(new Validaciones)->requerirCampo()->validarFechaNoMenorVencimientoActual($userId)],
        ]; [
                'Correo_Electronico.regex' => 'El correo electrónico debe contener un punto (.) después del arroba (@).',
                'Correo_Electronico.not_regex' => 'No se permiten correos electrónicos institucionales como @empresa.com, @institucion.edu, @otro.org.',
                'Correo_Electronico.unique' => 'El correo electrónico ya está registrado en el sistema.',
                'regex' => 'El formato del campo :attribute es inválido.', // Sobrescribir el mensaje general para cualquier regex
        ];



    }

    public function messages()
    {
        return [
            'Usuario.required' => 'El campo "Usuario" es obligatorio.',
            'Nombre_Usuario.required' => 'El campo "Nombre de Usuario" es obligatorio.',
            'Contrasena.required' => 'El campo "Contraseña" es obligatorio.',
            'Correo_Electronico.required' => 'El campo "Correo Electrónico" es obligatorio.',
            'Correo_Electronico.unique' => 'El correo electrónico ya ha sido registrado.',
            'Fecha_Ultima_Conexion.required' => 'El campo "Fecha Última Conexión" es obligatorio.',
            'Primer_Ingreso.required' => 'El campo "Primer Ingreso" es obligatorio.',
            'Fecha_Vencimiento.required' => 'El campo "Fecha de Vencimiento" es obligatorio.',
        ];
    }
    protected function fetchApiData($endpoint)
    {
        // Aquí deberías implementar la lógica para obtener los datos de la API
        // Esto puede ser una llamada HTTP o acceso a un repositorio de datos local
        // Retorna un array o colección de los datos obtenidos

        $response = Http::get("http://127.0.0.1:3000/{$endpoint}");
        return $response->json();
    }

}
