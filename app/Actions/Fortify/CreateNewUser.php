<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Carbon\Carbon;
use App\Rules\ReglaUsuarioContraseña;
use Laravel\Fortify\Rules\Validaciones;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
            Validator::make($input, [
                'Nombre_Usuario' => [(new Validaciones)->requerirTodoMayusculas()->requerirUnEspacio()->prohibirNumerosSimbolos()->requerirCampo()],
                'Usuario' => [(new Validaciones)->requerirSinEspacios()->requerirTodoMayusculas()],
                'Contrasena' => [(new Validaciones)->requerirSinEspacios()->requerirSimbolo()->requerirMinuscula()->requerirMayuscula()->requerirNumero()->requerirlongitudMinima(8)->requerirlongitudMaxima(12)->requerirCampo()],
                'Correo_Electronico' => [(new Validaciones)->requerirSinEspacios()->requerirArroba()->requerirCampo()->requerirCorreoUnico('users', 'email')],
            ])->validate();
        

        // Asignar valores predeterminados
        $defaultValues = [
            'Fecha_Ultima_Conexion' => Carbon::now(),
            'Estado_Usuario' => 'NUEVO',
            'Id_Rol' => 3,
            'Primer_Ingreso' => 1,
            'Fecha_Vencimiento' => Carbon::now()->addMonths(3),
            'Verificacion_Usuario' => false,
            'Intentos_Login' => 0,
        ];

        // Encriptar la contraseña
        $input['Contrasena'] = Hash::make($input['Contrasena']);

        // Crear el usuario con los valores predeterminados
        $user = User::create(array_merge($input, $defaultValues));

        // Disparar el evento de registro
        event(new Registered($user));

        // Enviar notificación de verificación de correo manualmente
        $user->sendEmailVerificationNotification();

        // Registrar en la bitácora
        $this->registrarEnBitacora($user->Id_usuario, 3, 'Creación de un nuevo usuario', 'Nuevo');

        return $user;
    }

    /**
     * Registra un evento en la bitácora.
     *
     * @param  int|null  $Id_usuario
     * @param  int  $ID_objetos
     * @param  string  $descripcion
     * @param  string  $accion
     * @return void
     */
    protected function registrarEnBitacora($Id_usuario, $ID_objetos, $descripcion, $accion)
    {
        Bitacora::create([
            'Id_usuario' => $Id_usuario,
            'Id_Objetos' => 3,
            'Descripcion' => $descripcion,
            'Fecha' => Carbon::now(),
            'Accion' => $accion
        ]);
    }
}
