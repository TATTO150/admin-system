<?php

namespace App\Providers;

use App\Models\Permisos;
use Illuminate\Support\Facades\Auth;

class PermisoService
{
    /**
     * Verificar si el usuario autenticado tiene un permiso específico en un objeto.
     *
     * @param string $nombreObjeto El nombre del objeto que se va a consultar (Ej: 'TIPOEMPLEADO').
     * @param string $permiso El tipo de permiso que se va a verificar (Ej: 'Consultar', 'Insercion', 'Actualizacion', 'Eliminacion').
     * @param bool $redirect Si es true, redirige a la ruta anterior si no tiene permiso.
     * @return bool
     */
    public function tienePermiso($nombreObjeto, $permiso, $redirect = false)
    {
        // Verificar si el usuario no está autenticado
        if (!Auth::check()) {
            // Redirigir a la vista `sesion_suspendida`
            return redirect()->route('sesion.suspendida');
        }
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Construimos el campo de permiso, por ejemplo, 'Permiso_Consultar', 'Permiso_Insercion', etc.
        $campoPermiso = 'Permiso_' . ucfirst($permiso);

        // Verificar si el rol del usuario tiene el permiso específico en el objeto dado
        $tienePermiso = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) use ($nombreObjeto) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', $nombreObjeto)
                    ->limit(1);
            })
            ->where($campoPermiso, 'PERMITIDO')
            ->exists();

        // Si no tiene el permiso y se solicita redirección
        if (!$tienePermiso && $redirect) {
            return redirect()->back()
                ->withErrors("No tiene permiso para realizar la acción '$permiso' en la ventana de $nombreObjeto")
                ->send();
        }

        return $tienePermiso;
    }
}
