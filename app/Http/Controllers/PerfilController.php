<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Rules\Validaciones;
class PerfilController extends Controller
{
    
    public function editProfile()
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return redirect()->back()->withErrors(['error' => 'Instancia de usuario inválida.']);
        }

        return view('Perfil.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'Correo_Electronico' => 'required|email|unique:tbl_ms_usuario,Correo_Electronico,' . Auth::id() . ',Id_usuario',
            'Usuario' => 'required|string|max:255',
            'Nombre_Usuario' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        if (!$user instanceof User) {
            return redirect()->back()->withErrors(['error' => 'Instancia de usuario inválida.']);
        }

        $user->Correo_Electronico = $request->input('Correo_Electronico');
        $user->Usuario = $request->input('Usuario');
        $user->Nombre_Usuario = $request->input('Nombre_Usuario');

        $user->save();

        return redirect()->route('Perfil.edit')->with('success', 'Perfil actualizado con éxito.');
    }

    public function disableTwoFactorAuthentication()
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return redirect()->back()->withErrors(['error' => 'Instancia de usuario inválida.']);
        }

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;

        $user->save();

        return redirect()->route('Perfil.edit')->with('success', 'Autenticación de dos factores desactivada.');
    }

    public function enable2fa(Request $request)
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return redirect()->back()->withErrors(['error' => 'Instancia de usuario inválida.']);
        }

        // Establecer Verificacion_Usuario a 0
        $user->forceFill([
            'Verificacion_Usuario' => 0,
        ])->save();

        // Redirigir a la vista del autenticador de dos factores
        return redirect()->route('two-factor.authenticator');
    }
    
    Public function updatePassword(Request $request)
    {
        // Validar las entradas
        $request->validate([
            'current_password' => 'required',
            'new_password'  => [
                'required',
                (new Validaciones)->requerirSinEspacios()
                    ->requerirSimbolo()
                    ->requerirMinuscula()
                    ->requerirMayuscula()
                    ->requerirNumero()
                    ->requerirlongitudMinima(8)
                    ->requerirlongitudMaxima(12)
                    ->requerirCampo(),
                'confirmed', // Validación de confirmación de contraseña
            ],
        ], [
            // Mensajes de error personalizados
            'current_password.required' => 'Debe ingresar su contraseña actual.',
            'new_password.required' => 'Debe ingresar una nueva contraseña.',
            'new_password.confirmed' => 'Las contraseñas no coinciden. Asegúrese de que la confirmación sea igual a la nueva contraseña.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.max' => 'La nueva contraseña no debe tener más de 12 caracteres.',
        ], [
            // Traducción de los atributos de los campos
            'new_password' => 'nueva contraseña',
        ]);
    
        // Obtener el usuario autenticado
        $user = Auth::user();
    
        if (!$user instanceof User) {
            return redirect()->back()->withErrors(['error' => 'Instancia de usuario inválida.']);
        }
    
        // Verificar que la contraseña actual es correcta
        if (!Hash::check($request->current_password, $user->Contrasena)) {
            return redirect()->back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }
    
        // Verificar que la nueva contraseña no sea la misma que la actual
        if (Hash::check($request->new_password, $user->Contrasena)) {
            return redirect()->back()->withErrors(['new_password' => 'La nueva contraseña no puede ser la misma que la contraseña actual.']);
        }
    
        // Actualizar la contraseña y establecer la nueva fecha de vencimiento
        $user->Contrasena = Hash::make($request->new_password); // Hashear la nueva contraseña correctamente
        $user->Fecha_Vencimiento = \Carbon\Carbon::now()->addMonths(6); // Nueva fecha de vencimiento a 6 meses
        $user->save();
    
        return redirect()->route('Perfil.edit')->with('success', 'Contraseña actualizada correctamente.');
    }
    

}
