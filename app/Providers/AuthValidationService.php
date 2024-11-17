<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;

class AuthValidationService
{
    /**
     * Validar si el usuario está autenticado.
     * 
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function validarAutenticacion()
    {
        // Verificar si el usuario no está autenticado
        if (!Auth::check()) {
            // Redirigir a la vista `sesion_suspendida`
            return redirect()->route('sesion.suspendida');
        }
    }
}
