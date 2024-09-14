<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Laravel\Fortify\Rules\Validaciones;
use App\Models\Bitacora;
use Carbon\Carbon;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        try {
            Validator::make($input, [
                'Usuario' => [(new Validaciones)->requerirSinEspacios()->requerirTodoMayusculas()->requerirCampo()],
                'Correo_Electronico' => [(new Validaciones)->requerirSinEspacios()->requerirArroba()->requerirCampo()],
                'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            ])->validateWithBag('updateProfileInformation');
        } catch (ValidationException $e) {
            $this->registrarEnBitacora($user, 7, 'Error en la actualización de perfil: ' . $e->getMessage(), 'Update'); // ID_objetos 7: 'perfil usuario'
            throw $e;
        }

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['Correo_Electronico'] !== $user->Correo_Electronico && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'Usuario' => $input['Usuario'],
                'Correo_Electronico' => $input['Correo_Electronico'],
            ])->save();
            $this->registrarEnBitacora($user, 7, 'Usuario actualizado', 'Update'); // ID_objetos 7: 'perfil usuario'
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'Usuario' => $input['Usuario'],
            'Correo_Electronico' => $input['Correo_Electronico'],
            'Verificacion_Correo_Electronico' => false,
        ])->save();

        $user->sendEmailVerificationNotification();
        $this->registrarEnBitacora($user, 7, 'Correo electrónico actualizado', 'Update'); // ID_objetos 7: 'perfil usuario'
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
