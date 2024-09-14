<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Rules\Validaciones;
use App\Models\Bitacora;
use Carbon\Carbon;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Valida y actualiza la contrasena del usuario
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        try {
            Validator::make($input, [
                'current_password' => ['required', 'string', 'current_password:web'],
                'Contrasena' => [(new Validaciones)->requerirSinEspacios()->requerirSimbolo()->requerirMinuscula()->requerirMayuscula()->requerirNumero()->requerirlongitudMinima(8)->requerirlongitudMaxima(12)->requerirCampo()],
            ], [
                'current_password.current_password' => __('La contrasena no coincide con nuestros registros'),
            ])->validateWithBag('updatePassword');
        } catch (ValidationException $e) {
            $this->registrarEnBitacora($user, 7, 'Error en la actualización de contraseña: ' . $e->getMessage(), 'Update'); // ID_objetos 7: 'perfil usuario'
            throw $e;
        }

        $user->forceFill([
            'Contrasena' => Hash::make($input['Contrasena']),
        ])->save();

        $this->registrarEnBitacora($user, 7, 'Contraseña actualizada', 'Update'); // ID_objetos 7: 'perfil usuario'
    }

    /**
     * Registra un evento en la bitácora.
     *
     * @param  \App\Models\User  $user
     * @param  int  $ID_objetos
     * @param  string  $descripcion
     * @param  string  $accion
     * @return void
     */
    protected function registrarEnBitacora($user, $ID_objetos, $descripcion, $accion)
    {
        Bitacora::create([
            'Id_usuario' => $user->Id_usuario,
            'ID_objetos' => $ID_objetos,
            'Descripcion' => $descripcion,
            'Fecha' => Carbon::now(),
            'Accion' => $accion
        ]);
    }
}
