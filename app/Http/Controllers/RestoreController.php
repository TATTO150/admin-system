<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\Permisos;

class RestoreController extends Controller
{
    protected $bitacora;

    public function __construct(BitacoraController $bitacora)
    {
        
        $this->bitacora = $bitacora;

    }

    public function index()
    {
        $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de consulta en el objeto SOLICITUD
    $permisoConsultar = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'RESTAURAR')
                ->limit(1);
        })
        ->where('Permiso_Consultar', 'PERMITIDO')
        ->exists();

    if (!$permisoConsultar) {
        $this->bitacora->registrarEnBitacora(9, 'Intento de ingreso a la ventana de restauracion sin permisos', 'Ingreso');
        return redirect()->route('dashboard')->withErrors('No tiene permiso para consultar Restauracion');
    }
    $this->bitacora->registrarEnBitacora(71, 'Ingreso a la ventana de restaurar', 'Ingreso');
        return view('restore.index'); // Renderiza la vista del formulario de restauración
    }

    public function restoreFromBackup()
    {
        // Define el directorio donde están los archivos de respaldo
        $backupDirectory = storage_path('app/backups');
    
        // Busca un archivo que comience con 'admsystemct' y tenga la extensión .sql
        $files = glob($backupDirectory . '/admsystemct*.sql');
    
        // Si no se encuentra ningún archivo que coincida
        if (empty($files)) {
            return back()->with('status', 'No se encontró ningún archivo de respaldo que coincida.');
        }
    
        // Toma el primer archivo que coincida con el patrón
        $filePath = $files[0];
    
        try {
            // Llama al comando Artisan para restaurar la base de datos
            Artisan::call('database:restore', ['filePath' => $filePath]);
    
            // Mensaje de éxito en el log
            $this->bitacora->registrarEnBitacora(9, 'RESTAURACION DE BASE DE DATOS COMPLETADO CORRECTAMNETE', 'RESTARUACIONn');
            Log::info('Restauración de la base de datos completada correctamente.');
    
            // Cierra la sesión del usuario
            Auth::logout();
            $this->bitacora->registrarEnBitacora(71, 'RESTAURACION BASE COMPLETADA', 'RESTAURAR');
            return redirect('/login')->with('success', 'La restauración se completó correctamente. Por favor, vuelve a iniciar sesión.');
    
        } catch (\Exception $e) {
            // Loguea cualquier error en el proceso de restauración
        
            Log::error('Error al restaurar la base de datos: ' . $e->getMessage());
            $this->bitacora->registrarEnBitacora(71, 'ERROR RESTAURACION BASE DE DATOS ', 'RESTAURAR');
            // Redirige de regreso con el mensaje de error
            return back()->with('status', 'Error al restaurar la base de datos: ' . $e->getMessage());
        }
    }
}
