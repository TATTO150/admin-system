<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
}
